<?php

require_once 'component.php';

class Table extends Component {

    function __construct($request, $session, $application, $fields, $action_page, $selects, $select_table, $joins = [], $similar_columns = [], $referencing = [], $form = null) {

        parent::__construct($request, $session, $application);

        $this->fields = $fields;

        $this->action_page = $action_page;

        $this->selects = $selects;

        $this->select_table = $select_table;

        $this->joins = $joins;

        $this->similar_columns = $similar_columns;

        $this->referencing = $referencing;

        $this->form = $form;

    }

    function reset() {
        
    }


    function bootstrap() {

    }

    function get() {

        if (isset($this->request->get['id']) && isset($this->request->get['delete'])) {

            $this->application->delete($this->select_table, $this->request->get['id']);

        }


        if (isset($this->request->get['unnest'])) {



            $comparator = $this->select_table;

            // It finds uri with equal uri and clear it
            $this->session->clear_output($comparator);


            //
            $key = array_key_first($this->request->get);

            if ($key) {
    
                $this->session->prev_output_set_last_id($this->request->get[$key]);
    
            }
    
            echo $this->session->get_prev_output($this->request->uri);

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


        $fields_names = [];

        $others_fields_names = [];

        foreach($this->fields as $field) {

            if (is_object($field[1])) {

                array_push($others_fields_names, $field[1]->field);

            } else {

                array_push($fields_names, $field[1]);

            }

        }



 

  




        ob_start();

        foreach($this->fields as $field) {

            if (is_object($field[1])) {

                $value = $field[1]->field;

                echo <<<EOT
                <option value="$value">$field[0]</option>
                EOT;

            } else {

                echo <<<EOT
                <option value="$field[1]">$field[0]</option>
                EOT;


            }



        }

        $select_filter_options = ob_get_contents();

        ob_end_clean();


        ob_start();

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

        $output_head = ob_get_contents();

        ob_end_clean();

        $filters = [];

        $text_filters = [];

        foreach($this->request->get as $get_field => $get_value) {

            if (in_array($get_field, $fields_names) || in_array($get_field, $this->selects)) {

                $filters[$get_field] = $get_value;

            } else if (in_array($get_field, $others_fields_names)) {

                $text_filters[$get_field] = $get_value;

            }
            
        }


        $host = $this->application->host;

        $demo_id = $this->request->demo_id;

        $path = $this->application->path;

        //$tables = $this->application->referenced_tables($this->select_table);


        $img_link = $this->application->host . "/img/link_FILL0_wght700_GRAD0_opsz48.png";


        $rows_filtered = [];

        $rows = [0];

        $offset = 0;

        if (isset($this->request->get['page'])) {

            $offset = $this->request->get['page'] * 4;

        }

        while (count($rows_filtered) < 4 && count($rows) !== 0) {

            $rows = $this->application->select($this->select_table, $this->joins, $filters, $this->selects, true, $offset);

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

        





        ob_start();

        foreach($rows_filtered as $row) {

            $id = $row[$this->select_table . '_id'];

            echo <<<EOT
            <tr class="enabled-$id">
            EOT;

            foreach($this->fields as $field) {

                $td = '';

                if (is_object($field[1])) {

                    $td = $row[$field[1]->field];

                } else {

                    $td = $row[$field[1]];

                }

                /*
                if (isset($field[2]) && isset($field[3])) {
                    
                    $field_id = $row[$field[3]];

                    $label = $row[$field[1]];

                    $color = 'ffeb3bd6';

                    if (isset($field[4])) {

                        $color = 'style="#' . $field[4] . '"';

                    }
                    
                    $td = <<<EOT
                    <a class="link" href="$host/$path/$field[2]?$field[3]=$field_id"><span class="circle" $color><img class="invert icon" src="$img_link"></span> $label</a>
                    EOT;

                }
                */

                echo <<<EOT
                <td><span>$field[0]</span>$td</td>
                EOT;

            }



            $script_name = $this->request->script_name;

            // Clear query string
            $query_string = $this->request->delete_query_string_param($this->request->query_string, 'delete');

            $query_string = $this->request->update_query_string_param($query_string, 'nest', 'unnest', '');

            $query_string = $this->request->set_query_string_param($query_string, 'id', $id);

            $query_string_update = $this->request->set_query_string_param($query_string, 'popup', '');

            $action_update = "$host$script_name$query_string_update";

            $query_string_delete = $this->request->set_query_string_param($query_string, 'delete', '');

            $action_delete = "$host$script_name$query_string_delete";

            $img_edit = $this->application->host . "/img/edit_FILL0_wght700_GRAD0_opsz48.png";

            $img_delete = $this->application->host . "/img/delete_FILL0_wght700_GRAD0_opsz48.png";

            // Td for actions
            echo <<<EOT
            <td class="table-action-section"><a href="$action_update" class="button-a"><img class="icon invert" src="$img_edit"> Modifica</a><a class="button-a" style="margin-left: 4px;" href="$action_delete"><img class="icon invert" src="$img_delete"> Elimina</a>
            EOT;

            // Td for references
            //foreach($tables as $table) {
    
                //$table_matched = $this->find_referencing_table($table['table_name']);
            
            foreach($this->referencing as $table) {

                $table_matched = $table;

                if ($table_matched) {

                    $page = $table_matched[1];
    
                    $query_string = $table_matched[3] . "=$id&nest";
    
                    $label = $table_matched[2];

                    $host = $this->application->host;

                    $color = 'ffeb3bd6';

                    if (isset($table_matched[3])) {

                        $color = 'style="#' . $table_matched[3] . '"';

                    }

                    echo <<<EOT
                    <a class="link" href="$host/$path/$page.php?$query_string" style="margin-left: 4px;"><span class="circle" $color><img class="invert icon" src="$img_link"></span> $label</a>
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

        $output_body = ob_get_contents();

        ob_end_clean();

        $prefix = $this->select_table;

        $onsubmit = "event.preventDefault(); let filter = document.getElementById('$prefix-filter'); let filter_value = document.getElementById('$prefix-filter-value'); let new_url = window.location.href;";

        $onsubmit .= " new_url = url_with_new_param(new_url, filter.value, filter_value.value);";

        $onsubmit .= " window.location.href = new_url;";


        ob_start();

        foreach($this->request->get as $get_field => $get_value) {
        
            foreach($this->fields as $field) {

                if (is_object($field[1])) {

                    $field_name = $field[1]->field;

                    $label = $field[0];

                } else {

                    $field_name = $field[1];

                    $label = $field[0];

                }

                if ($field_name === $get_field) {

                    $value = $get_value;

                    echo <<<EOT
                    <a style="text-decoration: none;" href="" onclick="event.preventDefault(); window.location.href = remove_url_param(window.location.href, '$field_name');"><span>$label: $value</span></a>
                    EOT;

                }

                

            }

        }

        $applied_filters = ob_get_contents();

        ob_end_clean();

        $img_close = $this->application->host . "/img/close_FILL0_wght700_GRAD0_opsz48.png";

        $img_search = $this->application->host . "/img/search_FILL0_wght700_GRAD0_opsz48.png";

        $img_add = $this->application->host . "/img/add_FILL0_wght700_GRAD0_opsz48.png";

        $query_string_tags = [];

        foreach($this->request->get as $get_field => $get_value) {

            if (!in_array($get_field, ['add', 'ajax', 'options', 'id', 'nested'])) {

                $filters[$get_field] = $get_value;

                array_push($query_string_tags, "$get_field=$get_value");

            }

            

        }
        

        $popup_content = '';

        if (null !== $this->form && isset($this->request->get['popup'])) {

            $button_close_popup_query_string = $this->request->delete_query_string_param($this->request->query_string, 'delete');
            $button_close_popup_query_string = $this->request->update_query_string_param($button_close_popup_query_string, 'nest', 'unnest', '');
            $button_close_popup_query_string = $this->request->delete_query_string_param($button_close_popup_query_string, 'id');
            $button_close_popup_query_string = $this->request->delete_query_string_param($button_close_popup_query_string, 'popup');

            $popup_content .= <<<EOT
            <div id="overlay">
            <div id="popup">
            <div style="text-align: right;">
            <a href="$button_close_popup_query_string" class="close-button"><img src="$img_close" alt=""></a>
            </div>
            <div id="popup-content">

            EOT;

            ob_start();

            $this->form->get();

            $content = ob_get_contents();

            ob_end_clean();

            $popup_content .= $content;

            $popup_content .= <<<EOT
            </div>
            </div>
            </div>
            EOT;

        }


        

        $script_name = $this->request->script_name;

        // Clear query string
        $query_string = $this->request->delete_query_string_param($this->request->query_string, 'delete');
        
        $query_string = $this->request->update_query_string_param($query_string, 'nest', 'unnest', '');

        $query_string = $this->request->delete_query_string_param($query_string, 'id');

        $button_aggiungi_query_string = $this->request->set_query_string_param($query_string, 'popup', '');

        
        $button_aggiungi = <<<EOT
        <a href="$button_aggiungi_query_string" class="button-a">Aggiungi</a>
        EOT;


        $img_arrow_down = $this->application->host . "/img/arrow_drop_up_FILL0_wght700_GRAD0_opsz48.png";

        $img_arrow_up = $this->application->host . "/img/arrow_drop_down_FILL0_wght700_GRAD0_opsz48.png";

        $page_number = 0;

        if (isset($this->request->get['page'])) {

            $page_number = $this->request->get['page'];

        }

        $up_page_number = $page_number + 1;
        
        $query_string = $this->request->set_query_string_param($this->request->query_string, 'page', $up_page_number);

        $button_arrow_up_uri = "$host$script_name$query_string";

        $down_page_number = $page_number - 1;

        $query_string = $this->request->set_query_string_param($this->request->query_string, 'page', $down_page_number--);

        $button_arrow_down_uri = "$host$script_name$query_string";

        $output = <<<EOT
        <form class="filter-form" onsubmit="$onsubmit">
        <div>
        <select id="$prefix-filter" name="filter">
        <option value="">...</option>
        $select_filter_options
        </select>
        <input type="text" id="$prefix-filter-value" name="filter_value">
        <button class="button" type="submit"><img class="icon invert" src="$img_search">Cerca</button>
        </div>
        </form>
        <div class="filters-labels">
        $applied_filters
        </div>
        <!-- custom content -->
        <div class="table-container">
        $button_aggiungi
        $popup_content
        <div class="scrollable">
        <table class="table">
        <thead>
        $output_head
        </thead>
        <tbody>
        $output_body
        </tbody>
        </table>
        <div class="scrollable-buttons">
        <a class="button-a arrows" href="$button_arrow_down_uri" style="margin-top: 52px;"><img class="icon invert" src="$img_arrow_down"></a>
        <a class="button-a arrows" href="$button_arrow_up_uri"><img class="icon invert" src="$img_arrow_up"></a>
        </div>  
        </div>
        </div>
        <!-- end custom content -->
        EOT;

        if (!isset($this->request->get['unnest'])) {

            $uri = $this->request->uri;

            $comparator = $this->select_table;

            $this->session->set_prev_output($output, $uri, $comparator);

        }

        echo $output;

    }

    private function find_referencing_table($table_to_match) {

        $matched = null;

        foreach($this->referencing as $table) {

            if (isset($table[0]) && $table[0] == $table_to_match) {

                $matched = $table;

            }

        }

        return $matched;

    }

    function post() {

    }

    function delete() {

    }
    
}