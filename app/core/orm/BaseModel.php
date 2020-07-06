<?php

abstract class BaseModel extends QueryBuilder {

    protected $tableName;
    protected $hidden = [];

    public function __construct(){
        $this->tableName = $this->uncamelize($this->table ?? static::class) . 's';
    }

    public function update($fields = [], int $id){
        $conn = Connection::getConn();
        $query =  "UPDATE $this->tableName SET ";
        foreach ($fields as $key => $value){
            $query .= "{$this->escape($key)} = :$key,";
        }
        $query = rtrim($query, ',');
        $query .= ' WHERE id = :id';
        $query = str_replace('#', $this->organizeColumns(array_keys($fields)), $query);

        $stmt = $conn->prepare($query);

        foreach ($fields as $key => $value){
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('id', $id);

        $stmt->execute();

        if ($stmt->rowCount() > 0){
            return $fields;
        }

        return $stmt->errorInfo();
    }

    public function delete(int $id){
        $query = "DELETE FROM $this->tableName WHERE id = ?";
        $conn = Connection::getConn();

        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            return true;
        }

        throw new Exception('Not found', 404);
    }

    public function find(int $id){
        $query = "SELECT * FROM $this->tableName WHERE id = ?";
        $conn = Connection::getConn();

        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            $object =  $stmt->fetchObject();
            return $this->response($object);
        }

        throw new Exception('Not found', 404);
    }

    public function create($fields = []){
        $conn = Connection::getConn();

        $query =  "INSERT INTO $this->tableName (#) VALUES (";

        foreach ($fields as $key => $value){
            $query .= ":$key,";
        }

        $query = rtrim($query, ',') . ');';
        $query = str_replace('#', $this->organizeColumns(array_keys($fields)), $query);

        $stmt = $conn->prepare($query);

        foreach ($fields as $key => $value){
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        if ($stmt->rowCount() > 0){
            $id = $conn->lastInsertId();

            $stmt = $conn->prepare("SELECT * FROM $this->tableName WHERE id = $id");
            $stmt->execute();

            return $stmt->fetchObject(PDO::FETCH_OBJ);
        }
        return $stmt->errorInfo();
    }

    public function all(){
        $query = "SELECT * FROM $this->tableName";
        $conn = Connection::getConn();

        $stmt = $conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            $objects = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $this->response($objects);
        }

        throw new Exception('Not found', 404);
    }

    private function uncamelize($camel,$splitter="_") {
        $camel = preg_replace('/(?!^)[[:upper:]][[:lower:]]/', '$0', preg_replace('/(?!^)[[:upper:]]+/', $splitter.'$0', $camel));
        return strtolower($camel);
    }

    private function response($object){
        foreach ($this->hidden as $key){
            if (is_array($object)){
                foreach ($object as $value){
                    unset($value->$key);
                }
            }
            unset($object->$key);
        }
        return $object;
    }
}