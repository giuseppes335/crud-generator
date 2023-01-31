<?php

require_once 'relation.php';

class Select extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

        $this->id = $params['id'];

        $this->label = $params['label'];

        $this->name = $params['name'];

        $this->format = $params['format'];

        $this->mysql_type = $params['mysql_type'];

        $this->nullable = $params['nullable'];

        $this->dataset_table = $params['dataset_table'];

        $this->dataset_id = $params['dataset_id'];

        $this->dataset_label = $params['dataset_label'];

        $this->view = $params['view'];

        $this->multiselect = $params['multiselect'];
        
        $this->init();

    }
    
    function init() {
        
        $this->value = '';
        
        $this->disabled = '';
        
    }


    function get() {
        
?>

<div class="field">

	<label for="<?= $this->id ?>"><?= $this->label ?></label>

	<select id="<?= $this->id ?>" name="<?= $this->name ?>" $multiselect_tag <?= $this->disabled ?>>

    	<option value="">...</option>
    
        <?php foreach($this->application->select($this->dataset_table) as $record): ?>
        
        <option <?= $this->get_selected($record[$this->dataset_id]) ?> value="<?= $record[$this->dataset_id] ?>"><?= $record[$this->dataset_label] ?></option>
        
        <?php endforeach; ?>

	</select>

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

    function disable() {

        $this->disabled = 'disabled';

    }
    
    
    function get_selected($value) {
        
        $selected_tag = '';

        if ($value == $this->value) {
            
            $selected_tag = 'selected';
            
        }
        
        return $selected_tag;
        
    }

}