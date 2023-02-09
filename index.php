<?php require_once 'private/layout/header.php'; ?>

<section class="primary-font-color">

    <h1 style="font-size: 32px;">New application</h1>
    
    <form action="preview.php?debug&reset" method="post">
    
        <div class="form-item" style="margin-top: 8px;">
            <label for="name">Name</label>
            <input type="text" id="name" name="name">
        </div>

        <div class="form-item" style="margin-top: 8px;">
            <label for="template-id">Template</label>
            <select id="template-id" name="template_id">
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
            <textarea id="schema" name="schema" style="height: 300px;"><?= $default_schema ?></textarea>
        </div>
        
        <div class="form-item" style="margin-top: 8px; overflow: auto;">
       		<button type="submit" style="height: 36px; font-size: 16px; float: right;">Preview</button> 
       	</div>
       	
	</form>
    
</section>


<?php require_once 'private/layout/footer.php'; ?>
