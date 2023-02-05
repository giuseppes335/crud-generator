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
    function print_filter_form() {
        
        $prefix = $this->select_table;

?>

<form class="filter-form" onsubmit="<?= $this->get_onsubmit_filters_form() ?>">
	<div>
	
        <select id="<?= $prefix ?>-filter" name="filter">
        
            <option value="">...</option>
            <?php foreach($this->fields as $field): ?>
            <option value="<?= $field[1] ?>"><?= $field[0] ?></option>
            <?php endforeach; ?>
          
        </select>

        <select id="<?= $prefix ?>-operator" name="operator">
            <option value="eq">=</option>
            <option value="like">Like</option>
            <option value="lt"><</option>
            <option value="gt">></option>
            <option value="let"><=</option>
            <option value="get">>=</option>
        </select>

        <input type="text" id="<?= $prefix ?>-filter-value" name="filter_value">
        
        <button class="button" type="submit"><img class="icon invert" src="<?= $this->application->host ?>/<?= $this->application->path ?>/img/search_FILL0_wght700_GRAD0_opsz48.png">Cerca</button>
    
    </div>
</form>

<div class="filters-labels">

	<?php foreach($this->request->get as $get_field => $get_values): ?>
	
	<?php $fields = array_filter($this->fields, function($field) use ($get_field)  {
                return $field[1] === $get_field;
	}); if (count($fields) > 0): ?>
	
	<a style="text-decoration: none;" href="" onclick="<?= $this->get_onclick_filters_labels($get_field) ?>"><span><?= $this->get_filters_label($get_field) ?></span></a>
	
	<?php endif; ?>
	
	<?php endforeach; ?>
	
</div>
        
<?php

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
   
?>

<div class="table-container"><a href="<?= $button_aggiungi_query_string ?>" class="button-a" onclick="<?= $onclick ?>">Aggiungi</a>

<?php
     
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
        
?>

<div id="overlay">
    <div id="popup">
        <div style="text-align: right;">
        	<a href="<?= $button_close_popup_query_string ?>" class="close-button"><img src="<?= $img_close ?>" alt=""></a>
        </div>
        <div id="popup-content">
        
        <?= $this->form->get() ?>
        
        </div>
	</div>
</div>

<?php

    }

    // Print header table
    function print_header_table() {
        
?>

<div class="scrollable">
    <table class="table">
        <thead>
        
            <?php foreach($this->fields as $field): ?>
            
            <th><?= $field[0] ?></th>
            
            <?php endforeach; ?>
            
            <th></th>
            
            <th></th>
            
        </thead>
        
<?php

    }

    // Print body table
    function print_body_table() {

        $selects = $this->selects;

        $fields_names = [];

        foreach($this->fields as $field) {

            array_push($fields_names, $field[1]);

        }

        $filters = [];

        foreach($this->request->get as $get_field => $get_values) {

            if (in_array($get_field, $fields_names) || in_array($get_field, $selects)) {

                $filters[$get_field] = $this->get_query_param_part($get_field, $get_values);

            }
            
        }
        
        $limit = 4;

        $offset = 0;

        if (isset($this->request->get['page'])) {

            $offset = $this->request->get['page'] * 4;

        }
        
        $rows = $this->application->select($this->select_table, $this->joins, $filters, $selects, true, $offset, [], $limit);
        

        
        
        
?>

<tbody>

	<?php foreach($rows as $row): ?>
	
	<?php 
	   $id = $row[$this->select_table . '_id'];
	?>
	
	<tr data-id="<?= $id ?>">
	
    	<?php foreach($this->fields as $field): ?>
    	<td><span><?= $field[0] ?></span><?= $row[$field[1]]?></td>
    	<?php endforeach; ?>
    	
    	<?php 
    	
    	$application_host = $this->application->host;
    	
    	$application_path = $this->application->path;
    	
    	
    	
    	$img_edit = "$application_host/$application_path/img/edit_FILL0_wght700_GRAD0_opsz48.png";
    	
    	$img_delete = "$application_host/$application_path/img/delete_FILL0_wght700_GRAD0_opsz48.png";
    	
    	$img_link = "$application_host/$application_path/img/link_FILL0_wght700_GRAD0_opsz48.png";
    	
    	
    	
    	$query_string_delete = $this->request->set_query_string_param($this->cleared_query_string($id), 'delete', '');
    	
    	$action_page = $this->action_page;
    	
    	$query_string_update = $this->request->set_query_string_param($this->cleared_query_string($id), 'popup', '');
    	
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
    	   
    	?>
    	
    	<td class="table-action-section">
    		<a href="<?= $application_host?><?= $this->application->script_name?><?= $query_string_update ?>" class="button-a" onclick="$onclick_update">
    			<img class="icon invert" src="<?= $img_edit ?>">
    		</a>
    		<a class="button-a" style="margin-left: 4px;" href="<?= $application_host?><?= $this->application->script_name?><?= $query_string_delete ?>">
    			<img class="icon invert" src="<?= $img_delete ?>">
    		</a>
    	
    	<?php 
    	
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
                    
?>

				<a class="link" href="<?= $application_host ?>/<?= $application_path ?>/<?= $page.php ?>?<?= $query_string ?>" style="margin-left: 4px;"><span class="circle" $color><img class="invert icon" src="<?= $img_link ?>"></span> $label</a>

<?php
    
            }

        }
            
?>
		</td>
    	
	</tr>	
	
	<?php endforeach; ?>


</tbody>




<?php 
       

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
        
?>

        		</table>
        	<div class="scrollable-buttons">
            <a class="button-a arrows" href="<?= $button_arrow_down_uri ?>" style="margin-top: 52px;"><img class="icon invert" src="<?= $img_arrow_down ?>"></a>
            <a class="button-a arrows" href="<?= $button_arrow_up_uri ?>"><img class="icon invert" src="<?= $img_arrow_up ?>"></a>
        </div>  
	</div>
</div>

<?php

    }


    private function print_list() {
        
        if ($this->form && isset($this->request->get['popup'])) {
            
            $this->print_popup_form();
            
        }

        $this->print_filter_form();

        $this->print_header_table_container();

        $this->print_header_table();

        $this->print_body_table();

        $this->print_footer_table();

    }

    function get() {

        if (isset($this->request->get['id']) && $this->request->get['id'] && isset($this->request->get['delete'])) {
            
            $this->application->delete_record($this->select_table, $this->request->get['id']);
            
            $redirect = $this->request->referer;
            
            header("Location: $redirect");
            
            exit;
        
        }
        
        $this->print_list_stack();

        $output = '';

        ob_start();
        $this->print_list();
        $output = ob_get_contents();
        ob_end_clean();
        echo $output;
        
        $this->push_list_stack($output);

        $this->print_errors_popup();
        
    }

    function post() {

    }

    function put() {

    }

    function delete() {

    }
    
    function get_onsubmit_filters_form() {
        
?>

event.preventDefault(); 

let filter = document.getElementById('<?= $this->select_table ?>-filter'); 

let filter_value = document.getElementById('<?= $this->select_table ?>-filter-value'); 

let new_url = window.location.href;

let operator = document.getElementById('<?= $this->select_table ?>-operator');

if (!url_exists_param(new_url, filter.value + '[0]')) {

	new_url = url_with_new_param(new_url, filter.value + '[0]', filter_value.value);
	
	new_url = url_with_new_param(new_url, filter.value + '[1]', operator.value);

} else {

	new_url = url_with_new_param(new_url, filter.value + '[2]', filter_value.value);
	
	new_url = url_with_new_param(new_url, filter.value + '[3]', operator.value);

}

window.location.href = new_url;

<?php
        
    }
    
    // TODO
    function get_onclick_filters_labels($get_field) {
        
?>

event.preventDefault(); 

let new_url = remove_url_param(window.location.href, '<?= $get_field ?>' + '[0]'); 

new_url = remove_url_param(new_url, '<?= $get_field ?>' + '[1]'); 

new_url = remove_url_param(new_url, '<?= $get_field ?>' + '[2]'); 

window.location.href = remove_url_param(new_url, '<?= $get_field ?>' + '[3]');

<?php        
    }
    
    function get_filters_label($get_field) {
        
        
        $label_array = [];
        
        $index = 0;
        while(isset($this->request->get[$get_field][$index]) && isset($this->request->get[$get_field][$index + 1])) {

            $fields = array_filter($this->fields, function($field) use ($get_field)  {
                return $field[1] === $get_field;
            });

            $label = array_values($fields)[0][0];
            
            $value = $this->request->get[$get_field][$index];
            
            $operator = $this->request->get[$get_field][$index + 1];
            
            array_push($label_array, "$label $operator $value");

            $index = $index + 2;
            
        }
        
        return implode(' and ', $label_array);
        
    }
    
    
    function cleared_query_string($id) {
        
        $query_string = $this->request->delete_query_string_param($this->request->query_string, 'delete');
        
        $query_string = $this->request->update_query_string_param($query_string, 'nest', 'unnest', '');
        
        $query_string = $this->request->set_query_string_param($query_string, 'id', $id);
       
        
        return $query_string;
        
    }
    
    
    function get_onclick_update() {
        
        $action_page = $this->action_page;
        
        $query_string = $this->cleared_query_string($id);
        
        $query_string = $this->request->set_query_string_param($this->cleared_query_string($id), 'popup', '');
        
        $mobile_url = "$application_host/$application_path/$action_page.php$query_string";
        
?>

event.preventDefault();
let new_href = this.href;
if (window.innerWidth < 768) {
    new_href = '<?= $mobile_url ?>';
    new_href = url_with_new_param(new_href, 'mobile', '');
}
window.location.href = new_href;

<?php        

        
    }
    
    
}