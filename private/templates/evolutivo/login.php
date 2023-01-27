<?php

require_once 'relation.php';

class Login extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->fields = [
            (object) ['name' => 'utenti_email'],
            (object) ['name' => 'utenti_password']
        ];

        $this->action_component = $params['action_component'];

        $this->page = $params['page'];

        $this->redirect_component = $params['redirect_component'];

        $this->users_table = $params['users_table'];

        $this->username_field = $params['username_field'];

        $this->password_field = $params['password_field'];

    }

    function get() {

        if (isset($this->request->get['logout'])) {

            $this->session->logout();
        
        }

        ob_start();

        $action = $this->action_component . '.php';

        $email_value = '';

        $password_value = '';


        // TODO
        $errors = $this->session->get_errors('globals');

        $errors_output = '';

        if ($errors && isset($errors['errors'])) {

            $errors_output = implode(', ', $errors['errors']);

        }
        //

        echo <<<EOT
        <div class="error">$errors_output</div>
        <form action="$action" method="post" autocomplete="off">
EOT;

        if ($this->session->errors() && $this->session->get_errors($this->username_field)) {

            $email_value = $this->session->get_errors($this->username_field)['old_value'];

        }

        // TODO
        $errors = $this->session->get_errors($this->username_field);

        $errors_output = '';

        if ($errors && isset($errors['errors'])) {

            $errors_output = implode(', ', $errors['errors']);

        }
        //

        echo <<<EOT
        <div class="field">
        <label for="$this->username_field">Email</label>
        <input type="text" id="$this->username_field" name="$this->username_field" value="$email_value"/>
        <div class="error">$errors_output</div>
        </div>
EOT;


        if ($this->session->errors() && $this->session->get_errors($this->password_field)) {

            $password_value = $this->session->get_errors($this->password_field)['old_value'];

        }

        // TODO
        $errors = $this->session->get_errors($this->password_field);

        $errors_output = '';

        if ($errors && isset($errors['errors'])) {

            $errors_output = implode(', ', $errors['errors']);

        }
        //

        echo <<<EOT
        <div class="field">
        <label for="$this->password_field">Password</label>
        <input type="password" id="$this->password_field" name="$this->password_field" value="$password_value"/>
        <div class="error">$errors_output</div>
        </div>
EOT;
        

        // There errors where cleared
        if ($this->session->errors()) {

            $this->session->clear_errors();

        }

        echo <<<EOT
        <button class="button" type="submit">Submit</button>
        </form>
EOT;

    }


    function post() {

        $page = $this->page;

        // Redirect
        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $redirect = $this->redirect_component . '.php';

        $full_path_redirect = "$application_host/$application_path/$redirect";
        
        // Validation
        foreach($this->fields as $field) {

            // Not necessary to test isset($this->request->post[$field->name]) to check validation

            $errors = [];

            if ($this->request->post[$field->name] === '') {

                array_push($errors, "Campo richiesto");

            }

            if (count($errors) > 0) {

                $this->session->push_errors($field->name, [
                    'old_value' => $this->request->post[$field->name],
                    'errors' => $errors
                ]);

            } else {

                $this->session->push_errors($field->name, [
                    'old_value' => $this->request->post[$field->name]
                ]);

            }

        }

        if ($this->session->errors()) {

            header("Location: $application_host/$application_path/$page");

            exit;

        } 
        //
        

        $id = $this->application->login($this->users_table, $this->username_field, $this->password_field, $this->request->post[$this->username_field], $this->request->post[$this->password_field]);
        
        if ($id) {

            $this->session->set_logged_user($id);

        } else {

            array_push($errors, "Credenziali non valide");

            $this->session->push_errors('globals', [
                'errors' => $errors
            ]);

            header("Location: $application_host/$application_path/$page");

            exit;

        }

        

        header("Location: $full_path_redirect");

        exit;

    }

    function put() {

    }

    function delete() {
        
    }

    
}