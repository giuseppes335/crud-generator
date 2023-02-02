Skip to content
Search or jump to…
Pull requests
Issues
Codespaces
Marketplace
Explore
 
@giuseppes335 
giuseppes335
/
crud-generator
Public
Cannot fork because you own this repository and are not a member of any organizations.
Code
Issues
Pull requests
Actions
Projects
Wiki
Security
Insights
Settings
crud-generator/be-crea-elementi.php /
@giuseppes335
giuseppes335 fixes
Latest commit d92dda7 yesterday
 History
 1 contributor
485 lines (299 sloc)  8.57 KB

<?php 

require_once 'private/be/global_import.php';
require_once "spyc/Spyc.php";

$schema = $request->post['schema'];
$schema_object = spyc_load($schema);


echo <<<EOT
<pre>
imports: []
components:
  auth-storage: &auth-storage
    table_name: users
    field_username: users_email
    field_password: users_password
  authorized-user: &authorized-user
    storage: *auth-storage
    username: admin1@admins.it 
  acl: &acl
    groups_table_name: groups
    users_table_name: users
    cruds_table_name: cd_resources_groups
    rus_table_name: ru_resources_groups
    rs_table_name: r_resources_groups
    sharings_table_name: sharings
  request: &request
    name: request
    component: request.php
  session: &session
    name: session
    component: session.php
  application: &application
    name: application
    component: application.php
</pre>      
EOT;



$menu_items = '';

$menu_items_array = [];

$i = 0;

foreach($schema_object['elements'] as $element) {

  $i++;

  $plural_name = $element['plural_name'];

  $insert_table = str_replace('-', '_', $plural_name);

  $ucfirst_plural_name = ucfirst(str_replace('-', ' ', $plural_name));
  
  

  $menu_item = "[$i, $plural_name.php, $ucfirst_plural_name]";
  array_push($menu_items_array, $menu_item);

}

$menu_items = implode(",", $menu_items_array);


$login = null;

if (isset($element['auth'])) {
    
    $login = <<<EOT
  form-login: &form-login
    name: form-login
    component: login.php
    params:
      request: *request
      session: *session
      application: *application
      action_component: form-login
      page: login.php
      redirect_component: users
      users_table: users
      username_field: users_email
      password_field: users_password
  login: &login
    name: login
    component: pagelogin.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      title: Login
      content: *form-login
      footer: *footer
EOT;
    
}

echo <<<EOT
<pre>
  menu: &menu
    name: menu
    component: menu.php
    params:
      request: *request
      session: *session
      application: *application
      items: [$menu_items]
  header: &header
    name: header
    component: header.php
    params:
      request: *request
      session: *session
      application: *application
      language: it
      menu: *menu
      title: Demo
  footer: &footer
    name: footer
    component: footer.php
    params:
      request: *request
      session: *session
      application: *application
$login
</pre>
EOT;



foreach($schema_object['elements'] as $element) {

  $output = '';

  $output_fields = '';

  $joins = '';

  $output_seed = '';



  $singular_name = $element['singular_name'];

  $plural_name = $element['plural_name'];

  $insert_table = str_replace('-', '_', $plural_name);

  $ucfirst_singular_name = ucfirst(str_replace('-', ' ', $singular_name));

  $ucfirst_plural_name = ucfirst(str_replace('-', ' ', $plural_name));

  $fields = $element['fields'];

  $seeds = null;

  if (isset($element['seeds'])) {

    $seeds = $element['seeds'];

  }
  
  $auth = null;
  
  if (isset($element['auth']) && $element['auth'] == 0) {
      
      $auth = 'auth: *authorized-user';
      
  } else if (isset($element['auth']) && $element['auth'] == 1) {
      
      $auth = <<<EOT
auth: 
        resource: $insert_table 
        acl: *acl
EOT;
      
  }


  $output_table_fields_array = [];

  $output_select_fields_array = [];

  $output_fields_array = [];

  $joins_array = [];

  $seed_array = [];

  if ($seeds) {

    foreach($seeds as $seed) {

      array_push($seed_array, "[" . implode(',', $seed) . "]");
  
    }

  } 

  $output_seed = '[' . implode(', ', $seed_array) . ']';





  array_push($output_table_fields_array, "[Id, {$insert_table}_id]");

  array_push($output_select_fields_array, "{$insert_table}_id");

  foreach($fields as $index => $field0) {

    $field = $field0;

    $component = 'input-text';

    $type = <<<EOT
format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
EOT;

    $id = '';

    $label = '';

    $name = '';


    

    if (is_array($field0)) {

      $field = $field0[0];

      $component = 'select';

      $table = str_replace('-', '_', $field0[1]['plural_name']);
      
      $table_alias = $table . '_' . $index;

      $table_id = $table . '_id';    

      $table_label = $table . '_' . $field0[2];
      
      $auth = null;
      
      if (isset($element['auth']) && $element['auth'] == 0) {
          
          $auth = <<<EOT
              auth:
                acl: *acl
                resource: $table
EOT;
          
      }

      $type = <<<EOT
format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: $table
              dataset_id: $table_id
              dataset_label: $table_label
              view: []
              multiselect: []
              $auth
EOT;

      $id = "$plural_name-$field";

      $label = ucfirst(str_replace('-', ' ', $id));
  
      $name = str_replace('-', '_', $id);

      array_push($joins_array, "join $table as $table_alias on $table_alias.$table_id = $insert_table.$name");
      

      
      
      $name0 = str_replace('-', '_', "$table_alias.$table_label");
      
      $name1 = str_replace('-', '_', "$table_alias" . "_" . "$field0[2]");
      
      array_push($output_table_fields_array, "[$label, $name1]");
      
      array_push($output_select_fields_array, "$name0 as $name1");


    } else {


      $id = "$plural_name-$field";

      $label = ucfirst(str_replace('-', ' ', $id));
  
      $name = str_replace('-', '_', $id); 
      
      array_push($output_table_fields_array, "[$label, $name]");
      
      array_push($output_select_fields_array, $name);

    }

    $joins = "[" . implode(', ', $joins_array) . "]";



    $output_field = <<<EOT
          $id: &$id
            name: $id
            component: $component.php
            params:
              request: *request
              session: *session
              application: *application
              id: $id
              label: $label
              name: $name
              $type
EOT;

    array_push($output_fields_array, $output_field);











  }

  $output_fields = implode("\n", $output_fields_array);

  $output_table_fields = '[' . implode(', ', $output_table_fields_array) . ']';

  $output_select_fields = '[' . implode(', ', $output_select_fields_array) . ']';







  

  $output .= <<<EOT
  <pre>
  $singular_name-form: &$singular_name-form
    name: $singular_name-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      $auth
      action_component: $singular_name-form
      insert_table: $insert_table
      redirect_component: $plural_name
      seed: $output_seed
      fields: 
$output_fields
      
  $singular_name: &$singular_name
    name: $singular_name
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: $ucfirst_singular_name form
      content: *$singular_name-form
      footer: *footer
        
  $plural_name-table: &$plural_name-table
    name: $plural_name-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      $auth
      fields: $output_table_fields
      action_page: $singular_name
      select: $output_select_fields
      from: $insert_table
      joins: $joins
      referencing: []
      form: *$singular_name-form
        
  $plural_name: &$plural_name
    name: $plural_name
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: $ucfirst_plural_name table
      content: *$plural_name-table
      footer: *footer
    
  </pre>
EOT;


  echo $output;

}

$keys = array_keys($schema_object['elements']);

$start_element = str_replace('-', '_', $schema_object['elements'][$keys[0]]['plural_name']);

$index = <<<EOT
  index: &index
    name: index
    component: index.php
    params: 
      request: *request
      session: *session
      application: *application
      start_page: $start_element
      bootstraps:
EOT;

foreach($schema_object['elements'] as $i => $element) {

  $singular_name = $element['singular_name'];

  $plural_name = $element['plural_name'];

  $index .= <<<EOT
          
         - *$singular_name
         - *$plural_name
EOT;

}

echo "<pre>$index</pre>";