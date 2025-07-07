<?php

namespace app\models;

use Flight;
use PDO;
use Exception; 

class AppModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    public function getAll($table_name)
    {
        $stmt = $this->db->query("SELECT * FROM {$table_name} ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($table_name, $columnName, $id)
    {
        $sql = "SELECT * FROM {$table_name} WHERE {$columnName} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => intval($id)]);

        return $stmt->fetch();
    }
    public function deleteById($table_name, $id)
    {
        $sql = "DELETE FROM {$table_name} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => intval($id)]);

        return $stmt->rowCount() > 0;
    }

    public function getColumns($table_name)
    {
        $stmt = $this->db->query("SELECT column_name
        FROM information_schema.columns
        WHERE table_schema = 'public' 
        AND table_name = '{$table_name}'");
        return $stmt->fetchAll();
    }

    public function insert($table_name, $data)
    {
        $columns = $this->getColumns($table_name);

        $insertable = array_intersect_key($data, array_flip(array_column($columns, 'column_name')));

        $fields = implode(', ', array_keys($insertable));
        $placeholders = ':' . implode(', :', array_keys($insertable));

        $sql = "INSERT INTO {$table_name} ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($insertable);
    }

    public function update($table_name, $data, $id_column_name = 'id')
    {
        $columns = $this->getColumns($table_name);

        if (!isset($data['id'])) {
            throw new \Exception("L'ID est requis pour mettre à jour un enregistrement.");
        }
        $setClause = [];
        $params = [];

        foreach ($data as $key => $value) {
            if ($key !== 'id' && in_array($key, array_column($columns, 'column_name')) && $value !== null && $value !== '') {
                $setClause[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }
        if (empty($setClause)) {
            return false;
        }

        $params['id'] = intval($data['id']);
        $setStr = implode(', ', $setClause);
        $sql = "UPDATE {$table_name} SET {$setStr} WHERE {$id_column_name} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete($table_name,$id,$id_column_name='id')
    {
        $sql = "DELETE FROM {$table_name} WHERE {$id_column_name} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => intval($id)]);
        return $stmt->rowCount() > 0;
    }

    public function disableForeignKeysCheck()
    {
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 0");
    }

    public function enableForeignKeysCheck()
    {
        $this->db->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

 

}
