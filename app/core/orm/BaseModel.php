<?php

abstract class BaseModel{

    protected $tableName;
    protected $hidden = [];
    protected $queryBuilder;

    public function __construct(){
        $this->tableName = $this->uncamelize($this->table ?? static::class) . 's';
        $this->queryBuilder = new QueryBuilder();
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
        return $this->queryBuilder->insert($this->tableName, $fields)->create();
    }

    public function all(){
        return $this->queryBuilder->select($this->tableName)->create();
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