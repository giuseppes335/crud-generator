<?php

require_once 'component.php';

class Form extends Component {

    function __construct($request, $session, $application, $fields, $action_component, $insert_table, $redirect_component, $referencing = [], $triggers = [], $seed = []) {

        parent::__construct($request, $session, $application);

        $this->fields = $fields;

        $this->action_component = $action_component;

        $this->insert_table = $insert_table;

        $this->redirect_component = $redirect_component;

        $this->referencing = $referencing;

        $this->triggers = $triggers;

        $this->seed = $seed;

    }

    function reset() {

        foreach(array_reverse($this->fields) as $field) {

            $field->reset();

        }

        $this->application->drop($this->insert_table);

    }

    function bootstrap() {


        $fields_to_migrate = [];

        foreach($this->fields as $field) {

            

            // Check for multiselect
            if (property_exists($field, 'multiselect') && $field->multiselect) {

            } else {

                array_push($fields_to_migrate, $field);
                
            }

        }


        $this->application->migrate($this->insert_table, $fields_to_migrate);

        $row = [];

        foreach($this->seed as $record) {

            $row[$this->insert_table . '_id'] = $record[0];

            $formats = 'i';

            foreach($this->fields as $column_index => $column) {

                if (isset($record[$column_index + 1])) {

                    $row[$column->name] = $record[$column_index + 1];

                    $formats .= $column->format;

                }
                
            }


            $this->application->insert($formats, $this->insert_table, $row);

        }


        foreach($fields_to_migrate as $field) {

            $field->bootstrap();

        }

    }

    function get() {

        
        $row = [];

        if (isset($this->request->get['id'])) {

            $row = $this->application->find($this->insert_table, $this->request->get['id']);
        
        } 

        foreach($this->request->get as $get_field => $get_value) {

            if (in_array($get_field, array_column((array) $this->fields, 'name'))) {

                $row[$get_field] = $get_value;

            }

        }

        if (isset($this->request->get['id'])) {

            $action = $this->action_component . '.php?id=' . $this->request->get['id'];

        } else {

            $action = $this->action_component . '.php';

        }




        ob_start();

        foreach($this->fields as $field) {


            if (method_exists($field, 'set_action_component')) {

                $field->set_action_component($this->action_component);

            }

            if (isset($this->request->get['id'])) {

                $field->set_parent($row);

            }


            if (isset($this->request->get[$field->name])) {

                $field->set_value($this->request->get[$field->name]);

            } else {


                // Check for multiselect
                if (property_exists($field, 'multiselect') && $field->multiselect) {

                    if (isset($this->request->get['id'])) {

                        $options = $this->application->select($field->multiselect[0], [], [
                            $field->multiselect[2] => $this->request->get['id']
                        ]);
    
                        $values = array_column($options, $field->multiselect[1]);
    
                        $field->set_values($values);

                    }

    
                } else {


                    if (isset($row[$field->name])) {
                    
                        $value = $row[$field->name];
    
                        $field->set_value($value);
    
                    } 


                }



            }


            if ($this->session->errors() && $this->session->get_errors($field->name)) {

                $field->set_value($this->session->get_errors($field->name)['old_value']);

            }




            


            $field->get();

        }

        $inputs_output = ob_get_contents();

        ob_end_clean();


        if ($this->session->errors()) {

            $this->session->clear_errors();

        }



        echo <<<EOT
        <!-- custom content -->
        <form action="$action" method="post" autocomplete="off">
        $inputs_output
        <button class="button" type="submit">Submit</button>
        </form>
        <!-- end custom content -->
        EOT;

    }


    function post() {

        // Redirect
        $application_host = $this->application->host;

        $path = $this->application->path;

        $redirect = $this->redirect_component . '.php';

        $full_path_redirect = "$application_host/$path/$redirect";

        if ($this->request->referer) {

            $full_path_redirect = $this->request->referer;

        }



        

        // Validation
        foreach($this->fields as $field) {

            $errors = [];

            if (null !== strpos($field->nullable, 'not null') && $this->request->post[$field->name] === '') {
                
                array_push($errors, "Campo richiesto");

            }


            if (strpos($field->nullable, 'unique')) {
                

                $rows = $this->application->select($this->insert_table, [], [$field->name => $this->request->post[$field->name]]);

                if (isset($this->request->get['id'])) {

                    $unique = true;

                    foreach($rows as $row) {

                        if ($row[$this->insert_table . '_id'] != $this->request->get['id']) {

                            $unique = false;

                        }

                    }

                    if (!$unique) {

                        array_push($errors, "Campo duplicato");

                    }

                } else {

                    if (count($rows) > 0) {

                        array_push($errors, "Campo duplicato");
    
                    }

                }



            }

            if ($field->format === 's') {

                //array_push($errors, "Campo non testuale");

            } else if ($field->format === 'i') {

                preg_match('/^[1-9][0-9]*$/', $this->request->post[$field->name], $matches);

                if (count($matches) === 0) {

                    array_push($errors, "Campo non intero");

                }

            } else if ($field->format === 'd') {

                preg_match('/^[1-9][0-9]*(\.[1-9][0-9]*)?$/', $this->request->post[$field->name], $matches);

                if (count($matches) === 0) {

                    array_push($errors, "Campo non decimale");

                }

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

            header("Location: $full_path_redirect");

            exit;

        } 
        //

        $formats = '';

        $fields = [];

        $multiselects = [];

        foreach($this->fields as $field) {

            // Check for multiselect
            if (!$field->multiselect) {

                $fields[$field->name] = $this->request->post[$field->name];

                $formats .= $field->format;

            } else {

                array_push($multiselects, $field);

            }

           

        }

        $id = null;

        $object = [];

        if ($this->request->get['id']) {

            $this->application->update($formats, $this->insert_table, $fields, $this->request->get['id']);
            
            $id = $this->request->get['id'];

            $id_key = "$this->insert_table" . "_id";

            $object = $fields;

            $object[$id_key] = $id;

        } else {

            $id = $this->application->insert($formats, $this->insert_table, $fields);

            $id_key = "$this->insert_table" . "_id";

            $object = $fields;

            $object[$id_key] = $id;
            
        }

        foreach($multiselects as $m_fields) {

            $m_fields->set_related($id);
        
            $m_fields->post();

        }

        foreach($this->triggers as $trigger) {

            $trigger->set_id($id);

            $trigger->set_object($this);
            
            $trigger->start();

        }





        header("Location: $full_path_redirect");

        exit;

    }

    
}