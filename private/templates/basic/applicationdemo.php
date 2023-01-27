<?php

require_once 'component.php';

class Applicationdemo {

    function __construct() {

        $this->host = 'http://127.0.0.1:8000';

        $this->path = '';

        $this->demo = 'demo_' . $this->path . '__';

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

        unset($_SESSION["$this->demo$table"]);

    }

    function migrate($table, $form_fields) {

        if (!isset($_SESSION["$this->demo$table"])) {

            $_SESSION["$this->demo$table"] = [];

        }

    }

    function insert($formats, $table, $fields) {

        $date = date('Y-m-d H:i:s');

        $fields[$table . '_created_at'] = $date;

        $fields[$table . '_updated_at'] = $date;

        if (!isset($fields["$table". "_id"])) {
        
            $last_key = array_key_last($_SESSION["$this->demo$table"]);

            if ($last_key === null) {

                $fields["$table". "_id"] = 1;
                
            } else {

                $fields["$table". "_id"] = $last_key + 1;

            }

           

        }

        $_SESSION["$this->demo$table"][$fields["$table". "_id"]] =  $fields;

        //array_push($_SESSION["$this->demo$table"], $fields);

    }

    function update($formats, $table, $fields, $id) {

        foreach(array_keys($fields) as $field_key) {

            $_SESSION["$this->demo$table"][$id][$field_key] = $fields[$field_key];

        }

        $date = date('Y-m-d H:i:s');

        $_SESSION["$this->demo$table"][$id][$table . '_updated_at'] = $date;


    }

    function table_exists($table) {

        return isset($_SESSION["$this->demo$table"]);

    }

    function select($table, $joins = [], $filters = [], $selects = [], $where_like = false, $offset = 0) {


        foreach($joins as $join) {

            $join_tags = explode(' on ', $join);

            $join_table = trim(str_replace('join', '', $join_tags[0]));
            
            $join_on_tags = explode(' = ', $join_tags[1]);
            
            $join_table_id = explode('.', $join_on_tags[0])[1];

            $table_id = explode('.', $join_on_tags[1])[1];

            foreach($_SESSION["$this->demo$table"] as $key => $row) {
                
                foreach($_SESSION["$this->demo$join_table"] as $join_row) {

                    if ($join_row[$join_table_id] == $row[$table_id]) {
                        
                        foreach(array_keys($join_row) as $join_field) {

                            $_SESSION["$this->demo$table"][$key][$join_field] = $join_row[$join_field];
                        
                        }

                        
                    }

                }

            }

            

        }


        $row_filtered = [];

        foreach($_SESSION["$this->demo$table"] as $key => $row) {

            $to_insert = true;

            foreach($filters as $filter_name => $filter_value) {

                if ($filter_value) {

                    if (isset($row[$filter_name]) && false !== strpos($row[$filter_name], $filter_value)) {
                        
                        if ($where_like) {
        
                            $to_insert = true;
                            
                        } else {

                            if ($filter_value === $row[$filter_name]) {

                                $to_insert = true;

                            } else {

                                $to_insert = false;

                            }

                        }

                        

                    } else {

                        $to_insert = false;
                    }

                }

            }

            if ($to_insert) {

                array_push($row_filtered, $row);

            }

        }

        $row_filtered = array_slice($row_filtered, $offset, 4);


        /*
        foreach($left_joins as $left_join) {

            $left_join_tags = explode(' on ', $left_join);

            $left_join_table = $left_join_tags[0];
            
            $left_join_on_tags = explode(' = ', $left_join_tags[1]);
            
            $left_join_table_id = explode('.', $left_join_on_tags[0])[1];

            $table_id = explode('.', $left_join_on_tags[1])[1];

            foreach($_SESSION["$this->demo$table"] as $key => $row) {
                
                foreach($_SESSION["$this->demo$left_join_table"] as $left_join_row) {

                    //if ($left_join_row[$join_table_id] == $row[$table_id]) {
                        
                        foreach(array_keys($left_join_row) as $left_join_field) {

                            $_SESSION["$this->demo$table"][$key][$left_join_field] = $left_join_row[$left_join_field];
                        
                        }

                        
                    //}

                }

            }

            

        }
        */

        return $row_filtered;

    }


    function find($table, $id) {

        return $_SESSION["$this->demo$table"][$id];

    }
 
    function delete($table, $id) {

        unset($_SESSION["$this->demo$table"][$id]);

    }

    function delete_where($table, $filters) {

        $row_filtered = [];

        foreach($_SESSION["$this->demo$table"] as $key => $row) {

            $to_insert = false;

            foreach($filters as $filter_name => $filter_value) {

                if (isset($row[$filter_name]) && null !== strpos($filter_value, $row[$filter_name])) {

                    if ($where_like) {

                        $to_insert = false;
                        
                    } else {

                        if ($filter_value === $row[$filter_name]) {

                            $to_insert = false;

                        } else {

                            $to_insert = true;

                        }

                    }

                    

                } else {

                    $to_insert = true;
                }

            }

            if ($to_insert) {

                array_push($row_filtered, $row);

            }

        }

        $_SESSION["$this->demo$table"] = $row_filtered;


    }
    
}