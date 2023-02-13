<?php require_once 'private/layout/header.php'; ?>

<?php 

$name = '';

$template_id = '';

$schema = '';

$demo_id = '';

$database_host = '127.0.0.1';

$database_username = 'root';

$database_password = '';

$database_name = 'demos';

if (isset($_GET['demo_id']) && $_GET['demo_id']) {

    $demo = $application->get_demo($_GET['demo_id']);
    
    if ($demo) {
        
        $name = $demo['name'];
        
        $template_id = $demo['template_id'];
        
        $schema = $demo['schema0'];
        
        $demo_id = $_GET['demo_id'];
        
        $params = json_decode($demo['params']);
        
        $database_host = $params->database_host;
        
        $database_username = $params->database_username;
        
        $database_password = $params->database_password;
        
        $database_name = $params->database_name;
        
        
    }
    
}

?>

<section class="primary-font-color">

    <h1 style="font-size: 32px;">New application</h1>
    
    <form action="preview.php?demo_id=<?= $demo_id ?>&debug=&reset=" method="post">
    
        <div class="form-item" style="margin-top: 8px;">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= $name ?>">
        </div>
       	
       	<!--
        <div class="form-item" style="margin-top: 8px;">
            <label for="database-host">Database host</label>
            <input type="text" id="database-host" name="database_host" value="<?= $database_host ?>">
        </div>
        
        <div class="form-item" style="margin-top: 8px;">
            <label for="database-username">Database username</label>
            <input type="text" id="database-username" name="database_username" value="<?= $database_username ?>">
        </div>
        
        <div class="form-item" style="margin-top: 8px;">
            <label for="database-password">Database password</label>
            <input type="text" id="database-password" name="database_password" value="<?= $database_password ?>">
        </div>
        -->
        
        <div class="form-item" style="margin-top: 8px;">
            <label for="database-name">Database name</label>
            <input type="text" id="database-name" name="database_name" value="<?= $database_name ?>">
        </div>

        <div class="form-item" style="margin-top: 8px;">
            <label for="template-id">Template</label>
            <select id="template-id" name="template_id" value="<?= $template_id ?>">
       			<?php foreach($application->get_templates() as $template) : ?>
       			<option value="<?= $template['id']; ?>"><?= $template['name']; ?></option>
       			<?php endforeach; ?>
        	</select>
        </div>
        
<?php 

$default_schema = <<<EOT
elements:
  products: &products
    plural_name: products
    singular_name: product
    fields:
      - name
      - description


  providers: &providers
    plural_name: providers
    singular_name: provider
    fields:
      - name
      - description


  product-providers: &product-providers
    plural_name: product-providers
    singular_name: product-provider
    fields:
      - 
        - product
        - *products
        - name
      - 
        - provider
        - *providers
        - name
      - quantity
EOT;

?>		
        <div class="form-item" style="margin-top: 8px;">
            <label for="schema">Yaml schema</label>
            <textarea id="schema" name="schema" style="height: 300px;"><?= $schema?$schema:$default_schema ?></textarea>
        </div>
        
        <div class="form-item" style="margin-top: 8px; overflow: auto;">
       		<button type="submit" style="height: 36px; font-size: 16px; float: right;">Preview</button> 
       	</div>
       	
	</form>
    
</section>


<?php require_once 'private/layout/footer.php'; ?>
