<?php

require_once 'relation.php';

class Table extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->fields = $params['fields'];

        $this->action_page = $params['action_page'];

        $this->selects = $params['select'];

        $this->select_table = $params['from'];

        $this->joins = $params['joins'];

        $this->referencing = $params['referencing'];

        $this->form = $params['form'];

    }



    function delete1() {

        $this->application->delete_record($this->select_table, $this->request->get['id']);

        $redirect = $this->request->referer;

        header("Location: $redirect");

        exit;

    }




    private function print_list_stack() {

        if (isset($this->request->get['unnest'])) {

            $comparator = $this->select_table;

            $this->session->clear_output($comparator);
    
            echo $this->session->get_prev_output($comparator);

        } else if (isset($this->request->get['nest'])) {

            $key = array_key_first($this->request->get);

            if ($key) {
    
                $this->session->prev_output_set_last_id($this->request->get[$key]);
    
            }

            $comparator = $this->select_table;
    
            echo $this->session->get_prev_output($comparator);

        } else {

            $this->session->clear_all_output();

        }

    }

    private function push_list_stack($output) {

        if (!isset($this->request->get['unnest'])) {

            $uri = $this->request->uri;

            $comparator = $this->select_table;

            $this->session->set_prev_output($output, $uri, $comparator);

        }

    }


    // Print filter form header
    function print_filter_form_header() {

        $prefix = $this->select_table;

        $onsubmit = "event.preventDefault(); let filter = document.getElementById('$prefix-filter'); let filter_value = document.getElementById('$prefix-filter-value'); let new_url = window.location.href;";

        $onsubmit .= " let operator = document.getElementById('$prefix-operator');";

        $onsubmit .= " if (!url_exists_param(new_url, filter.value + '[0]')) {";

        $onsubmit .= " new_url = url_with_new_param(new_url, filter.value + '[0]', filter_value.value);";

        $onsubmit .= " new_url = url_with_new_param(new_url, filter.value + '[1]', operator.value);";

        $onsubmit .= " }";

        $onsubmit .= " else {";

        $onsubmit .= " new_url = url_with_new_param(new_url, filter.value + '[2]', filter_value.value);";

        $onsubmit .= " new_url = url_with_new_param(new_url, filter.value + '[3]', operator.value);";

        $onsubmit .= " }";

        $onsubmit .= " window.location.href = new_url;";

        echo <<<EOT
        <form class="filter-form" onsubmit="$onsubmit">
        <div>
        <select id="$prefix-filter" name="filter">
        <option value="">...</option>
EOT;

    }

    // Print option for filters
    function print_options_for_filters($authorized_fields = null) {


        foreach($this->fields as $field) {

            if (is_object($field[1])) {

                $value = $field[1]->field;

                if ($authorized_fields && in_array($value, $authorized_fields)) {

                    echo <<<EOT
                    <option value="$value">$field[0]</option>
EOT;

                } else {

                    echo <<<EOT
                    <option value="$value">$field[0]</option>
EOT;

                }

            } else {

                if ($authorized_fields) {

                    if (in_array($field[1], $authorized_fields)) {

                        echo <<<EOT
                        <option value="$field[1]">$field[0]</option>
EOT;

                    }

                } else {

                    echo <<<EOT
                    <option value="$field[1]">$field[0]</option>
EOT;

                }



            }

        }

    }

    // Print filter form footer
    function print_filter_form_footer() {

        $prefix = $this->select_table;

        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $img_search = "$application_host/$application_path/img/search_FILL0_wght700_GRAD0_opsz48.png";

        echo <<<EOT
        </select>
        <select id="$prefix-operator" name="operator">
            <option value="like">Like</option>
            <option value="eq">=</option>
            <option value="lt"><</option>
            <option value="gt">></option>
            <option value="let"><=</option>
            <option value="get">>=</option>
        </select>
        <input type="text" id="$prefix-filter-value" name="filter_value">
        <button class="button" type="submit">Cerca</button>
        </div>
        </form>
EOT;

    }

    // Print applied filter
    function print_applied_filter() {

        echo <<<EOT
        <div class="filters-labels">
EOT;

        foreach($this->request->get as $get_field => $get_values) {

            foreach($this->fields as $field) {

                if (is_array($get_values) && isset($get_values[0]) && isset($get_values[1])) {

                    $get_value = $get_values[0];

                    $operator = $get_values[1];

                    $label = '';

                    if (is_object($field[1])) {

                        $field_name = $field[1]->field;

                        $label = $field[0];

                    } else {

                        $field_name = $field[1];

                        $label = $field[0];

                    }

                    $complete_label = "$label $operator $get_value";

                    if (isset($get_values[2]) && isset($get_values[3])) {

                        $get_value = $get_values[2];

                        $operator = $get_values[3];

                        $complete_label .= " and $operator $get_value";

                    }

                    if ($field_name === $get_field) {

                        $value = $get_value;

                        echo <<<EOT
                        <a style="text-decoration: none;" href="" onclick="event.preventDefault(); let new_url = remove_url_param(window.location.href, '$field_name' + '[0]'); new_url = remove_url_param(new_url, '$field_name' + '[1]'); new_url = remove_url_param(new_url, '$field_name' + '[2]'); window.location.href = remove_url_param(new_url, '$field_name' + '[3]');"><span>$complete_label</span></a>
EOT;

                    }


                }

                

            }

        }

        echo <<<EOT
        </div>
EOT;

    }

    // Print header table
    function print_header_table_container() {

        $script_name = $this->application->script_name;

        // Clear query string
        $query_string = $this->request->delete_query_string_param($this->request->query_string, 'delete');
        
        $query_string = $this->request->update_query_string_param($query_string, 'nest', 'unnest', '');

        $query_string = $this->request->delete_query_string_param($query_string, 'id');

        $button_aggiungi_query_string = $this->request->set_query_string_param($query_string, 'popup', '');

        
        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $action_page = $this->action_page;

        $mobile_url = "$application_host/$application_path/$action_page.php";

        $onclick = <<<EOT
        event.preventDefault();
        let new_href = this.href;
        if (window.innerWidth < 768) {
            console.log('$mobile_url');
            new_href = '$mobile_url';
            new_href = url_with_new_param(new_href, 'mobile', '');
        }
        window.location.href = new_href;
EOT;
        
        // HEREDOC FIX
        echo '<div class="table-container"><a href="' . $button_aggiungi_query_string. '" class="button-a" onclick="' . $onclick. '">Aggiungi</a>';    
        
    }

    // Print popup form
    function print_popup_form() {
        
        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $img_close = "$application_host/$application_path/img/close_FILL0_wght700_GRAD0_opsz48.png";

        $button_close_popup_query_string = $this->request->delete_query_string_param($this->request->query_string, 'delete');
        $button_close_popup_query_string = $this->request->update_query_string_param($button_close_popup_query_string, 'unnest', 'nest', '');
        $button_close_popup_query_string = $this->request->delete_query_string_param($button_close_popup_query_string, 'id');
        $button_close_popup_query_string = $this->request->delete_query_string_param($button_close_popup_query_string, 'popup');

        echo <<<EOT
        <div id="overlay">
        <div id="popup">
        <div style="text-align: right;">
        <a href="$button_close_popup_query_string" class="close-button"><img src="$img_close" alt=""></a>
        </div>
        <div id="popup-content">
EOT;

        $this->form->get();

        echo <<<EOT
        </div>
        </div>
        </div>
EOT;

    }

    // Print header table
    function print_header_table() {

        echo <<<EOT
        <div class="scrollable">
        <table class="table">
        <thead>
EOT;

        foreach($this->fields as $field) {

            $th = $field[0];

            echo <<<EOT
            <th>$th</th>
EOT;

        }

        // Th for actions
        echo <<<EOT
        <th></th>
EOT;

        // Th for references
        echo <<<EOT
        <th></th>
EOT;

        echo <<<EOT
        </thead>
EOT;

    }

    // Print body table
    function print_body_table($authorized_fields = null, $creators = null) {

        $selects = $this->selects;

        if ($authorized_fields) {
            
            $fields = [];

            foreach($this->fields as $field) {

                if (in_array($field[1], $authorized_fields)) {

                    array_push($fields, $field);

                }

            }
            
            $this->fields = $fields;

        } 

        $fields_names = [];

        $others_fields_names = [];

        foreach($this->fields as $field) {

            if (is_object($field[1])) {

                array_push($others_fields_names, $field[1]->field);

            } else {

                array_push($fields_names, $field[1]);

            }

        }

        $filters = [];

        $text_filters = [];

        foreach($this->request->get as $get_field => $get_values) {

            if (in_array($get_field, $fields_names) || in_array($get_field, $selects)) {

                $filters[$get_field] = $this->get_query_param_part($get_field, $get_values);

            } else if (in_array($get_field, $others_fields_names)) {

                $text_filters[$get_field] = $this->get_query_param_part($get_field, $get_values);

            }
            
        }


        $rows_filtered = [];

        $rows = [0];

        $offset = 0;

        if (isset($this->request->get['page'])) {

            $offset = $this->request->get['page'] * 4;

        }

        while (count($rows_filtered) < 4 && count($rows) !== 0) {

            $rows = $this->application->select($this->select_table, $this->joins, $filters, $selects, true, $offset, $creators);

            foreach($rows as $row) {

                $new_row = $row;
    
                $check = true;
    
                foreach($this->fields as $field) {
                    
                    if (is_object($field[1])) {
    
                        $id_field = $field[1]->id_field;
    
                        $prefix_field = $field[1]->prefix_field;
    
                        $field[1]->set_id($row[$id_field]);
    
                        $field[1]->set_prefix($row[$prefix_field]);
    
                        $td = $field[1]->get();
    
                        $new_row[$field[1]->field] = $td;
    
                        if (isset($text_filters[$field[1]->field])) {
    
                            $words = explode(' ', $text_filters[$field[1]->field]);
        
                            foreach($words as $word) {
    
                                if (!strpos($td, $word)) {
                                    
                                    $check = false;
            
                                }
            
                            }
    
                        }
    
    
    
                    }
    
                }
    
    
                if ($check && count($rows_filtered) < 4) {
    
                    array_push($rows_filtered, $new_row);
    
                }
    
            }

            $offset = $offset + 4;

        }

        


        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $img_link = "$application_host/$application_path/img/link_FILL0_wght700_GRAD0_opsz48.png";

        echo <<<EOT
        <tbody>
EOT;

        foreach($rows_filtered as $row) {

            $id = $row[$this->select_table . '_id'];

            echo <<<EOT
            <tr data-id="$id">
EOT;

            foreach($this->fields as $field) {

                $td = '';

                if (is_object($field[1]) && isset($row[$field[1]->field])) {

                    $td = $row[$field[1]->field];

                } else if (isset($row[$field[1]])) {

                    $td = $row[$field[1]];

                }

                echo <<<EOT
                <td><span>$field[0]</span>$td</td>
EOT;

            }



            $script_name = $this->application->script_name;


            // Clear query string
            $query_string = $this->request->delete_query_string_param($this->request->query_string, 'delete');

            $query_string = $this->request->update_query_string_param($query_string, 'nest', 'unnest', '');

            $query_string = $this->request->set_query_string_param($query_string, 'id', $id);

            $query_string_update = $this->request->set_query_string_param($query_string, 'popup', '');

            $action_update = "$application_host$script_name$query_string_update";

            $query_string_delete = $this->request->set_query_string_param($query_string, 'delete', '');

            $action_delete = "$application_host$script_name$query_string_delete";

            $img_edit = "$application_host/$application_path/img/edit_FILL0_wght700_GRAD0_opsz48.png";

            $img_delete = "$application_host/$application_path/img/delete_FILL0_wght700_GRAD0_opsz48.png";


            $action_page = $this->action_page;

            $mobile_url = "$application_host/$application_path/$action_page.php$query_string_update";

            $onclick_update = <<<EOT
            event.preventDefault();
            let new_href = this.href;
            if (window.innerWidth < 768) {
                new_href = '$mobile_url';
                new_href = url_with_new_param(new_href, 'mobile', '');
            }
            window.location.href = new_href;
EOT;

            // Td for actions
            echo <<<EOT
            <td class="table-action-section"><a href="$action_update" class="button-a" onclick="$onclick_update">Edit</a><a class="button-a" style="margin-left: 4px;" href="$action_delete">Delete</a>
EOT;

            // Td for references            
            foreach($this->referencing as $table) {

                $table_matched = $table;

                if ($table_matched) {

                    $page = $table_matched[1];
    
                    $query_string = $table_matched[3] . "=$id&nest";
    
                    $label = $table_matched[2];

                    $color = 'ffeb3bd6';

                    if (isset($table_matched[3])) {

                        $color = 'style="#' . $table_matched[3] . '"';

                    }

                    echo <<<EOT
                    <a class="link" href="$application_host/$application_path/$page.php?$query_string" style="margin-left: 4px;"><span class="circle" $color><img class="invert icon" src="$img_link"></span> $label</a>
EOT;
    
                }
    
            }
    
            echo <<<EOT
            </td>
EOT;

            echo <<<EOT
            </tr>
EOT;

        }

        echo <<<EOT
        </tbody>
EOT;

    }

    function print_footer_table() {

        $script_name = $this->application->script_name;

        $application_host = $this->application->host;

        $application_path = $this->application->path;

        $img_arrow_down = "$application_host/$application_path/img/arrow_drop_up_FILL0_wght700_GRAD0_opsz48.png";

        $img_arrow_up = "$application_host/$application_path/img/arrow_drop_down_FILL0_wght700_GRAD0_opsz48.png";

        $page_number = 0;

        if (isset($this->request->get['page']) && $this->request->get['page']) {

            $page_number = $this->request->get['page'];

        }

        $up_page_number = $page_number + 1;
        
        $query_string = $this->request->set_query_string_param($this->request->query_string, 'page', $up_page_number);

        $button_arrow_up_uri = "$application_host$script_name$query_string";

        $down_page_number = $page_number - 1;

        $query_string = $this->request->set_query_string_param($this->request->query_string, 'page', $down_page_number--);

        $button_arrow_down_uri = "$application_host$script_name$query_string";

        echo <<<EOT
        </table>
        <div class="scrollable-buttons">
        <a class="button-a arrows" href="$button_arrow_down_uri" style="margin-top: 52px;"><img class="icon invert" src="$img_arrow_down"></a>
        <a class="button-a arrows" href="$button_arrow_up_uri"><img class="icon invert" src="$img_arrow_up"></a>
        </div>  
        </div>
        </div>
EOT;

    }


    private function print_list($authorized_fields = null, $creators = null) {

        $this->print_filter_form_header();

        $this->print_options_for_filters($authorized_fields);

        $this->print_filter_form_footer();

        $this->print_applied_filter();

        $this->print_header_table_container();


        if ($this->form && isset($this->request->get['popup'])) {
            
            $this->print_popup_form();

        }

        $this->print_header_table();

        $this->print_body_table($authorized_fields, $creators);

        $this->print_footer_table();

    }

    function get() {
        
        $this->update_authorized_fields();
        
        $this->update_creators();

        $this->print_list_stack();

        $output = '';

        // Delete
        if (isset($this->request->get['id']) && $this->request->get['id'] && isset($this->request->get['delete'])) {

            if ($this->auth) {
                
                
                // ACL delete
                $resource = $this->auth['resource'];
                
                $users_table_name = $this->auth['acl']['users_table_name'];
                
                $cruds_table_name = $this->auth['acl']['cruds_table_name'];
                
                $cd_authorized = $this->authorized_cd($users_table_name, $resource, $cruds_table_name);
                
                if(false !== $cd_authorized) {
                    
                    $this->delete1();
                    
                } else {
                    
                    $errors = [];
                    
                    array_push($errors, 'Permesso negato.');
                    
                    $this->session->push_errors('popup_errors', [
                        'errors' => $errors
                    ]);
                    
                    
                }
                
            } else {
                
                $this->delete1();
                
            }
            

            

            
        
        }
          
        // List
        if ($this->auth) {

            $authorized_fields = null;
            
            $creators = null;
            
            if (property_exists($this, 'authorized_fields')) {
                
                $authorized_fields = [];
                
                if ($this->authorized_fields) {
                    
                    if (count($this->authorized_fields) > 0) {
                        
                        foreach($this->fields as $field) {
                            
                            if (in_array($field[1], $this->authorized_fields)) {
                                
                                array_push($authorized_fields, $field[1]);
                                
                            }
                            
                        }
                        
                    } else {
                        
                        foreach($this->fields as $field) {
                            
                            array_push($authorized_fields, $field[1]);
                            
                        }
                        
                        
                    }
                    
                } else {
                    
                    foreach($this->fields as $field) {
                        
                        array_push($authorized_fields, $field[1]);
                        
                    }
                                            
                }
                     
            }
            
            if (property_exists($this, 'creators')) {
                
                $creators = [];
                
                if ($this->creators && count($this->creators) > 0) {
                    
                    $creators = $this->creators;
                    
                }
                   
            }
            
            if (null !== $authorized_fields && null !== $creators) {
                
                ob_start();
                $this->print_list($authorized_fields, $creators);
                $output = ob_get_contents();
                ob_end_clean();
                echo $output;
                
            }
            
        } else {
            
            ob_start();
            $this->print_list();
            $output = ob_get_contents();
            ob_end_clean();
            echo $output;
            
        }
        

        $this->push_list_stack($output);

        $this->print_errors_popup();
        
    }

    function post() {

    }

    function put() {

    }

    function delete() {

    }
    
}