<?php

require_once 'component.php';

class InputText extends Component {

    function __construct($request, $session, $application, $id, $label, $name, $format, $mysql_type, $nullable = 'not null') {

        parent::__construct($request, $session, $application);

        $this->id = $id;

        $this->label = $label;

        $this->name = $name;

        $this->format = $format;

        $this->mysql_type = $mysql_type;

        $this->nullable = $nullable;

    }

    function reset() {
        
    }

    function bootstrap() {
        
    }

    function get() {

        $value = '';

        if (isset($this->value)) {
            $value = $this->value;
        }

        $errors = $this->session->get_errors($this->name);

        $errors_output = '';

        if ($errors && isset($errors['errors'])) {

            $errors_output = implode(', ', $errors['errors']);

        }

        

        echo <<<EOT
        <!-- custom content -->
        <div class="field">
        <label for="$this->id">$this->label</label>
        <input type="text" id="$this->id" name="$this->name" value="$value"/>
        <div>$errors_output</div>
        </div>
        <!-- end custom content -->
        EOT;

    }

    function post() {

    }


    function set_insert_table($table) {

        $this->insert_table = $table;

    }


    function set_value($value) {

        $this->value = $value;

    }

    function set_parent($parent) {

        $this->parent = $parent;

    }
    
}