<?php

require_once 'relation.php';

class Lookup extends Relation {

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

        $this->acl = $params['acl'];

    }


    function get() {
        

        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $filters = [];

        // Correlated filter for initial loading on edit 
        if ( property_exists($this, 'parent') && $this->view && isset($this->view[2]) && isset($this->parent[$this->view[2][0]])) {

            $filters[$this->view[2][1]] = $this->get_query_param_part($this->view[2][1], [$this->parent[$this->view[2][0]], 'eq']);

        }

        // Correlated filter for change on edit or insert
        if ($this->view && isset($this->view[2]) && isset($this->request->get[$this->view[2][0]])) {

            $filters[$this->view[2][1]] = $this->get_query_param_part($this->view[2][1], [$this->request->get[$this->view[2][0]], 'eq']);
        
        }

        // Others filters
        /*
        foreach($this->request->get as $get_field => $get_value) {

            if (!in_array($get_field, ['ajax', 'options', 'id', 'nest', 'unnest', 'popup', 'page'])) {

                $filters[$get_field] = $get_value;

            }

            

        }*/
        if (isset($this->request->get[$this->dataset_label])) {

            $filters[$this->dataset_label] = $this->get_query_param_part($this->dataset_label, [$this->request->get[$this->dataset_label], 'like']);

        }

        

        $records = [];

        $args = null;

        $selects = [];

        $creators = [];

        // Check acl
        if (count($this->acl) > 0) {

            // Define fields
            $cd_authorized = $this->authorized_cd($this->acl[0], $this->acl[1], $this->acl[2]);
            $ru_authorized = $this->authorized_ru($this->acl[0], $this->acl[1], $this->acl[3]);
            $r_authorized = $this->authorized_r($this->acl[0], $this->acl[1], $this->acl[4]);

            // Logged user id
            $logged_user_id = $this->session->get_logged_user(); 

            array_push($creators, $logged_user_id);

            $logged_user = $this->application->select($this->acl[0], [], [
                'users_id' => $this->get_query_param_part('users_id', [$logged_user_id, 'eq'])
            ]);

            $sharings = $this->application->select('sharings', [], [
                'sharings_sharing_with' => $this->get_query_param_part('sharings_sharing_with', [$logged_user[0]['users_group_id'], 'eq'])
            ]);

            foreach($sharings as $sharing) {

                $sharings_user = $this->application->select($this->acl[0], [], [
                    'users_group_id' => $this->get_query_param_part('users_group_id', [$sharing['sharings_owner'], 'eq'])
                ]);

                if ($sharings_user) {

                    array_push($creators, $sharings_user[0]['users_id']);

                }

                

            }



            // If records are visible
            if($cd_authorized || $ru_authorized || $r_authorized) {
                
                
                if (!$cd_authorized) {
                    if ($ru_authorized) {
                        $args = $ru_authorized;  
                    } else if ($r_authorized) {
                        $args = $r_authorized;  
                    }
                }
                

                if ($args) {

                    array_push($selects, $this->dataset_id);

                    if (in_array($this->dataset_label, $args)) {

                        array_push($selects, $this->dataset_label);
    
                    }


                }

            }


        }
    
        if ($this->view && isset($this->view[1]) && $this->view[1]) {

            $records = $this->application->select($this->view[0], $this->view[1], $filters, $selects, false, 0, $creators);

        } else {

            $records = $this->application->select($this->dataset_table, [], $filters, $selects, false, 0, $creators);
        }

        $oninput = '';

        if ($this->view && isset($this->view[2])) {
            
            $component = $this->view[2][2];

            $oninput .= "if (this.value != '') {";
            
            $oninput .= " let new_url = '$application_host/$application_path/$component.php';";

            $oninput .= " new_url = url_with_new_param(new_url, '" . $this->dataset_label . "', this.value);";

            $oninput .= " new_url = url_with_new_param(new_url, 'options', '');";

            $oninput .= " new_url = ajax(new_url, 'lookup-$this->id');";

            $oninput .= " document.getElementById('lookup-$this->id').style.display = 'block';";

            $oninput .= "} else {";

            $oninput .= " document.getElementById('lookup-$this->id').style.display = 'none';";    

            $oninput .= "}";

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

                $labels = $this->application->select($this->dataset_table, [], [
                    $this->dataset_id => $this->get_query_param_part($this->dataset_id, [$this->value, 'eq'])
                ], [], true);

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


        $disabled = '';

        if (property_exists($this, 'disabled') && $this->disabled) {

            $disabled = $this->disabled;

        }


        $lookup = <<<EOT
        <!-- custom content -->
        <div class="field" style="position: relative;">
        <label for="$this->id">$this->label</label>
        <input type="hidden" id="$this->id" name="$name" value="$value">
        <input type="text" id="label-$this->id" oninput="$oninput" onkeydown="$onkeydown" value="$label_value" $disabled>
        <div id="lookup-$this->id" class="lookup">
        $options_output
        </div>
        <div class="error">$errors_output</div>
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