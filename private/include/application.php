<?php

class Application {

    function __construct($configuration) {

        $this->configuration = $configuration;

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->mysqli = new mysqli(
                            $this->configuration->db_host, 
                            $this->configuration->db_username, 
                            $this->configuration->db_password,
                            $this->configuration->db_name
                        );


    }

    function begin_transaction() {

        $this->mysqli->begin_transaction();

    }

    function commit() {

        $this->mysqli->commit();

    }

    function rollback() {

        $this->mysqli->rollback();

    }

    function insert_user($email, $password) {

        $stmt = $this->mysqli->prepare($this->configuration->insert_user_query);

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $date = date('Y-m-d H:i:s');

        $stmt->bind_param('ssss', $email, $hash, $date, $date); 

        $stmt->execute();

    }


    function user_exists($email, $password) {

        $stmt = $this->mysqli->prepare($this->configuration->get_user_query);

        $stmt->bind_param('s', $email); 

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        $id = null;
        if (password_verify($password, $row['password'])) {
            $id = $row['id'];
        }

        return $id;

    }


    
    function insert_activation($description, $credits, $receipt_number, $user_id) {

        $stmt = $this->mysqli->prepare($this->configuration->insert_activation_query);

        $date = date('Y-m-d H:i:s');

        $stmt->bind_param('sisssi', $description, $credits, $receipt_number, $date, $date, $user_id); 

        $stmt->execute();

    }

    function get_activations($user_id) {

        $stmt = $this->mysqli->prepare($this->configuration->get_activations_query);

        $stmt->bind_param('i', $user_id); 

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($rows, $row);
        }

        return $rows;

    }


    function get_templates() {

        $stmt = $this->mysqli->prepare($this->configuration->get_templates_query);

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($rows, $row);
        }

        return $rows;

    }

    function get_template($template_id) {

        $stmt = $this->mysqli->prepare($this->configuration->get_template_query);

        $stmt->bind_param('i', $template_id); 

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        return $row;

    }

    function get_demos($session_id) {

        $stmt = $this->mysqli->prepare($this->configuration->get_demos_query);

        $stmt->bind_param('i', $session_id); 

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($rows, $row);
        }

        return $rows;

    }

    function get_sessions_id_for_clear() {

        $stmt = $this->mysqli->prepare($this->configuration->get_sessions_id_for_clear_query);

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($rows, $row);
        }

        return $rows;

    }

    function get_demos_for_clear($session_id) {

        $stmt = $this->mysqli->prepare($this->configuration->get_demos_for_clear_query);

        $stmt->bind_param('i', $session_id);

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($rows, $row);
        }

        return $rows;

    }

    function get_demo_for_timeout($session_id) {

        $stmt = $this->mysqli->prepare($this->configuration->get_demo_for_timeout_query);

        $stmt->bind_param('i', $session_id);

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        return $row;

    }

    function get_demo($demo_id) {

        $stmt = $this->mysqli->prepare($this->configuration->get_demo_query);

        $stmt->bind_param('i', $demo_id); 

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        return $row;

    }

    function insert_demo($name, $schema, $session_id, $template_id) {

        $stmt = $this->mysqli->prepare($this->configuration->insert_demo_query);

        $date = date('Y-m-d H:i:s');

        $stmt->bind_param('sssssi', $name, $schema, $date, $date, $session_id, $template_id); 

        $stmt->execute();

        return $this->mysqli->insert_id;

    }

    function update_demo($name, $params, $schema, $demo_id) {

        $stmt = $this->mysqli->prepare($this->configuration->update_demo_query);

        $date = date('Y-m-d H:i:s');

        $stmt->bind_param('ssssi', $name, $params, $schema, $date, $demo_id); 

        $stmt->execute();

    }

    function delete_demo($demo_id) {

        $stmt = $this->mysqli->prepare($this->configuration->delete_demo_query);

        $stmt->bind_param('i', $demo_id); 

        $stmt->execute();

    }

    function get_components($demo_id) {

        $stmt = $this->mysqli->prepare($this->configuration->get_components_query);

        $stmt->bind_param('i', $demo_id); 

        $stmt->execute();

        $result = $stmt->get_result();

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            array_push($rows, $row);
        }

        return $rows;

    }

    function get_component($component_id) {

        $stmt = $this->mysqli->prepare($this->configuration->get_component_query);

        $stmt->bind_param('i', $component_id); 

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        return $row;

    }

    function insert_component($file_name, $constructor, $content, $demo_id) {

        $stmt = $this->mysqli->prepare($this->configuration->insert_components_query);

        $date = date('Y-m-d H:i:s');

        $stmt->bind_param('sssssi', $file_name, $constructor, $content, $date, $date, $demo_id); 

        $stmt->execute();

    }

    function update_component($content, $component_id) {

        $stmt = $this->mysqli->prepare($this->configuration->update_component_query);

        $date = date('Y-m-d H:i:s');

        $stmt->bind_param('ssi', $content, $date, $component_id); 

        $stmt->execute();

    }

    function delete_components($demo_id) {

        $stmt = $this->mysqli->prepare($this->configuration->delete_components_query);

        $stmt->bind_param('i', $demo_id); 

        $stmt->execute();

    }

    


}