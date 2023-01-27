<?php

require_once 'component.php';

class Aggregation extends Component {

    function __construct($request, $session, $application, $field, $table, $joins, $id_field, $format, $prefix_field) {

        parent::__construct($request, $session, $application);

        $this->field = $field;

        $this->table = $table;

        $this->joins = $joins;

        $this->id_field = $id_field;

        $this->format = $format;

        $this->prefix_field = $prefix_field;

        
    }

    function reset() {
        
    }

    function bootstrap () {
        
    }

    function get() {

        $ret = '';

        if (property_exists($this, 'id')) {

            $ret .= '<div class="variante">';

            $ret .= $this->prefix;

            $ret .= '<ul>';

            $rows = $this->application->select($this->table, $this->joins, [$this->id_field => $this->id]);

            foreach($rows as $row) {

                $ret .= '<li>';

                $to_implode = [];

                foreach($this->format as $f_field) {

                    if (isset($row[$f_field])) {

                        array_push($to_implode, $row[$f_field]);

                    } else {

                        array_push($to_implode, $f_field);

                    }

                    

                }

                $ret .= implode(' ', $to_implode);

                $ret .= '</li>';

            }

            $ret .= '</ul>';

            $ret .= '</div>';

        }

        return $ret;

    }

    function post() {
        
    }

    function set_id($id) {

        $this->id = $id;

    }

    function set_prefix($prefix) {

        $this->prefix = $prefix;

    }
    
}