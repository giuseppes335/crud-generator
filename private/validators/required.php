<?php

class Required {

    function __construct($texts, $language) {
        
        $this->texts = $texts;

        $this->language = $language;
    
    }

    function validate($value) {

        return ($value !== 0 && $value !== '')?true:false;
        
    }

    function error_message() {

        return $this->texts[$this->language]['required'];

    }

}