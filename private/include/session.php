<?php

class Session {


    function get_session_id() {
        if (!isset($_SESSION['ID'])) {
            $_SESSION['ID'] = time();
        }
        return $_SESSION['ID'];
    }


    function set_error_messages($error_messages) {

        $_SESSION['error_messages'] = $error_messages;

    }

    function get_error_message($input) {

        return isset($_SESSION['error_messages'][$input])?$_SESSION['error_messages'][$input]:'';

    }

    function unset_error_messages() {

        unset($_SESSION['error_messages']);
        unset($_SESSION['login_error_message']);
        unset($_SESSION['activate_error_message']);

    }

    function set_old_inputs($inputs) {

        $_SESSION['old_inputs'] = $inputs;

    }

    function get_old_input($input) {

        return isset($_SESSION['old_inputs'][$input])?$_SESSION['old_inputs'][$input]:'';

    }

    function unset_old_inputs() {

        unset($_SESSION['old_inputs']);

    }

    function set_login_error_message($message) {

        $_SESSION['login_error_message'] = $message;

    }

    function get_login_error_message() {

        return isset($_SESSION['login_error_message'])?$_SESSION['login_error_message']:'';

    }

    function set_logged_user($email) {

        $_SESSION['logged_user'] = $email;

    }

    function is_loggedin() {

        return (isset($_SESSION['logged_user']))?true:false;
    }

    function get_logged_user() {

        if ($this->is_loggedin()) {
            return $_SESSION['logged_user'];
        }
    }


    function set_logged_user_id($id) {

        $_SESSION['logged_user_id'] = $id;

    }

    function get_logged_user_id() {

        if ($this->is_loggedin()) {
            return $_SESSION['logged_user_id'];
        }
    }

    function set_activate_error_message($message) {

        $_SESSION['activate_error_message'] = $message;

    }

    function get_activate_error_message() {

        return isset($_SESSION['activate_error_message'])?$_SESSION['activate_error_message']:'';

    }

    function logout() {

        unset($_SESSION['logged_user']);

    }
}