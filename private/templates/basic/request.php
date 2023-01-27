<?php

require_once 'component.php';

class Request extends Component {

    function __construct() {

        $this->get = $_GET;

        $this->post = $_POST;

        $this->method = $_SERVER['REQUEST_METHOD']; 
        
        $this->uri = $_SERVER['REQUEST_URI']; 

        $this->script_name = $_SERVER['SCRIPT_NAME'];

        $this->demo_id = '';
        
        $tags_uri = explode('/', $this->uri);

        if (isset($tags_uri[3])) {

            $this->demo_id = $tags_uri[3];

        }

        $this->referer = '';

        if (isset($_SERVER['HTTP_REFERER'])) {

            $this->referer = $_SERVER['HTTP_REFERER'];

        }

        $this->query_string = '';

        if (isset($_SERVER['QUERY_STRING'])) {

            $this->query_string = $_SERVER['QUERY_STRING'];

        }

        

    }

    function reset() {
        
    }

    function bootstrap () {
        
    }

    function get() {

    }

    function post() {
        
    }

    function delete_query_string_param($old_query_string, $param) {

        $query_string = '';

        $query_string = str_replace('?', '', $old_query_string);

        $query_string_params_values = explode('&', $query_string);

        // Bug
        if ('' === $query_string_params_values[0]) {

            $query_string_params_values = [];
            
        }

        $new_query_string_params_values = [];

        foreach($query_string_params_values as $param_value) {

            if ($param !== explode('=', $param_value)[0]) {

                array_push($new_query_string_params_values, $param_value);

            }

        }

        $new_query_string = '?' . implode('&', $new_query_string_params_values);

        return $new_query_string;

    }

    function set_query_string_param($old_query_string, $param, $value) {
        
        $query_string = '';

        $query_string = str_replace('?', '', $old_query_string);

        $query_string_params_values = explode('&', $query_string);

        // Bug
        if ('' === $query_string_params_values[0]) {

            $query_string_params_values = [];
            
        }

        $new_query_string_params_values = [];

        $check = false;

        foreach($query_string_params_values as $param_value) {

            if ($param === explode('=', $param_value)[0]) {

                $check = true;
                array_push($new_query_string_params_values, "$param=$value");

            } else {

                array_push($new_query_string_params_values, $param_value);

            }

        }

        if (!$check) {

            array_push($new_query_string_params_values, "$param=$value");

        }
        
        $new_query_string = '?' . implode('&', $new_query_string_params_values);

        return $new_query_string;

    }

    function update_query_string_param($old_query_string, $old_param, $new_param, $new_param_value) {

        $query_string = '';

        $query_string = str_replace('?', '', $old_query_string);

        $query_string_params_values = explode('&', $query_string);

        // Bug
        if ('' === $query_string_params_values[0]) {

            $query_string_params_values = [];
            
        }

        $new_query_string_params_values = [];

        foreach($query_string_params_values as $param_value) {

            if ($old_param === explode('=', $param_value)[0]) {

                array_push($new_query_string_params_values, "$new_param=$new_param_value");

            } else {

                array_push($new_query_string_params_values, $param_value);

            }

        }

        $new_query_string = '?' . implode('&', $new_query_string_params_values);

        return $new_query_string;

    }
    
}