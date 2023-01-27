<?php

require_once 'component.php';

class Blank extends Component {

    function __construct($request, $session, $application) {

        parent::__construct($request, $session, $application);
   
    }

    function reset() {
        
    }

    function bootstrap () {
        
    }

    function get() {

        echo '';

    }

    function post() {
        
    }

    
}