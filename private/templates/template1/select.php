<?php

require_once 'relation.php';

class Select extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->id = $params['id'];

        $this->label = $params['label'];

        $this->name = $params['name'];

        $this->format = $params['format'];

        $this->mysql_type = $params['mysql_type'];

        $this->nullable = $params['nullable'];

        $this->dataset_table = $params['dataset_table'];

        $this->dataset_id = $params['dataset_id'];

        $this->dataset_label = $params['dataset_label'];

        $this->view = $params['view'];

        $this->multiselect = $params['multiselect'];

    }


    function get() {

        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $filters = [];

        // Filter for initial loading on edit 
        if ( property_exists($this, 'parent') && $this->view && isset($this->view[2]) && $this->parent[$this->view[2][0]->name]) {

            $filters[$this->view[2][1]] = $this->get_query_param_part($this->view[2][1], [$this->parent[$this->view[2][0]->name], 'eq']);

        }

        
        // Filter for change on edit or insert 

        if ($this->view && isset($this->view[2]) && isset($this->request->get[$this->view[2][0]->name])) {
            echo $this->view[2][1];
            $filters[$this->view[2][1]] = $this->get_query_param_part($this->view[2][1], [$this->request->get[$this->view[2][0]->name], 'eq']);
        
        }


        $records = [];



        $selects = [];
        
        $this->update_authorized_fields();
        
        $this->update_creators();  
        
        $creators = null;
        
        if ($this->auth) {
            
            if (property_exists($this, 'authorized_fields')) {
                
                $authorized_fields = [];
                
                if ($this->authorized_fields) {
                    
                    if (count($this->authorized_fields) > 0) {
                        
                        
                        if (in_array($this->dataset_id, $this->authorized_fields)) {
                            
                            array_push($selects, $this->dataset_id);
                            
                        }
                        
                        if (in_array($this->dataset_label, $this->authorized_fields)) {
                            
                            array_push($selects, $this->dataset_label);
                            
                        }
                        
                    } else {
                        
                        array_push($selects, $this->dataset_id);
                        
                        array_push($selects, $this->dataset_label);
                        
                    }
                    
                } else {
                    
                    array_push($selects, $this->dataset_id);
                    
                    array_push($selects, $this->dataset_label);
                    
                }
                
            }
            
            if (property_exists($this, 'creators')) {
                
                $creators = [];
                
                if ($this->creators && count($this->creators) > 0) {
                    
                    $creators = $this->creators;
                    
                }
                
            }
            
        }
        
    
                
        if ($this->view && isset($this->view[1]) && $this->view[1]) {

            $records = $this->application->select($this->view[0], $this->view[1], $filters, $selects, false, 0, $creators);

        } else {

            $records = $this->application->select($this->dataset_table, [], $filters, $selects, false, 0, $creators);
        }

        $script = '';


        if ($this->view && isset($this->view[2])) {
      
            $component = $this->view[2][2];

            $script = "let new_url = '$application_host/$application_path/$component.php';";

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


        $disabled = '';

        if (property_exists($this, 'disabled') && $this->disabled) {

            $disabled = $this->disabled;

        }


        $select = <<<EOT
        <div class="field">
        <img class="loader" src="$application_host/$application_path/img/loader.png" onload="$script">
        <label for="$this->id">$this->label</label>
        <select id="$this->id" name="$name" $multiselect_tag $disabled>
        $options_output
        </select>
        <div class="error">$errors_output</div>
        </div>
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

    function put() {

    }

    function delete() {
        
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

    function disable() {

        $this->disabled = 'disabled';

    }



    
}