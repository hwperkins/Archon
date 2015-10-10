<?php namespace Archon\Database;

use PDO;
use PDOException;
use MSHA\Models\RelationalModel;
use RuntimeException;

class PDODriver extends PDO {

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function schema_exists($schema) {
        $result = $this->query("SELECT schema_name FROM information_schema.schemata WHERE schema_name = '{$schema}';");
        foreach($result as $row) {
            if ($row['schema_name'] == $schema) return TRUE;
        }

        return FALSE;
    }

    public function has_column($schema, $table, $column) {
        $result = $this->query("SHOW COLUMNS FROM {$schema}.{$table} WHERE field = '{$column}';");
        return sizeof($result) > 0;
    }

    public function exec($sql) {
        $db = $this->db;
        $affected_rows = 0;
        try {
            $affected_rows = $db->exec($sql);
        } catch(PDOException $e) {
            echo $sql."</br>".$e->getMessage();
        }
        return $affected_rows;
    }

    public function query($sql, $fetch_type=PDO::FETCH_ASSOC) {
        $db = $this->db;
        try {
            $statement = $db->query($sql);
            $result = $statement->fetchAll($fetch_type);
        } catch(PDOException $e) {
            $result = NULL;
            echo $sql."</br>".$e->getMessage();
        }
        return $result;
    }

    public function escape_string($string) {
        $db = $this->db;
        return $db->quote($string);
    }

    public function prepare($sql, array $parameters) {
        $db = $this->db;
        $statement = $db->prepare($sql);
        $statement->execute($parameters);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function close() {
        $this->db = NULL;
    }
}