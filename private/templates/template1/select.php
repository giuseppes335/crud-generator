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
    
        <?php foreach($this->get_records() as $record): ?>
        
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
    
    
    function get_records() {
        
        $selects = [];
        
        $this->update_authorized_fields();
        
        $this->update_creators();
        
        $creators = null;
        
        if ($this->auth) {
            
            if (property_exists($this, 'authorized_fields')) {
                
                $authorized_fields = [];
                
                if ($this->authorized_fields) {
                    
                    if (count($this->authorized_fields) > 0) {
                        
                        
                        if (in_array($this->dataset_id, $this->authorized_fields)) {
                            
                            array_push($selects, $this->dataset_id);
                            
                        }
                        
                        if (in_array($this->dataset_label, $this->authorized_fields)) {
                            
                            array_push($selects, $this->dataset_label);
                            
                        }
                        
                    } else {
                        
                        array_push($selects, $this->dataset_id);
                        
                        array_push($selects, $this->dataset_label);
                        
                    }
                    
                } else {
                    
                    array_push($selects, $this->dataset_id);
                    
                    array_push($selects, $this->dataset_label);
                    
                }
                
            }
            
            if (property_exists($this, 'creators')) {
                
                $creators = [];
                
                if ($this->creators && count($this->creators) > 0) {
                    
                    $creators = $this->creators;
                    
                }
                
            }
            
        }
        
        return $this->application->select($this->dataset_table, [], [], $selects, false, 0, $creators);
        
    }

    
}