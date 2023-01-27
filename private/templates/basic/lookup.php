<?php

require_once 'component.php';

class Lookup extends Component {

    function __construct($request, $session, $application, $id, $label, $name, $format, $mysql_type, $nullable = '', $dataset_table, $dataset_id, $dataset_label, $view = null, $multiselect = null) {

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

        // Correlated filter for initial loading on edit 
        if ( property_exists($this, 'parent') && $this->view && isset($this->view[2]) && isset($this->parent[$this->view[2][0]])) {

            $filters[$this->view[2][1]] = $this->parent[$this->view[2][0]];

        }

        // Correlated filter for change on edit or insert
        if ($this->view && isset($this->view[2]) && isset($this->request->get[$this->view[2][0]])) {

            $filters[$this->view[2][1]] = $this->request->get[$this->view[2][0]];
        
        }

        // Others filters
        /*
        foreach($this->request->get as $get_field => $get_value) {

            if (!in_array($get_field, ['ajax', 'options', 'id', 'nest', 'unnest', 'popup', 'page'])) {

                $filters[$get_field] = $get_value;

            }

            

        }*/
        if (isset($this->request->get[$this->dataset_label])) {

            $filters[$this->dataset_label] = $this->request->get[$this->dataset_label];

        }

        

        $records = [];
    
        if ($this->view && isset($this->view[1]) && $this->view[1]) {

            $records = $this->application->select($this->view[0], $this->view[1], $filters, [], true);

        } else {

            $records = $this->application->select($this->dataset_table, [], $filters, [], true);
        }



        $oninput = '';

        if ($this->view && isset($this->view[2])) {
            
            $component = $this->view[2][2];

            $oninput = "document.getElementById('$this->id').value = null;";
            
            $oninput .= "let new_url = '$application_host/$path/$component.php';";

            $oninput .= " new_url = url_with_new_param(new_url, '" . $this->dataset_label . "', this.value);";

            $oninput .= " new_url = url_with_new_param(new_url, 'options', '');";

            $oninput .= " new_url = ajax(new_url, 'lookup-$this->id');";

            $oninput .= " document.getElementById('lookup-$this->id').style.display = 'block';";


            

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

            $tab_index = 0;

            foreach($options as $id => $label) {

                $onclick = <<<EOT
                document.getElementById('$this->id').value = this.dataset.value; 
                document.getElementById('label-$this->id').value = event.target.textContent;
                document.getElementById('lookup-$this->id').style.display = 'none';
                EOT;

                $onkeydown = <<<EOT
                let key = event.which || event.keyCode;
                let item = this;
                if (key === 40) {
                    item = this.nextSibling;
                    if (item) {
                        item.focus();
                    }
                } else if (key === 38) {
                    item = this.previousSibling;
                    if (item) {
                        item.focus();
                    }
                } else if (key === 13) {
                    document.getElementById('$this->id').value = this.dataset.value; 
                    document.getElementById('label-$this->id').value = event.target.textContent;
                    document.getElementById('lookup-$this->id').style.display = 'none';  
                }
                
                EOT;

                echo <<<EOT
                <div tabindex="$tab_index"class="lookup-record" onclick="$onclick" onkeydown="$onkeydown" data-value="$id">$label</div>
                EOT;

                $tab_index++;

            }

            $options_output = ob_get_contents();

            ob_end_clean();

        }

        $name = $this->name;

        $value = '';

        $label_value = '';
        
        if (property_exists($this, 'value')) {

            if ('' !== $this->value) {

                $value = $this->value;

                $labels = $this->application->select($this->dataset_table, [], [$this->dataset_id => $this->value], [], true);

                if (count($labels) > 0) {
      
                    $label_value = $labels[0][$this->dataset_label];
    
                }

            }
            



            

        }

        $onkeydown = <<<EOT
        let key = event.which || event.keyCode;
        let item = this;
        if (key === 40) {
            let lookup = document.getElementById('lookup-$this->id');
            let lookup_first_child = lookup.firstChild;
            if (lookup_first_child) {
                lookup_first_child.focus();
            }
        }
        EOT;


        $errors = $this->session->get_errors($this->name);

        $errors_output = '';

        if ($errors && isset($errors['errors'])) {

            $errors_output = implode(', ', $errors['errors']);

        }


        $lookup = <<<EOT
        <!-- custom content -->
        <div class="field" style="position: relative;">
        <label for="$this->id">$this->label</label>
        <input type="hidden" id="$this->id" name="$name" value="$value">
        <input type="text" id="label-$this->id" oninput="$oninput" onkeydown="$onkeydown" value="$label_value">
        <div id="lookup-$this->id" class="lookup">
        $options_output
        </div>
        $errors_output
        </div>
        <!-- end custom content -->
        EOT;

        if (isset($this->request->get['options'])) {

            echo $options_output;

        } else {

            echo $lookup;

        }

    }

    function post() {

        

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