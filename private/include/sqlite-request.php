<?php

class Request {

    function __construct($get, $post, $session, $application, $configuration) {

        $this->get = $get;

        $this->post = $post;

        $this->session = $session;

        $this->configuration = $configuration;

        $this->application = $application;

        // Migrations
        if (isset($this->get['migrate'])) {
 
            //$this->application->mysqli->query($this->configuration->create_users_table_query);

            //$this->application->mysqli->query($this->configuration->create_activations_table_query);

            $this->application->db->query($this->configuration->create_templates_table_query);

            $this->application->db->query($this->configuration->create_demos_table_query);
            
            $query0 = "INSERT INTO templates (name, path) VALUES ('Template 1', 'private/templates/template1');";
            
            $this->application->db->query($query0);

            //$this->application->mysqli->query($this->configuration->create_components_table_query);
        
        }

    }


    function validate($rules) {

        $error_essages = [];

        foreach($rules as $input => $input_rules) {

            foreach($input_rules as $rule) {
                
                $valid = $rule->validate($this->post[$input]);

                if (!$valid) {
                    if (!isset($error_essages[$input])) {
                        $error_essages[$input] = $rule->error_message();
                    }
                    
                }

            }


        }

        return $error_essages;

    }

    function translate($text) {
        
        if (isset($this->get['lang'])) {
            return $this->configuration->texts[$this->get['lang']][$text];
        } else {
            return $this->configuration->texts['it'][$text];
        }
        

    }

    function is_tool_panel() {

        return '/index.php' === $_SERVER['SCRIPT_NAME'] || '/demo.php' === $_SERVER['SCRIPT_NAME'] || '/scegli-un-applicazione.php' === $_SERVER['SCRIPT_NAME'] || '/personalizza-demo.php' === $_SERVER['SCRIPT_NAME'] || '/crea-elementi.php' === $_SERVER['SCRIPT_NAME'];

    }

    function get_uri() {

        return $_SERVER['REQUEST_URI'];

    }
    
}