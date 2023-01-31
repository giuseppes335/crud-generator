<?php

require_once 'relation.php';

class Session extends Relation {

    function __construct() {

    }

    function get() {

    }

    function post() {

    }

    function put() {

    }

    function delete() {
        
    }

    function logout() {

        if (isset($_SESSION['LOGGED_USER']) && $_SESSION['LOGGED_USER']) {

            unset($_SESSION['LOGGED_USER']);

        }

    }

    function set_logged_user($id) {

        $_SESSION['LOGGED_USER'] = $id;

    }

    function get_logged_user() {

        if (isset($_SESSION['LOGGED_USER']) && $_SESSION['LOGGED_USER']) {

            return $_SESSION['LOGGED_USER'];

        }

    }

    function errors() {

        $error_check = false;

        if (isset($_SESSION['ERRORS']) && count($_SESSION['ERRORS']) > 0) {

            foreach($_SESSION['ERRORS'] as $error) {

                if (isset($error['errors'])) {

                    $error_check =  true;

                }

            }

            

        }

        return $error_check;

    }

    function push_errors($field, $errors) {

        if (!isset($_SESSION['ERRORS'])) {

            $_SESSION['ERRORS'] = [];

        }

        $_SESSION['ERRORS'][$field] = $errors;

    }

    function get_errors($field) {

        if (isset($_SESSION['ERRORS']) && isset($_SESSION['ERRORS'][$field])) {

            return $_SESSION['ERRORS'][$field];

        } else {

            return null;
            
        }

    }
    
    function get_errors_output($field) {
        
        $errors = $this->get_errors($field);
        
        $errors_output = '';
        
        if ($errors && isset($errors['errors'])) {
            
            $errors_output = implode(', ', $errors['errors']);
            
        }
        
        return $errors_output;
        
    }

    function clear_errors() {

        if (isset($_SESSION['ERRORS'])) {

            foreach($_SESSION['ERRORS'] as $field => $error) {

                if($field !== 'popup_errors') {

                    unset($_SESSION['ERRORS'][$field]);

                }

            }

            

        }

    }

    function clear_error($field) {

        if (isset($_SESSION['ERRORS']) && $_SESSION['ERRORS'][$field]) {

            unset($_SESSION['ERRORS'][$field]);

        }

    }


    function set_prev_output($prev_output, $prev_link, $comparator) {

        if (!isset($_SESSION['PREV']) || !is_array($_SESSION['PREV'])) {

            $_SESSION['PREV'] = [];

        }

        array_push($_SESSION['PREV'], ['output' => $prev_output, 'link' => $prev_link, 'comparator' => $comparator]);

    }

    function get_prev_output($stop_comparator) {

        $ret = '';

        if (isset($_SESSION['PREV']) && is_array($_SESSION['PREV'])) {

            $index = 0;

            $prev = $_SESSION['PREV'][$index];

            $prevs = [];

            while (isset($_SESSION['PREV'][$index]) && $prev && $stop_comparator !== $prev['comparator']) {

                $prev_output = $prev['output'];

                $prev_link  = $prev['link'];

                $prev_id = $prev['id'];

                array_push($prevs, $prev);

                $ret .= <<<EOT
                <div style="position: relative;">
                <div data-selected="$prev_id" class="disabled" onclick="window.location.href = '$prev_link'">
                </div>
                $prev_output
                </div>
EOT;

                $index++;

                $prev = null;

                if (isset($_SESSION['PREV'][$index])) {

                    $prev = $_SESSION['PREV'][$index];

                }

                

            }

            unset($_SESSION['PREV']);

            $_SESSION['PREV'] = $prevs;

        }

        return $ret;

    }

    function prev_output_set_last_id($id) {

        if (isset($_SESSION['PREV']) && is_array($_SESSION['PREV'])) {

            $last = array_key_last($_SESSION['PREV']);

            $_SESSION['PREV'][$last]['id'] = $id;

        }

    }

    function clear_output($comparator) {

        if (isset($_SESSION['PREV'])) {

            $prevs = [];

            foreach($_SESSION['PREV'] as $prev) {

                if ($prev['comparator'] !== $comparator) {

                    array_push($prevs, $prev);

                }

            }

            $_SESSION['PREV'] = $prevs;

        }
        
    }

    function clear_all_output() {

        unset($_SESSION['PREV']);

    }
   
    
}