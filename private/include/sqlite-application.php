<?php

class Application {

    function __construct($configuration) {

        $this->configuration = $configuration;

        $this->db = new SQLite3($this->configuration->db_name . '.dbi');


    }

    function begin_transaction() {

        $this->db->begin_transaction();

    }

    function commit() {

        $this->db->commit();

    }

    function rollback() {

        $this->db->rollback();

    }


    function get_templates() {

        $stmt = $this->db->prepare($this->configuration->get_templates_query);

        $result = $stmt->execute();

        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            array_push($rows, $row);
        }
        
        $result->finalize();

        return $rows;

    }

    function get_template($template_id) {

        $stmt = $this->db->prepare($this->configuration->get_template_query);

        $stmt->bindParam(1, $template_id, SQLITE3_INTEGER); 

        $result = $stmt->execute();

        $row = $result->fetchArray(SQLITE3_ASSOC);

        return $row;

    }

    function get_demos($session_id) {

        $stmt = $this->db->prepare($this->configuration->get_demos_query);

        $stmt->bindParam(1, $session_id, SQLITE3_INTEGER); 

        $result = $stmt->execute();

        $rows = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            array_push($rows, $row);
        }

        return $rows;

    }

    function get_demo($demo_id) {

        $stmt = $this->db->prepare($this->configuration->get_demo_query);

        $stmt->bindParam(1, $demo_id, SQLITE3_INTEGER); 

        $result = $stmt->execute();

        $row = $result->fetchArray(SQLITE3_ASSOC);

        return $row;

    }

    function insert_demo($name, $params, $schema, $session_id, $template_id) {

        $stmt = $this->db->prepare($this->configuration->insert_demo_query);

        $date = date('Y-m-d H:i:s');

        $stmt->bindParam(1, $name, SQLITE3_TEXT); 
        
        $stmt->bindParam(2, $params, SQLITE3_TEXT); 
        
        $stmt->bindParam(3, $schema, SQLITE3_TEXT); 
        
        $stmt->bindParam(4, $date, SQLITE3_TEXT); 
        
        $stmt->bindParam(5, $date, SQLITE3_TEXT); 
        
        $stmt->bindParam(6, $session_id, SQLITE3_TEXT); 
        
        $stmt->bindParam(7, $template_id, SQLITE3_INTEGER); 

        $stmt->execute();

        return $this->db->lastInsertRowID();

    }

    function update_demo($name, $params, $schema, $demo_id) {

        $stmt = $this->db->prepare($this->configuration->update_demo_query);

        $date = date('Y-m-d H:i:s');
        
        $stmt->bindParam(1, $name, SQLITE3_TEXT);
        
        $stmt->bindParam(2, $params, SQLITE3_TEXT);
        
        $stmt->bindParam(3, $schema, SQLITE3_TEXT);
        
        $stmt->bindParam(4, $date, SQLITE3_TEXT);
        
        $stmt->bindParam(5, $demo_id, SQLITE3_INTEGER);
       

        $stmt->execute();

    }

    function delete_demo($demo_id) {

        $stmt = $this->db->prepare($this->configuration->delete_demo_query);

        $stmt->bindParam(1, $demo_id, SQLITE3_INTEGER);

        $stmt->execute();

    }

    


}