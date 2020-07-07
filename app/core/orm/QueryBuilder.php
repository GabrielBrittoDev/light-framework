<?php


class QueryBuilder
{

    private $query;
    private $values;
    private $returnFunction;

    public function escape($data){
            if ( !isset($data) || empty($data)) return '';
            if (is_numeric($data)) return $data;

            $non_displayables = array(
                '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
                '/%1[0-9a-f]/',             // url encoded 16-31
                '/[\x00-\x08]/',            // 00-08
                '/\x0b/',                   // 11
                '/\x0c/',                   // 12
                '/[\x0e-\x1f]/'             // 14-31
            );
            foreach ( $non_displayables as $regex )
                $data = preg_replace( $regex, '', $data );
            $data = str_replace("'", "''", $data );
            return $data;
    }


    public function select($table){
        $query = 'SELECT * FROM ' . $this->escape($table);
        $this->query = $query;

        $this->returnFunction = function ($conn, $stmt){
            if ($stmt->rowCount() > 0){
                $objects = $stmt->fetchAll(PDO::FETCH_OBJ);
                return $objects;
            }

            throw new Exception('Not found', 404);
        };
        return $this;
    }

    public function and($field, $operator){
        $query = '';

        $query .= ' AND ' . $this->escape($field) . ' ' . $this->escape($operator) . ' :' . $this->escape($field);

        $this->query .= $query;
        return $this;
    }

    public function or($field, $operator){
        $query = '';

        $query .= ' OR ' . $this->escape($field) . ' ' . $this->escape($operator) . ' :' . $this->escape($field);

        $this->query .= $query;
        return $this;
    }

    public function where($field, $operator){
        $query = '';

        if (!strpos($this->query, 'WHERE')) $query .= ' WHERE ';

        $query .= ' ' . $this->escape($field) . ' ' . $this->escape($operator) . ' :' . $this->escape($field);

        $this->query .= $query;
        return $this;
    }

    public function insert($table, $values)
    {
        $query = 'INSERT INTO ' . $this->escape($table) . '(#) VALUES (';
        $valuesEscaped = [];
        foreach ($values as $key => $value){
            $valuesEscaped[$this->escape($key)] = $value;
        }

        $query = str_replace('#', implode(',', array_keys($valuesEscaped)), $query);

        foreach ($valuesEscaped as $key => $value){
            $query .= ":$key,";
        }

        $query = rtrim($query, ',') . ');';

        $this->values = $valuesEscaped;
        $this->query = $query;

        $this->returnFunction = function ($conn, $stmt) use ($table){
            if ($stmt->rowCount() > 0){
                $id = $conn->lastInsertId();

                $stmt = $conn->prepare("SELECT * FROM $table WHERE id = $id");
                $stmt->execute();

                return $stmt->fetchObject();
            }

            return $stmt->errorInfo();
        };

        return $this;
    }

    public function create(){
        $conn = Connection::getConn();

        $stmt = $conn->prepare($this->query);

        if (!empty($this->values)){
            foreach ($this->values as $key => $value){
                $stmt->bindValue($key, $value);
            }
        }


        $stmt->execute();
        $this->values = [];
        $this->query = '';

        return ($this->returnFunction)($conn, $stmt);
    }
}