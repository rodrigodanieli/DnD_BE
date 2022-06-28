<?php

namespace App\Repository;

use App\System\DatabaseAsync;

use function React\Promise\all;
use function React\Promise\resolve;

class Repository extends DatabaseAsync
{

    protected $table = '';
    protected $pk = 'id';
    protected $choices = false;
    protected $relational_map = false;

    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table;
        return $this->runQuery($query);
    }

    public function get($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE `" . $this->pk . "` = ?";

        return $this->runQuery($query, [$id])->then(function ($result) use ($id) {
            $result = sizeof($result) > 0 ? $result[0] : $result;
            $queries = [];
            if ($this->choices)
                $queries[] = $this->getChoices($id)->then(function ($choices) use (&$result) {
                    if (!empty($choices)) $result['choices'] = $choices;
                });
            if ($this->relational_map)
                $queries[] = $this->getRelationalMap($id)->then(function ($rel) use (&$result) {
                    if (!empty($rel)) $result = array_merge($rel, $result);
                });

            return all($queries)->then(function () use (&$result) {
                return $result;
            });
        });
    }

    public function where($field, $value)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE `$field` = ?";
        return $this->runQuery($query, [$value]);
    }

    public function new(array $params)
    {
        $choices = [];
        $relational_map = [];

        if ($this->choices && array_key_exists('choices', $params)) {
            $choices = $params['choices'];
            unset($params['choices']);
        }
        if ($this->relational_map) {
            $relational_map = [
                "rpg_id" => $params['rpg_id'],
                "expansion_id" => array_key_exists('expansion_id', $params) ? $params['expansion_id'] : NULL
            ];
            unset($params['rpg_id']);
            unset($params['expansion_id']);
        }

        $keys = array_keys($params);
        $deattach_params = array_map(fn ($param) => $param, $params);
        $number_of_arguments = count($params);


        $query = "INSERT INTO " . $this->table . " (" . implode(',', $keys) . ")  VALUES (";
        while ($number_of_arguments--) {
            $query .= $number_of_arguments >= 0 ? "?" : "";
            $query .= $number_of_arguments > 0 ? "," : ");";
        }
        return $this->runQuery($query, $deattach_params)->then(function ($id) use ($choices, $relational_map) {
            if (!$id)
                return false;
            if (!empty($relational_map)) {
                $exp = null;
                if (!empty($relational_map['expansion_id']) && is_numeric($relational_map['expansion_id']))
                    $exp = $relational_map['expansion_id'];
                $this->newRelationMap($id, $relational_map['rpg_id'], $exp);
            }
            if (!empty($choices)) {
                foreach ($choices as $choice) {
                    $this->newChoice($id, $choice['type'], $choice['number'], $choice['options']);
                }
            }

            return $id;
        });
    }

    public function update($id, $field, $value)
    {
        if (empty($id))
            return false;
        $query = "UPDATE " . $this->table . " SET `" . $field . "`=? WHERE `" . $this->pk . "`=?";

        return $this->runQuery($query, [$value, $id]);
    }

    public function delete($id)
    {
        $queries = [];
        $queries[] = $this->runQuery("DELETE FROM " . $this->table . " WHERE `" . $this->pk . "` = ?;", [$id]);
        if ($this->choices)
            $queries[] = $this->runQuery("DELETE FROM game_choices WHERE `ref_type` = ? AND `ref_id` = ?", [$this->table, $id]);
        if ($this->relational_map)
            $queries[] = $this->runQuery("DELETE FROM rpg_objects_map WHERE `object_type` = ? AND `ref_id` = ?", [$this->table, $id]);

        return all($queries)->then(fn ($resolve) => true);
    }

    public function getChoices($id)
    {
        if (!$this->choices)
            return resolve([]);
        $query = "SELECT `type`, `choose_number`, `options` FROM game_choices WHERE `ref_type` = ? AND `ref_id` = ?";
        return $this->runQuery($query, [$this->table, $id])->then(fn ($result) => $result);
    }

    public function getRelationalMap($id)
    {
        if (!$this->relational_map)
            return resolve([]);
        $query = "SELECT gc.rpg_id, rpg.name as rpg_name, re.name as rpg_edition, re2.id as rpg_expansion_id, re2.name as rpg_expansion_name FROM rpg_objects_map gc 
        JOIN rpg_editions re ON gc.rpg_id = re.id 
        JOIN rpgs rpg ON rpg.id = re.rpg_id 
        LEFT JOIN rpg_expansions re2 on gc.rpg_expansion = re2.id WHERE gc.object_type = ? AND gc.ref_id = ?";
        return $this->runQuery($query, [$this->table, $id])->then(fn ($result) => sizeof($result) > 0 ? $result[0] : $result);
    }

    private function newChoice(int $id, string $type, int $choose_number, array $options)
    {
        $query = "INSERT INTO game_choices VALUES (?,?,?,?,?);";
        return $this->runQuery($query, [
            $id,
            $this->table,
            $type,
            $choose_number,
            json_encode($options)
        ]);
    }

    private function newRelationMap(int $id, int $rpg, $expantion)
    {
        $query = "INSERT INTO rpg_objects_map VALUES (?,?,?,?);";
        return $this->runQuery($query, [
            $rpg,
            $expantion,
            $this->table,
            $id
        ]);
    }
}
