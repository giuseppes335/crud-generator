<?php

require_once 'relation.php';

class Index extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->bootstraps = $params['bootstraps'];

        $this->start_page = $params['start_page'];
    }

    function get() {

        if (isset($this->request->get['reset'])) {

            foreach(array_reverse($this->bootstraps) as $component) {

                if (is_object($component)) {
                    $component->reset();

                }
                
            
            }

        }

        foreach($this->bootstraps as $component) {
            if (is_object($component)) {
            $component->bootstrap();
            }
        
        }

        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $start_page = $this->start_page;

        header("Location: $application_host/$application_path/$start_page.php");

        exit;

    }

    function post() {

    }

    function put() {

    }

    function delete() {
        
    }

}