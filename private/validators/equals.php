<?php

class Equals {

    function __construct($texts, $language, $comparing_value) {
        
        $this->texts = $texts;

        $this->language = $language;

        $this->comparing_value = $comparing_value;
    
    }

    function validate($value) {
        return ($value === $this->comparing_value)?true:false;
    }

    function error_message() {

        return $this->texts[$this->language]['equals'];
    }

}