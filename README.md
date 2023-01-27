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
INSERT INTO templates (name, path) VALUES ('Evolutivo', 'private/templates/evolutivo');
</pre>

Edit private/templates/evolutivo/application.php as follows
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
http://127.0.0.1:8000/crea-elemento.php
</pre>

Inside crea-elemento.php form, post the following ACL schema and copy the output schema
<pre>
elements:
  groups: &groups
    auth: 0
    plural_name: groups
    singular_name: group
    fields:
      - name
    seeds:
      - [1, Admins]
<br>
  resources: &resources
    auth: 0
    plural_name: resources
    singular_name: resource
    fields:
      - name
    seeds:
      - [1, groups] 
      - [2, resources] 
      - [3, cd_resources_groups]
      - [4, ru_resources_groups]
      - [5, r_resources_groups]
      - [6, users]
      - [7, sharings]
<br>
  cd-resources-groups: &cd-resources-groups
    auth: 0
    plural_name: cd-resources-groups
    singular_name: cd-resources-group
    fields:
      - 
        - resource-id
        - *resources
        - name
      - 
        - group-id
        - *groups
        - name      
    seeds:
      - [1, 1, 1]
      - [2, 2, 1] 
      - [3, 3, 1]
      - [4, 4, 1]
      - [5, 5, 1]
      - [6, 6, 1]
      - [7, 7, 1]
<br>
  ru-resources-groups: &ru-resources-groups
    auth: 0
    plural_name: ru-resources-groups
    singular_name: ru-resources-group
    fields:
      - 
        - resource-id
        - *resources
        - name
      - 
        - group-id
        - *groups
        - name  
      - fields
<br>
  r-resources-groups: &r-resources-groups
    auth: 0
    plural_name: r-resources-groups
    singular_name: r-resources-group
    fields:
      - 
        - resource-id
        - *resources
        - name
      - 
        - group-id
        - *groups
        - name  
      - fields
<br>
  users: &users
    auth: 0
    plural_name: users
    singular_name: user
    fields:
      - name
      - email
      - password
      - 
        - group-id
        - *groups
        - name 
    seeds:
      - [1, utente1, admin1@admins.it, $2y$10$.vGA1O9wmRjrwAVXD98HNOgsNpDczlqm3Jq7KnEd1rVAGv3Fykk1a, 1]
<br>
  sharings: &sharings
    auth: 0
    plural_name: sharings
    singular_name: sharing
    fields:
      - 
        - owner
        - *groups
        - name 
      - 
        - sharing-with
        - *groups
        - name  
</pre>

At http://127.0.0.1:8000/demo.php start a new application and paste previous generated schema inside "parametri" 
![This is an image](https://github.com/giuseppes335/crud-generator/tree/main/img/screen1.png) 


At http://127.0.0.1:8000/demo.php start the demo and login
<pre>
admin1@admins.it 
rasmuslerdorf
</pre>








