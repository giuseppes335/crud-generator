<?php

require_once 'relation.php';

class Form extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->fields = $params['fields'];

        $this->action_component = $params['action_component'];

        $this->insert_table = $params['insert_table'];

        $this->redirect_component = $params['redirect_component'];

        $this->seed = $params['seed'];

    }


    // TODO
    function reset() {

        $this->application->drop($this->insert_table);

    }

    // TODO
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
            
            $column_index = 0;

            foreach($this->fields as $column) {

                if (isset($record[$column_index + 1])) {

                    $row[$column->name] = $record[$column_index + 1];

                    $formats .= $column->format;

                }
                
                $column_index++;
                
            }

            $creator_id = null;
            
            if (isset($record[count($this->fields) + 1])) {
                
                $creator_id = $record[count($this->fields) + 1];
                
            }

            $this->application->insert($formats, $this->insert_table, $row, $creator_id);

        }


    }

    function get1($authorized_fields = null) {
            
        ob_start();

        $action = $this->action_component . '.php';

        $action .= '?' . $this->request->query_string;

        echo <<<EOT
        <form action="$action" method="post" autocomplete="off" >
        <!--<div style="display: flex; flex-direction: column;">-->
EOT;




        
        $row = [];

        if (isset($this->request->get['id']) && $this->request->get['id']) {

            $row = $this->application->find($this->insert_table, $this->request->get['id']);
        
        } 

        foreach($this->request->get as $get_field => $get_value) {

            if (in_array($get_field, array_column($this->fields, 'name'))) {

                $row[$get_field] = $get_value;

            }

        }

        $counter = 0;

        foreach($this->fields as $field) {




            // More row forms
            /*
            $check_open_div = false;

            $check_close_div = false;


            if ($counter % 3 === 0) {

                $check_open_div = true;

            } else if ($counter % 3 === 2) {

                $check_close_div = true;

            }

            if ($check_open_div) {

                echo <<<EOT
                <div style="display: flex;">
                EOT;

            }
            */
            //





            if (method_exists($field, 'set_parent') && isset($this->request->get['id']) && $this->request->get['id']) {

                $field->set_parent($row);

            }


            // Check for multiselect
            // Set values (plural)
            if (property_exists($field, 'multiselect') && $field->multiselect) {

                if (isset($this->request->get['id']) && $this->request->get['id']) {

                    $options = $this->application->select($field->multiselect[0], [], [
                        $field->multiselect[2] => $this->get_query_param_part($field->multiselect[2], [$this->request->get['id'], 'eq'])
                    ]);

                    $values = array_column($options, $field->multiselect[1]);

                    $field->set_values($values);

                }

            // Or set value (singular)
            } else {


                if (isset($row[$field->name])) {

                    $field->set_value($row[$field->name]);

                } 


            }


            if ($this->session->errors() && $this->session->get_errors($field->name)) {

                $field->set_value($this->session->get_errors($field->name)['old_value']);

            }




            if ($authorized_fields) {

                if (in_array($field->name, $authorized_fields)) {

                    $field->get();

                }

            } else {

                $field->get();

            }

            


            // Inside this errors where rendered
            /*
            echo <<<EOT
            <div style="width: 33%; margin: 4px;">
            EOT;
            $field->get();
            echo <<<EOT
            </div>
            EOT;


            
            if ($check_close_div) {

                echo <<<EOT
                </div>
                EOT;

            }

            $counter++;
            */


        }

        /*
        if (!$check_close_div) {

            echo <<<EOT
            </div>
            EOT;

        }
        */

        // There errors where cleared
        if ($this->session->errors()) {

            $this->session->clear_errors();

        }



        echo <<<EOT
        <!--</div>-->
        <button class="button" type="submit">Submit</button>
        </form>
EOT;

    }

    function get() {
        
        $this->update_authorized_fields();
        
        $this->update_creators();
        
        if ($this->auth) {
            
            $authorized_fields = null;
            
            $creators = null;
            
            if (property_exists($this, 'authorized_fields')) {
                
                $authorized_fields = [];
                
                if ($this->authorized_fields) {
                    
                    if (count($this->authorized_fields) > 0) {
                        
                        foreach($this->fields as $field) {
                            
                            if (in_array($field->name, $this->authorized_fields)) {
                                
                                array_push($authorized_fields, $field->name);
                                
                            }
                            
                        }
                        
                    } else {
                        
                        foreach($this->fields as $field) {
                            
                            array_push($authorized_fields, $field->name);
                            
                        }
                        
                        
                    }
                    
                } else {
                    
                    foreach($this->fields as $field) {
                        
                        array_push($authorized_fields, $field->name);
                        
                    }
                    
                }
                
            }
            
            if (property_exists($this, 'creators')) {
                
                $creators = [];
                
                if ($this->creators && count($this->creators) > 0) {
                    
                    $creators = $this->creators;
                    
                }
                
            }
            
            // Else it will go on error
            if (null !== $authorized_fields) {
                
                if (null !== $creators) {
                    
                    $this->get1($authorized_fields);
                    
                } else {
                    
                    $errors = [];
                    
                    array_push($errors, 'Permesso negato.');
                    
                    $this->session->push_errors('popup_errors', [
                        'errors' => $errors
                    ]);
                    
                }
                
                
                
            }
            
        } else {
            
            $this->get1();
            
        }

        $this->print_errors_popup();
        
    }


    function post() {
        
        // Redirect
        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $redirect = $this->redirect_component . '.php';

        
        $success_redirect = '';

        $error_redirect = '';

        if (isset($this->request->get['mobile'])) {
            
            $success_redirect = "$application_host/$application_path/$redirect";

            $error_redirect = $this->request->referer;

        } else {

            $success_redirect = $this->request->referer;

            $error_redirect = $this->request->referer;

        }

        
        
        $this->update_authorized_fields();
        
        $fields_for_validation = $this->fields;
        
        if ($this->auth) {
            
            $authorized_fields = null;
            
            if (property_exists($this, 'authorized_fields')) {
                
                $authorized_fields = [];
                
                if ($this->authorized_fields) {
                    
                    if (count($this->authorized_fields) > 0) {
                        
                        foreach($this->fields as $field) {
                            
                            if (in_array($field->name, $this->authorized_fields)) {
                                
                                array_push($authorized_fields, $field);
                                
                            }
                            
                        }
                        
                    } else {
                        
                        foreach($this->fields as $field) {
                            
                            array_push($authorized_fields, $field);
                            
                        }
                        
                        
                    }
                    
                } else {
                    
                    foreach($this->fields as $field) {
                        
                        array_push($authorized_fields, $field);
                        
                    }
                    
                }
                
            }
            
            if (null !== $authorized_fields) {
                
                $fields_for_validation = $authorized_fields;
                
            }
            
        } 
        
     

        // Validation
        foreach($fields_for_validation as $field) {

            // Not necessary to test isset($this->request->post[$field->name]) to check validation

            $errors = [];

            if (false !== strpos($field->nullable, 'not null') && $this->request->post[$field->name] === '') {

                array_push($errors, "Campo richiesto");

            }

            if (false !== strpos($field->nullable, 'unique')) {
                

                $rows = $this->application->select($this->insert_table, [], [$field->name => $this->get_query_param_part($field->name, [$this->request->post[$field->name], 'eq'])]);

                if (isset($this->request->get['id']) && $this->request->get['id']) {

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


            preg_match('/varchar\(([1-9][0-9]*)\)/', $field->mysql_type, $matches);

            if (count($matches) > 0 && $matches[1]) {

                if (strlen($this->request->post[$field->name]) > $matches[1]) {

                    array_push($errors, "Lunghezza non valida");

                }

            }

            if ($field->format === 's') {

                //array_push($errors, "Campo non testuale");

            } else if ($field->format === 'i') {

                if (!$field->multiselect) {

                    preg_match('/^[1-9][0-9]*$/', $this->request->post[$field->name], $matches);

                    if (count($matches) === 0) {

                        array_push($errors, "Campo non intero");

                    }

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

            header("Location: $error_redirect");

            exit;

        } 
        //

        $formats = '';

        $fields = [];

        $multiselects = [];

        foreach($this->fields as $field) {

            // Check for multiselect
            if (!$field->multiselect) {

                if (property_exists($field, 'is_password') && (boolean)$field->is_password) {

                    $fields[$field->name] = password_hash($this->request->post[$field->name], PASSWORD_DEFAULT);

                } else {

                    $fields[$field->name] = $this->request->post[$field->name];

                }

                $formats .= $field->format;

            } else {

                array_push($multiselects, $field);

            }

           

        }
        
        
        $this->update_authorized_fields(false);
        
        $this->update_creators();
        
        $authorized_update = true;
        
        if ($this->auth) {
            
            $authorized_fields = null;
            
            if (property_exists($this, 'authorized_fields')) {
                
                $authorized_fields = [];
                
                if ($this->authorized_fields) {
                    
                    if (count($this->authorized_fields) > 0) {
                        
                        foreach($this->fields as $field) {
                            
                            if (in_array($field->name, $this->authorized_fields)) {
                                
                                array_push($authorized_fields, $field->name);
                                
                            }
                            
                        }
                        
                    } else {
                        
                        foreach($this->fields as $field) {
                            
                            array_push($authorized_fields, $field->name);
                            
                        }
                        
                        
                    }
                    
                } else {
                        
                    foreach($this->fields as $field) {
                        
                        array_push($authorized_fields, $field->name);
                        
                    }
                    
                }
                
            } else {
                
                $authorized_update = false;
                
            }
            
            if (null !== $authorized_fields) {
                
                foreach($fields as $field => $value) {
                    
                    if (!in_array($field, $authorized_fields)) {
                        
                        $authorized_update = false;
                        
                    }
                    
                }
                
            }
            
        } 
        
        
        if (isset($this->request->get['id']) && $this->request->get['id']) {
            
            $row = $this->application->find($this->insert_table, $this->request->get['id']);
            
            if (property_exists($this, 'creators')) {
                
                $creator_field = $this->insert_table . '_creator';
                
                if (!in_array($row[$creator_field], $this->creators)) {
                    
                    $authorized_update = false;
                    
                }
                
            }
            
            
        }
        
        

        // Acl post
        if(!$authorized_update) {
            
            $errors = [];
            
            array_push($errors, 'Record non salvato. Permesso negato.');
            
            $this->session->push_errors('popup_errors', [
                'errors' => $errors
            ]);
            
            header("Location: $error_redirect");
            
            exit;
            
        }

        $id = null;

        if (isset($this->request->get['id']) && $this->request->get['id']) {

            $this->application->update($formats, $this->insert_table, $fields, $this->request->get['id']);
            
            $id = $this->request->get['id'];

        } else {

            $creator_id = null;

            if ($this->auth) {

                $creator_id = $this->session->get_logged_user();

            }

            $id = $this->application->insert($formats, $this->insert_table, $fields, $creator_id);

        }

        foreach($multiselects as $m_fields) {

            $m_fields->set_related($id);
        
            $m_fields->post();

        }

        header("Location: $success_redirect");

        exit;

    }

    function put() {

    }

    function delete() {
        
    }

    
}