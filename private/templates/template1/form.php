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
        
        $this->row = null;
 
        
        $this->init();

    }
    
    function init() {
        
        if (isset($this->request->get['id']) && $this->request->get['id']) {
            
            $this->row = $this->application->find($this->insert_table, $this->request->get['id']);
            
        } 
        
        foreach($this->fields as $field) {
            
            if (isset($this->row[$field->name])) {
                
                $field->set_value($this->row[$field->name]);
                
            } 

            if ($this->session->errors() && $this->session->get_errors($field->name)) {
                
                $field->set_value($this->session->get_errors($field->name)['old_value']);
                
            }
            
        }
        
    }


    // TODO
    function reset() {

        $this->application->drop($this->insert_table);

    }

    // TODO
    function bootstrap() {

        $this->application->migrate($this->insert_table, $this->fields);

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

    function get() {
            

?>

<form action="<?= $this->action_component . '.php?' . $this->request->query_string ?>" method="post" autocomplete="off" >

	<?php foreach($this->fields as $field): ?>
	
	<?= $field->get() ?>
	
	<?php endforeach; ?>

<button class="button" type="submit">Submit</button>
</form>

<?php

        // There errors where cleared
        if ($this->session->errors()) {

            $this->session->clear_errors();

        }

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

        
        // Validation
        $fields_for_validation = $this->fields;

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


            preg_match('/varchar\(([0-9]+)\)/', $field->mysql_type, $matches);

            if (count($matches) > 0 && $matches[1]) {

                if (strlen($this->request->post[$field->name]) > $matches[1]) {

                    array_push($errors, "Lunghezza non valida");

                }

            }

            if ($field->format === 's') {

                //array_push($errors, "Campo non testuale");

            } else if ($field->format === 'i') {

                if (!$field->multiselect) {

                    preg_match('/^[0-9]+$/', $this->request->post[$field->name], $matches);

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

   
        
        // Acl post
        $formats = '';
        
        foreach($this->fields as $field) {
            
            if ('' !== $this->request->post[$field->name]) {
                
                $fields[$field->name] = $this->request->post[$field->name];
                
                $formats .= $field->format;
                
            }
            
        } 
        

        if (isset($this->request->get['id']) && $this->request->get['id']) {

            $this->application->update($formats, $this->insert_table, $fields, $this->request->get['id']);

        } else {

            $this->application->insert($formats, $this->insert_table, $fields);

        }

        header("Location: $success_redirect");

        exit;

    }

    function put() {

    }

    function delete() {
        
    }

    
}