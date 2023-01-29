<?php

require_once 'relation.php';

class InputText extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->id = $params['id'];

        $this->label = $params['label'];

        $this->name = $params['name'];

        $this->format = $params['format'];

        $this->mysql_type = $params['mysql_type'];

        $this->nullable = $params['nullable'];

        $this->value = '';

        $this->is_password = $params['is_password'];

    }

    function set_value($value) {

        $this->value = $value;

    }

    function get() {

        ob_start();

        $id = $this->id;

        $label = $this->label;

        $name = $this->name;

        $value = $this->value;

        // TODO
        $errors = $this->session->get_errors($this->name);

        $errors_output = '';

        if ($errors && isset($errors['errors'])) {

            $errors_output = implode(', ', $errors['errors']);

        }
        //

        $disabled = '';

        if (property_exists($this, 'disabled') && $this->disabled) {

            $disabled = $this->disabled;

        }

        echo <<<EOT
        <div class="field">
        <label for="$id">$label</label>
        <input type="text" id="$id" name="$name" value="$value" $disabled/>
        <div class="error">$errors_output</div>
        </div>
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

    function disable() {

        $this->disabled = 'disabled';

    }
    
}