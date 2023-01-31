# crud-generator

Launch local mysql database and create schema
<pre>
demos
</pre>

Launch local server
<pre>
php -S 127.0.0.1:8000
</pre>

Open browser at
<pre>
http://127.0.0.1:8000/demo.php?migrate
</pre>

Run sql query
<pre>
INSERT INTO templates (name, path) VALUES ('Template 1', 'private/templates/template1');
</pre>

Edit private/templates/template1/application.php as follows
<pre>
$this->mysqli = new mysqli(
    '127.0.0.1', 
    'root', 
    '',
    'demos'
);
</pre>

Open browser at
<pre>
http://127.0.0.1:8000/demo.php
</pre>

Press Template 1 "Prova" button and inside personalizza-demo.php form post the following YAML schema
<pre>
imports: []
<br>
components:
<br>
  request: &request
    name: request
    component: request.php
<br>
  session: &session
    name: session
    component: session.php
<br>
  application: &application
    name: application
    component: application.php
<br>
  menu: &menu
    name: menu
    component: menu.php
    params:
      request: *request
      session: *session
      application: *application
      items: [[1, products.php, Products],[2, providers.php, Providers],[3, product-providers.php, Product providers]]
<br>
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
<br>
  footer: &footer
    name: footer
    component: footer.php
    params:
      request: *request
      session: *session
      application: *application
<br>
  product-form: &product-form
    name: product-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application  
      action_component: product-form
      insert_table: products
      redirect_component: products
      seed: []
      fields: 
          products-name: &products-name
            name: products-name
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: products-name
              label: Products name
              name: products_name
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
          products-description: &products-description
            name: products-description
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: products-description
              label: Products description
              name: products_description
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
<br>      
  product: &product
    name: product
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Product form
      content: *product-form
      footer: *footer
<br>        
  products-table: &products-table
    name: products-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application      
      fields: [[Id, products_id], [Products name, products_name], [Products description, products_description]]
      action_page: product
      select: [products_id, products_name, products_description]
      from: products
      joins: []
      referencing: []
      form: *product-form
<br>        
  products: &products
    name: products
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Products table
      content: *products-table
      footer: *footer
<br>
  provider-form: &provider-form
    name: provider-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      action_component: provider-form
      insert_table: providers
      redirect_component: providers
      seed: []
      fields: 
          providers-name: &providers-name
            name: providers-name
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: providers-name
              label: Providers name
              name: providers_name
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
          providers-description: &providers-description
            name: providers-description
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: providers-description
              label: Providers description
              name: providers_description
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
<br>     
  provider: &provider
    name: provider
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Provider form
      content: *provider-form
      footer: *footer
<br>       
  providers-table: &providers-table
    name: providers-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application  
      fields: [[Id, providers_id], [Providers name, providers_name], [Providers description, providers_description]]
      action_page: provider
      select: [providers_id, providers_name, providers_description]
      from: providers
      joins: []
      referencing: []
      form: *provider-form
<br>        
  providers: &providers
    name: providers
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Providers table
      content: *providers-table
      footer: *footer
<br>  
  product-provider-form: &product-provider-form
    name: product-provider-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application  
      action_component: product-provider-form
      insert_table: product_providers
      redirect_component: product-providers
      seed: []
      fields: 
          product-providers-product: &product-providers-product
            name: product-providers-product
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: product-providers-product
              label: Product providers product
              name: product_providers_product
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: products
              dataset_id: products_id
              dataset_label: products_name
              view: []
              multiselect: []            
          product-providers-provider: &product-providers-provider
            name: product-providers-provider
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: product-providers-provider
              label: Product providers provider
              name: product_providers_provider
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: providers
              dataset_id: providers_id
              dataset_label: providers_name
              view: []
              multiselect: []      
          product-providers-quantity: &product-providers-quantity
            name: product-providers-quantity
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: product-providers-quantity
              label: Product providers quantity
              name: product_providers_quantity
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
<br>     
  product-provider: &product-provider
    name: product-provider
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Product provider form
      content: *product-provider-form
      footer: *footer
<br>        
  product-providers-table: &product-providers-table
    name: product-providers-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application 
      fields: [[Id, product_providers_id], [Product providers product, products_0_name], [Product providers provider, providers_1_name], [Product providers quantity, product_providers_quantity]]
      action_page: product-provider
      select: [product_providers_id, products_0.products_name as products_0_name, providers_1.providers_name as providers_1_name, product_providers_quantity]
      from: product_providers
      joins: [join products as products_0 on products_0.products_id = product_providers.product_providers_product, join providers as providers_1 on providers_1.providers_id = product_providers.product_providers_provider]
      referencing: []
      form: *product-provider-form
<br> 
  product-providers: &product-providers
    name: product-providers
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Product providers table
      content: *product-providers-table
      footer: *footer
<br>
  index: &index
    name: index
    component: index.php
    params: 
      request: *request
      session: *session
      application: *application
      start_page: products
      bootstraps:          
         - *product
         - *products          
         - *provider
         - *providers          
         - *product-provider
         - *product-providers
</pre>



At http://127.0.0.1:8000/demo.php start the demo

If you want to generate your own YAML schemas you can use crea-elementi.php form. You can post for example:
<pre>
elements:
  products: &products
    plural_name: products
    singular_name: product
    fields:
      - name
      - description
<br>
  providers: &providers
    plural_name: providers
    singular_name: provider
    fields:
      - name
      - description
<br>
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
</pre>
