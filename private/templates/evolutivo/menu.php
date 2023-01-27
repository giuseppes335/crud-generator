<?php

require_once 'relation.php';

class Menu extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->items = $params['items'];

    }

    private function echo_item($item) {

        $application_host = $this->application->host;

        $script_name = $this->application->script_name;

        $application_path = $this->application->path;

        if (is_array($item) && isset($item[0])) {

            // TODO Check parsing
            $menu_index = $item[0];

            $menu_page = $item[1];

            $memu_label = $item[2];
            //

            $class = '';

            if ($script_name === "/$application_path/$menu_page") {

                $class = 'class="selected"';

            }

            $menu_index = $item[0];

            echo <<<EOT
            <li $class>
            <a href="$application_host/$application_path/$menu_page"><span class="menu-index">$menu_index</span> $memu_label</a>
            </li>
EOT;

        } else {

            echo <<<EOT
            <hr>
EOT;

        }

    }

    function get() {

        ob_start();

        echo <<<EOT
        <ul class="menu">
EOT;

        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $script_name = $this->application->script_name;

        foreach($this->items as $index => $item) {

            $acl = [];
            
            if (isset($item[3])) {

                $acl = $item[3];

            }
            
            // Check acl popup form
            if (count($acl) > 0) {

                $cd_authorized = $this->authorized_cd($acl[0], $acl[1], $acl[2]);
                $ru_authorized = $this->authorized_ru($acl[0], $acl[1], $acl[3]);
                $r_authorized = $this->authorized_r($acl[0], $acl[1], $acl[4]);

                // If get popup is permitted
                if($cd_authorized || $ru_authorized || $r_authorized) {
                    
                    $this->echo_item($item);

                // If get is not permitted
                
                } /*else {

                    $errors = [];

                    array_push($errors, 'Permesso negato.');

                    $this->session->push_errors('popup_errors', [
                        'errors' => $errors
                    ]);

                }
                */

            // If no acl
            } else {

                $this->echo_item($item);

            }
            
        }

        echo <<<EOT
        <hr>
        <li>
        <a href="$application_host/$application_path/login.php?logout">Esci</a>
        </li>
        </ul>
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