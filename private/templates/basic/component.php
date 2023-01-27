<?php

abstract class Component {

    function __construct($request, $session, $application) {

        $this->request = $request;

        $this->session = $session;

        $this->application = $application;

        /*
        if ($_SESSION['CURRENT_DEMO_ID'] != $this->request->demo_id) {

            header("Location: $host/demo.php");

            exit;

        }
        */

    }

    abstract function reset();

    abstract function bootstrap();

    abstract function get();

    abstract function post();
    
}