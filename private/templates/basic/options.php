<?php

require_once 'component.php';

class Options extends Component {

    function __construct($request, $session, $application) {

        parent::__construct($request, $session, $application);

    }

    function set_dataset_table($dataset_table) {

        $this->dataset_table = $dataset_table;

    }

    function set_dataset_id($dataset_id) {

        $this->dataset_id = $dataset_id;

    }

    function set_dataset_label($dataset_label) {

        $this->dataset_label = $dataset_label;

    }

    function set_view($view) {

        $this->view = $view;

    }

    function set_multiselect($multiselect) {

        $this->multiselect = $multiselect;

    }

    function get_scripts() {

    }

    function reset() {
        
    }

    function bootstrap() {

    }

    function get() {



        $filters = [];

        // Filter for edit
        if ( property_exists($this, 'parent') && $this->view && isset($this->view[2]) && $this->parent[$this->view[2][0]]) {

            $filters[$this->view[2][1]] = $this->parent[$this->view[2][0]];

        }

        // Filter for edit and create
        if ($this->view && isset($this->view[2]) && isset($this->request->get[$this->view[2][0]])) {

            $filters[$this->view[2][1]] = $this->request->get[$this->view[2][0]];
        
        }


        $records = [];
    
        if ($this->view && isset($this->view[1])) {

            $records = $this->application->select($this->view[0], $this->view[1], $filters);

        } else {

            $records = $this->application->select($this->dataset_table, [], $filters);
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

            // Clear onchange
            $onchange = '';

            $name = $this->name . '[]';
        }

        $onchange = '';

        echo <<<EOT
        <!-- custom content -->
        $options_output
        <!-- end custom content -->
        EOT;

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
    
}