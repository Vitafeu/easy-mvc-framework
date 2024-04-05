<?php

namespace Vitafeu\EasyMVC;

use PDO;

class Model {
    private static $conn = null;

    private static $query = '';
    private static $hasWhere = false;

    private static function init() {
        if (self::$conn === null) {
            self::$conn = Database::getConnection();
        }
    }

    // Custom query method

    public static function query($query) {
        self::init();
        $stmt = self::$conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CRUD Methods

    public static function read() {
        if (empty(self::$query)) {
            self::basicSelect();
        }

        self::init();
        $stmt = self::$conn->prepare(self::$query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $objs = [];
        foreach ($result as $row) {
            $model = new static();
            foreach ($row as $key => $value) {
                $model->$key = $value;
            }
            $objs[] = $model;
        }

        return $objs;
    }

    public static function create($data) {
        $calledClass = get_called_class();

        self::init();

        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$calledClass::$table} ($columns) VALUES ($placeholders)";
        $stmt = self::$conn->prepare($sql);

        $stmt->execute(array_values($data));

        return self::$conn->lastInsertId();
    }

    public static function update($id, $data) {
        $calledClass = get_called_class();

        self::init();

        $columns = implode(',', array_map(function ($key) {
            return "{$key} = ?";
        }, array_keys($data)));

        $sql = "UPDATE {$calledClass::$table} SET $columns WHERE id = ?";
        $stmt = self::$conn->prepare($sql);
        $stmt->execute(array_merge(array_values($data), [$id]));

        return $stmt->rowCount();
    }

    public static function delete($id) {
        $calledClass = get_called_class();

        self::init();

        $sql = "DELETE FROM {$calledClass::$table} WHERE id = ?";
        $stmt = self::$conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount();
    }

    // Conditions

    public static function where($condition) {
        if (self::$hasWhere) {
            throw new \Exception('Cannot use multiple WHERE clauses');
        }

        self::$hasWhere = true;
        return self::buildCondition($condition, 'WHERE');
    }

    public static function or($condition) {
        return self::buildCondition($condition, 'OR');
    }

    public static function and($condition) {
        return self::buildCondition($condition, 'AND');
    }

    private static function buildCondition($condition, $operator) {
        if (empty(self::$query)) {
            self::basicSelect();
        }

        if (!empty($condition)) {
            self::$query .= " $operator $condition";
        }

        return new static();
    }

    // Order

    public static function orderBy($columns, $direction = 'ASC') {
        if (empty(self::$query)) {
            self::basicSelect();
        }

        if (!empty($columns)) {
            self::$query .= ' ORDER BY ' . implode(',', $columns) . ' ' . $direction;
        }

        return new static();
    }

    // Limit

    public static function limit($limit) {
        if (empty(self::$query)) {
            self::basicSelect();
        }

        if (!empty($limit)) {
            self::$query .= ' LIMIT ' . $limit;
        }

        return new static();
    }

    private static function basicSelect() {
        $calledClass = get_called_class();

        $columns = implode(',', array_keys($calledClass::$attributes));

        self::$query = "SELECT $columns FROM {$calledClass::$table}";
    }
}