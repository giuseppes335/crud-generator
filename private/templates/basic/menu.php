<?php

require_once 'component.php';

class Menu extends Component {

    function __construct($request, $session, $application, $items) {

        parent::__construct($request, $session, $application);

        $this->items = $items;

    }

    function reset() {

    }

    function bootstrap() {

    }

    function get() {

        $application_host = $this->application->host;

        $request_demo_id = $this->request->demo_id;

        $path = $this->application->path;

        ob_start();

        foreach($this->items as $index => $item) {
            


            if ($item !== '-') {

                $selected_class = '';
                if ("/$path/$item[1]" === $this->request->script_name) {
                    $selected_class = 'selected';
                }

                $menu_index = $item[0];

                echo <<<EOT
                <li class="$selected_class">
                <a href="$application_host/$path/$item[1]"><span class="menu-index">$menu_index</span> $item[2]</a>
                </li>
                EOT;

            } else {

                echo <<<EOT
                <hr>
                EOT;

            }


        }

        $items_output = ob_get_contents();

        ob_end_clean();

        echo <<<EOT
        <!-- custom content -->
        <ul class="menu">
        $items_output
        <hr>
        <li>
        <a href="$application_host">Esci</a>
        </li>
        </ul>
        <!-- end custom content -->
        EOT;
        
    }

    function post() {



    }
    
}