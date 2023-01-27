<?php

require_once 'component.php';

class Page extends Component {

    function __construct($request, $session, $application, $header, $content, $footer) {

        parent::__construct($request, $session, $application);

        $this->header = $header;

        $this->content = $content;

        $this->footer = $footer;

    }


    function reset() {

        $this->footer->reset();

        $this->content->reset();

        $this->header->reset();

    }

    function bootstrap() {
        
        $this->header->bootstrap();

        $this->content->bootstrap();

        $this->footer->bootstrap();

    }

    function get() {


        $popup_content = <<<EOT
        <div id="overlay">
        <div id="popup">
        <div id="popup-content">

        EOT;

        $content = "Contenuto bloccato";

        $popup_content .= $content;

        $popup_content .= <<<EOT
        </div>
        </div>
        </div>
        EOT;

        ob_start();

        if (!isset($this->request->get['ajax'])) {
            $this->header->get();

        }

        $this->content->get();

        //echo $popup_content;

        if (!isset($this->request->get['ajax'])) {
            $this->footer->get();

        }

        $output = ob_get_contents();

        ob_end_clean();

        echo <<<EOT
        <!-- custom content -->
        $output
        <!-- end custom content -->
        EOT;

    }

    function post() {

        $this->content->post();

    }

}