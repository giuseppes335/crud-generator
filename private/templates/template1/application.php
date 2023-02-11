<?php

require_once 'relation.php';

class Application extends Relation {

    function __construct(Array $params) {
        
        $server_protocol = 'http';
        
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL']) {

            if ('HTTP/1.1' === $_SERVER['SERVER_PROTOCOL']) {

                $server_protocol = 'http';

            } else if ('HTTPS/1.1' === $_SERVER['SERVER_PROTOCOL']) {

                $server_protocol = 'https';

            }

        }

        $server_name = '127.0.0.1';
        
        if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {

            $server_name = $_SERVER['SERVER_NAME'];

        }

        $server_port = '8000';
        
        if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']) {

            $server_port = $_SERVER['SERVER_PORT'];

        }
        
        $this->host = "$server_protocol://$server_name:$server_port";

        $path = 'private/demos/default';

        if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME']) {
            
            $script_name_parts = explode('/', $_SERVER['SCRIPT_NAME']);
            
            if (count($script_name_parts) > 1) {

                // Remove empty part at 0;
                array_shift($script_name_parts);

                // Remove last element (file name)
                array_pop($script_name_parts);
 
                $path = implode('/', $script_name_parts);

            } else {

                $path = '';

            }

        }

        $this->path = $path;

        $script_name = 'private/demos/default/index.php';

        if (isset($_SERVER['SCRIPT_NAME']) && $_SERVER['SCRIPT_NAME']) {
            
            $script_name = $_SERVER['SCRIPT_NAME'];

        }

        $this->script_name = $script_name;


        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->mysqli = new mysqli(
            $params['database_host'], 
            $params['database_username'], 
            $params['database_password'],
            $params['database_name']
        );

    }

    function get() {

    }

    function post() {

    }

    function put() {

    }

    function delete() {
        
    }

    function drop($table) {

        $query = "drop table if exists $table";

        $this->mysqli->query($query);

    }

    function migrate($table, $form_fields) {

        $table_definitions = [];

        $fks_definitions = [];
        
        foreach($form_fields as $index => $field) {
            
            if (!$field->nullable) {
                $field->nullable = '';
            }

            array_push($table_definitions, "$field->name $field->mysql_type $field->nullable");

            if (isset($field->dataset_table)) {
                 
                $now = str_replace('-', '_', $index);

                array_push($fks_definitions, "constraint fk_$now" . "_$table" . "_to_$field->dataset_table foreign key($field->name) references $field->dataset_table($field->dataset_id) on delete cascade");

            }
        }

        $fks = '';

        if (count($fks_definitions) > 0) {

            $fks = ', ' . implode(', ', $fks_definitions);

        }
        
        $query = "create table if not exists $table ($table" . "_id bigint unsigned auto_increment primary key, " . implode(', ', $table_definitions) .", " . $table . "_creator bigint unsigned null, " . $table . "_created_at timestamp default CURRENT_TIMESTAMP, " . $table . "_updated_at timestamp default CURRENT_TIMESTAMP $fks);";
        echo "$query<br><br>";
        $this->mysqli->query($query);

    }

    function insert($formats, $table, $fields, $creator_id = null) {

        $date = date('Y-m-d H:i:s');

        $fields[$table . '_creator'] = $creator_id;

        $fields[$table . '_created_at'] = $date;

        $fields[$table . '_updated_at'] = $date;

        $keys = array_keys($fields);
        
        $query = "insert into $table (" . implode(', ', $keys) . ") values (" . implode(', ', array_fill(0, count($keys), '?')) . ");";

        $stmt = $this->mysqli->prepare($query);

        $stmt->bind_param($formats . 'iss', ...array_values($fields)); 

        $stmt->execute();

        return $this->mysqli->insert_id;

    }

    function update($formats, $table, $fields, $id) {

        $date = date('Y-m-d H:i:s');

        $fields[$table . '_updated_at'] = $date;

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

    function select($table, $joins = [], $filters = [], $selects = [], $where_like = false, $offset = 0, $creators = [], $limit = 1000) {
 
        $query = "select * from $table";

        if (count($selects) > 0) {

            $select_part = implode(', ', $selects);

            $query = "select $select_part from $table";

        }

        foreach($joins as $join) {

            $query .= " $join";

        }
        
        if (count($selects) > 0) {
            
            $query = "select * from ($query) as t";
            
        }

        
        $formats = '';
        
        $values = [];
        
        $wheres = [];


        
        foreach($filters as $field => $ov) {
            
            $formats .= 's';
            
            $value = $ov['value'];
            
            array_push($values, $value);
      
            if ($ov['op'] === 'eq') {
                
                $operator = '=';
                
            } else if ($ov['op'] === 'like') {
                
                $operator = 'like';
                
            } else if ($ov['op'] === 'lt') {
                
                $operator = '<';
                
            } else if ($ov['op'] === 'gt') {
                
                $operator = '>';
                
            } else if ($ov['op'] === 'let') {
                
                $operator = '<=';
                
            } else if ($ov['op'] === 'get') {
                
                $operator = '>=';
                
            }
            
            $query_part = "$field $operator ?";
            
            array_push($wheres, $query_part);
            
        }

        $where_conditions = implode(' and ', $wheres);

        if (count($wheres) > 0) {

            $query .= " where $where_conditions";

        } else {

            $query .= " where 1";

        }

        if (count($creators) > 0) {

            $cr_ids = implode(', ', $creators);

            $query .= " and $table" . "_creator " . "in ($cr_ids)";

        }

        $query .= " order by $table" . "_id";

        $query .= " limit $offset, $limit";

        $stmt = $this->mysqli->prepare($query);
        
        if (count($wheres) > 0) {
        
            $stmt->bind_param($formats, ...array_values($values));
            
        }
        
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
 
    function delete_record($table, $id) {

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

    function login($users_table, $username_field, $password_field, $username_value, $password_value) {

        $stmt = $this->mysqli->prepare("select * from $users_table where $username_field = ?");

        $stmt->bind_param('s', $username_value); 
    
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        $row = $result->fetch_assoc();
    
        $id = null;

        if ($row && password_verify($password_value, $row[$password_field])) {
            $id = $row["$users_table" . "_id"];
        }
    
        return $id;

    }
    
}