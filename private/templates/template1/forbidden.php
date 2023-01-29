<?php

require_once 'relation.php';


class Forbidden extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

    }


    function get() {

        ob_start();

        echo <<<EOT
        <p>Permesso negato.</p>
EOT;
        
        $output = ob_get_contents();

        ob_end_clean();
        

        echo $output;

    }

    function post() {

    }

    function put() {

    }

    function delete() {
        
    }

}