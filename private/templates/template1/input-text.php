<?php

require_once 'relation.php';

class InputText extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->id = $params['id'];

        $this->label = $params['label'];

        $this->name = $params['name'];

        $this->format = $params['format'];

        $this->mysql_type = $params['mysql_type'];

        $this->nullable = $params['nullable'];

        $this->is_password = $params['is_password'];
        
        $this->init();

    }
    
    function init() {
        
        $this->value = '';
        
    }

    function get() {

?>

<div class="field">
	
	<label for="<?= $this->id ?>"><?= $this->label ?></label>
	
	<input type="text" id="<?= $this->id ?>" name="<?= $this->name ?>" value="<?= $this->value ?>" <?= $this->disabled ?>/>

	<div class="error"><?= $this->session->get_errors_output($this->name) ?></div>

</div>

<?php

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


    
}