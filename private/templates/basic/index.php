<?php

require_once 'component.php';

class Index extends Component {

    function __construct($request, $session, $application, $start_page, $bootstraps) {

        parent::__construct($request, $session, $application);

        $this->bootstraps = $bootstraps;

        $this->start_page = $start_page;
    }

    function reset() {

    }

    function bootstrap() {

    }

    function get() {

        if (isset($this->request->get['reset'])) {

            foreach(array_reverse($this->bootstraps) as $component) {
            
                $component->reset();
            
            }

        }

        foreach($this->bootstraps as $component) {
            
            $component->bootstrap();
        
        }

        $application_host = $this->application->host;

        $request_demo_id = $this->request->demo_id;

        $path = $this->application->path;

        $start_page = $this->start_page;

        header("Location: $application_host/$path/$start_page.php");

        exit;

    }

    function post() {

    }

}