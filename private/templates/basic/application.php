<?php

require_once 'component.php';

class Application {

    function __construct($request) {

        print_r($_SERVER);

        $this->host = 'http://127.0.0.1:8000';

        $this->database = 'versioni-demo';

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->mysqli = new mysqli(
                            '127.0.0.1', 
                            'laravel', 
                            'laravel',
                            $this->database
                        );

        $this->request = $request;

        $this->path = 'private/demos/' . $this->request->demo_id;

        $this->demo = 'demo_' . $this->request->demo_id . '__';

    }


    function reset() {

    }

    function bootstrap () {
        
    }

    function get() {

    }

    function post() {
        
    }

    function drop($table) {

        $query = "drop table if exists $table";

        $this->mysqli->query($query);

    }

    function migrate($table, $form_fields) {

        $table_definitions = [];

        $fks_definitions = [];

        foreach($form_fields as $field) {

            if (!$field->nullable) {
                $field->nullable = '';
            }

            array_push($table_definitions, "$field->name $field->mysql_type $field->nullable");

            if (isset($field->dataset_table)) {

                $now = time();

                array_push($fks_definitions, "constraint fk_$now" . "_$table" . "_to_$field->dataset_table foreign key($field->name) references $field->dataset_table($field->dataset_id) on delete cascade");

            }
        }

        $fks = '';

        if (count($fks_definitions) > 0) {

            $fks = ', ' . implode(', ', $fks_definitions);

        }

        $query = "create table if not exists $table ($table" . "_id bigint unsigned auto_increment primary key, " . implode(', ', $table_definitions) .", created_at timestamp, updated_at timestamp $fks);";

        $this->mysqli->query($query);

    }

    function insert($formats, $table, $fields) {

        $date = date('Y-m-d H:i:s');

        $fields['created_at'] = $date;

        $fields['updated_at'] = $date;

        $keys = array_keys($fields);

        $query = "insert into $table (" . implode(', ', $keys) . ") values (" . implode(', ', array_fill(0, count($keys), '?')) . ");";

        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param($formats . 'ss', ...array_values($fields)); 

        $stmt->execute();

        return $this->mysqli->insert_id;

    }

    function update($formats, $table, $fields, $id) {

        $date = date('Y-m-d H:i:s');

        $fields['updated_at'] = $date;

        $update_statements = [];

        foreach($fields as $field => $value) {

            array_push($update_statements, "$field = ?");

        }

        $query = "update $table set " . implode(', ', $update_statements) . " where $table" . "_id = $id";

        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param($formats . 's', ...array_values($fields)); 

        $stmt->execute(); 

    }

    function table_exists($table) {

        $query = "select * from information_schema.tables where table_schema = '$this->database' and table_name ='$table' limit 1";

        $stmt = $this->mysqli->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        return $row;

    }
    

    function similar($columns, $table, $joins = [], $filters = [], $where_like = false) {

        

        $query = "select * from $table";

        foreach($joins as $join) {

            $query .= " $join";

        }

        $select_part = implode(', ', $columns);

        $query .= " where ($select_part) in (select $select_part from $table";

        foreach($joins as $join) {

            $query .= " $join";

        }

        $wheres = [];

        foreach($filters as $field => $value) {

            if ($where_like) {

                array_push($wheres, "$field like '%$value%'");

            } else {

                array_push($wheres, "$field = '$value'");

            }
            

        }

        $where_conditions = implode(' and ', $wheres);

        if (count($wheres) > 0) {

            $query .= " where $where_conditions";

        }

        $query .= ')';

        $query .= " order by $table" . "_id";

        $stmt = $this->mysqli->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($rows, $row);
        }

        return $rows;

    }

    function select($table, $joins = [], $filters = [], $selects = [], $where_like = false, $offset = 0) {
 
        $query = "select * from $table";

        if (count($selects) > 0) {

            $select_part = implode(', ', $selects);

            $query = "select $select_part from $table";

        }

        foreach($joins as $join) {

            $query .= " $join";

        }

        $wheres = [];

        foreach($filters as $field => $value) {

            if ($where_like) {

                array_push($wheres, "$field like '%$value%'");

            } else {

                array_push($wheres, "$field = '$value'");

            }
            

        }

        $where_conditions = implode(' and ', $wheres);

        if (count($wheres) > 0) {

            $query .= " where $where_conditions";

        }

        $query .= " order by $table" . "_id";

        $query .= " limit $offset, 10";
        
        $stmt = $this->mysqli->prepare($query);

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($rows, $row);
        }

        return $rows;

    }


    function find($table, $id) {

        $query = "select * from $table where $table" . "_id = ?";

        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param('i', $id); 

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        return $row;

    }
 
    function delete($table, $id) {

        $query = "delete from $table where $table" . "_id = ?";

        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param('i', $id); 

        $stmt->execute();

    }

    function delete_where($table, $filters) {

        $query = "delete from $table";

        $wheres = [];

        foreach($filters as $field => $value) {

            array_push($wheres, "$field = '$value'");

        }

        $where_conditions = implode(' and ', $wheres);

        if (count($wheres) > 0) {

            $query .= " where $where_conditions";

        }

        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param('i', $id); 

        $stmt->execute();

    }

    function referenced_tables($table) {

        $query = "select table_name, referenced_table_name from information_schema.key_column_usage where referenced_table_schema is not null and table_schema = 'versioni-demo' and referenced_table_name = ?";

        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param('s', $table); 

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = [];

        while ($row = $result->fetch_assoc()) {

            array_push($rows, $row);

        }

        return $rows;

    }
    
}