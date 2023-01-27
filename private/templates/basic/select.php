<?php

require_once 'component.php';

class Select extends Component {

    function __construct($request, $session, $application, $id, $label, $name, $format, $mysql_type, $nullable = 'not null', $dataset_table, $dataset_id, $dataset_label, $view = null, $multiselect = null) {

        parent::__construct($request, $session, $application);

        $this->id = $id;

        $this->label = $label;

        $this->name = $name;

        $this->format = $format;

        $this->mysql_type = $mysql_type;

        $this->nullable = $nullable;

        $this->dataset_table = $dataset_table;

        $this->dataset_id = $dataset_id;

        $this->dataset_label = $dataset_label;

        $this->view = $view;

        $this->multiselect = $multiselect;

    }

    function get_errors() {

        $errors = [];

        if (strpos($this->nullable, 'not null') && $this->value === null) {

            array_push($errors, "Campo richiesto");

        }


        if (strpos($this->nullable, 'unique')) {

            $rows = $this->application->select($this->insert_table, [], [$this->name => $this->value]);

            if (count($rows) > 0) {

                array_push($errors, "Campo duplicato");

            }

        }

        if ($this->format === 's' && !is_string($this->value)) {

            array_push($errors, "Campo non testuale");

        } else if ($this->format === 'i' && !is_int($this->value)) {

            array_push($errors, "Campo non intero");

        } else if ($this->format === 'd' && !is_float($this->value)) {

            array_push($errors, "Campo non decimale");

        }


    }

    function reset() {
        
    }

    function bootstrap() {


    }

    function get() {

        $application_host = $this->application->host;

        $path = $this->application->path;

        $filters = [];

        // Filter for initial loading on edit 
        if ( property_exists($this, 'parent') && $this->view && isset($this->view[2]) && $this->parent[$this->view[2][0]->name]) {

            $filters[$this->view[2][1]] = $this->parent[$this->view[2][0]->name];

        }

        // Filter for change on edit or insert
        if ($this->view && isset($this->view[2]) && isset($this->request->get[$this->view[2][0]->name])) {

            $filters[$this->view[2][1]] = $this->request->get[$this->view[2][0]->name];
        
        }


        $records = [];
    
        if ($this->view && isset($this->view[1]) && $this->view[1]) {

            $records = $this->application->select($this->view[0], $this->view[1], $filters);

        } else {

            $records = $this->application->select($this->dataset_table, [], $filters);
        }

        $script = '';


        if ($this->view && isset($this->view[2])) {
      
            $component = $this->view[2][2];

            $script = "let new_url = '$application_host/$path/$component.php';";

            /*
            if ($this->view && isset($this->view[3])) {

                foreach($this->view[3] as $url_param) {
    
                    $onchange .= "new_url = remove_url_param(new_url, '$url_param');";
    
                }
                
            }
            */



            
            $script .= " new_url = url_with_new_param(new_url, 'ajax', '');";

            $script .= " new_url = url_with_new_param(new_url, 'options', '');";

            $script .= " addListener('" . $this->view[2][0]->id . "', '" . $this->view[2][0]->name . "', new_url, '" . $this->id . "');";

        }


        $options_output = '';

        if (count($records) > 0) {

            $options = [];

            foreach($records as $record) {

                $id = $record[$this->dataset_id];

                $label = '';

                if (is_object($this->dataset_label)) {

                    $id_field = $this->dataset_label->id_field;

                    $prefix_field = $this->dataset_label->prefix_field;

                    $this->dataset_label->set_id($record[$id_field]);

                    $this->dataset_label->set_prefix($record[$prefix_field]);

                    $label = $this->dataset_label->get();

                } else {

                    $label = $record[$this->dataset_label];

                }

                

                $options[$id] = $label;

            }

            ob_start();

            
            echo <<<EOT
            <option value="">...</option>
            EOT;

            foreach($options as $id => $label) {

                $selected_tag = '';
                // Check for multiple
                if ($this->multiselect && in_array($id, $this->values) || $id == $this->value) {
                    $selected_tag = 'selected';
                }

                echo <<<EOT
                <option $selected_tag value="$id">$label</option>
                EOT;

            }

            $options_output = ob_get_contents();

            ob_end_clean();

        }

        $name = $this->name;

        $multiselect_tag = '';

        if ($this->multiselect) {

            $multiselect_tag = 'multiple';

            $name = $this->name . '[]';

        }

        $errors = $this->session->get_errors($this->name);

        $errors_output = '';

        if ($errors && isset($errors['errors'])) {

            $errors_output = implode(', ', $errors['errors']);

        }


        $select = <<<EOT
        <!-- custom content -->
        <div class="field">
        <img class="loader" src="$application_host/img/loader.png" onload="$script">
        <label for="$this->id">$this->label</label>
        <select id="$this->id" name="$name" $multiselect_tag>
        $options_output
        </select>
        <div>$errors_output</div>
        </div>
        <!-- end custom content -->
        EOT;

        if (isset($this->request->get['options'])) {

            echo $options_output;

        } else {

            echo $select;

        }

    }

    function post() {

        if ($this->multiselect) {



            if (isset($this->request->post[$this->name])) {

                $values = $this->application->select($this->multiselect[0], [], [$this->multiselect[2] => $this->related]);

                $values = array_column($values, $this->multiselect[1]);
    

                $to_delete = array_diff($values, $this->request->post[$this->name]);

                foreach($to_delete as $id_to_delete) {

                    $this->application->delete_where($this->multiselect[0], [
                        $this->multiselect[2] => $this->related,
                        $this->multiselect[1] => $id_to_delete
                    ]);

                }

                $to_insert = array_diff($this->request->post[$this->name], $values);

                foreach($to_insert as $id_to_insert) {

                    $this->application->insert('ii', $this->multiselect[0], [
                        $this->multiselect[2] => $this->related,
                        $this->multiselect[1] => $id_to_insert]);

                }


            }


        }

    }

    function set_value($value) {

        $this->value = $value;

    }

    function set_values($values) {

        $this->values = $values;
        

    }

    function set_related($related) {

        $this->related = $related;

    }

    function set_parent($parent) {

        $this->parent = $parent;

    }

    function set_action_component($action_component) {

        $this->action_component = $action_component;

    }

    function set_insert_table($table) {

        $this->insert_table = $table;

    }

    
}