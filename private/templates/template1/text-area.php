<?php

require_once 'relation.php';

class TextArea extends Relation {

    function __construct(Request $request, Session $session, $application, String $id, String $label, String $name, String $format, String $mysql_type, String $nullable) {

        parent::__construct($request, $session, $application);

        $this->id = $id;

        $this->label = $label;

        $this->name = $name;

        $this->format = $format;

        $this->mysql_type = $mysql_type;

        $this->nullable = $nullable;

        $this->value = '';

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
        <textarea id="$id" name="$name" value="$value" $disabled></textarea>
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