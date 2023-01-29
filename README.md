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
  auth-storage: &auth-storage
    table_name: users
    field_username: users_email
    field_password: users_password
<br>
  authorized-user: &authorized-user
    storage: *auth-storage
    username: admin1@admins.it 
<br>
  acl: &acl
    groups_table_name: groups
    users_table_name: users
    cruds_table_name: cd_resources_groups
    rus_table_name: ru_resources_groups
    rs_table_name: r_resources_groups
    sharings_table_name: sharings
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
      items: [[1, groups.php, Groups],[2, resources.php, Resources],[3, cd-resources-groups.php, Cd resources groups],[4, ru-resources-groups.php, Ru resources groups],[5, r-resources-groups.php, R resources groups],[6, users.php, Users],[7, sharings.php, Sharings],[8, training-cards.php, Training cards],[9, annotations.php, Annotations]]
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
      title: ACL
<br>
  footer: &footer
    name: footer
    component: footer.php
    params:
      request: *request
      session: *session
      application: *application
<br>
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
<br>
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
<br>
  group-form: &group-form
    name: group-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: groups 
        acl: *acl
      action_component: group-form
      insert_table: groups
      redirect_component: groups
      seed: [[1,Admins],[2,Accounts manager,1], [3,Trainers,1]]
      fields: 
          groups-name: &groups-name
            name: groups-name
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: groups-name
              label: Groups name
              name: groups_name
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
<br>  
  group: &group
    name: group
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Group form
      content: *group-form
      footer: *footer
<br>       
  groups-table: &groups-table
    name: groups-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: groups 
        acl: *acl
      fields: [[Id, groups_id], [Groups name, groups_name]]
      action_page: group
      select: [groups_id, groups_name]
      from: groups
      joins: []
      referencing: []
      form: *group-form
<br>     
  groups: &groups
    name: groups
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Groups table
      content: *groups-table
      footer: *footer
<br>
  resource-form: &resource-form
    name: resource-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: resources 
        acl: *acl
      action_component: resource-form
      insert_table: resources
      redirect_component: resources
      seed: [[1,groups], [2,resources], [3,cd_resources_groups], [4,ru_resources_groups], [5,r_resources_groups], [6,users], [7,sharings],[8,groups,1],[9,users,1],[10,training_cards,1],[11,annotations,1]]
      fields: 
          resources-name: &resources-name
            name: resources-name
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: resources-name
              label: Resources name
              name: resources_name
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
<br>     
  resource: &resource
    name: resource
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Resource form
      content: *resource-form
      footer: *footer
<br>       
  resources-table: &resources-table
    name: resources-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: resources 
        acl: *acl
      fields: [[Id, resources_id], [Resources name, resources_name]]
      action_page: resource
      select: [resources_id, resources_name]
      from: resources
      joins: []
      referencing: []
      form: *resource-form
<br>     
  resources: &resources
    name: resources
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Resources table
      content: *resources-table
      footer: *footer
<br>
  cd-resources-group-form: &cd-resources-group-form
    name: cd-resources-group-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: cd_resources_groups 
        acl: *acl
      action_component: cd-resources-group-form
      insert_table: cd_resources_groups
      redirect_component: cd-resources-groups
      seed: [[1,1,1], [2,2,1], [3,3,1], [4,4,1], [5,5,1], [6,6,1], [7,7,1], [8,8,2,1], [9,9,2,1], [10,10,3,1], [11,11,2,1]]
      fields: 
          cd-resources-groups-resource-id: &cd-resources-groups-resource-id
            name: cd-resources-groups-resource-id
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: cd-resources-groups-resource-id
              label: Cd resources groups resource id
              name: cd_resources_groups_resource_id
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: resources
              dataset_id: resources_id
              dataset_label: resources_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: resources
          cd-resources-groups-group-id: &cd-resources-groups-group-id
            name: cd-resources-groups-group-id
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: cd-resources-groups-group-id
              label: Cd resources groups group id
              name: cd_resources_groups_group_id
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: groups
              dataset_id: groups_id
              dataset_label: groups_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: groups
<br>    
  cd-resources-group: &cd-resources-group
    name: cd-resources-group
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Cd resources group form
      content: *cd-resources-group-form
      footer: *footer
<br>        
  cd-resources-groups-table: &cd-resources-groups-table
    name: cd-resources-groups-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: cd_resources_groups 
        acl: *acl
      fields: [[Id, cd_resources_groups_id], [Cd resources groups resource id, resources_0_name], [Cd resources groups group id, groups_1_name]]
      action_page: cd-resources-group
      select: [cd_resources_groups_id, resources_0.resources_name as resources_0_name, groups_1.groups_name as groups_1_name]
      from: cd_resources_groups
      joins: [join resources as resources_0 on resources_0.resources_id = cd_resources_groups.cd_resources_groups_resource_id, join groups as groups_1 on groups_1.groups_id = cd_resources_groups.cd_resources_groups_group_id]
      referencing: []
      form: *cd-resources-group-form
<br>        
  cd-resources-groups: &cd-resources-groups
    name: cd-resources-groups
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Cd resources groups table
      content: *cd-resources-groups-table
      footer: *footer
<br>
  ru-resources-group-form: &ru-resources-group-form
    name: ru-resources-group-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: ru_resources_groups 
        acl: *acl
      action_component: ru-resources-group-form
      insert_table: ru_resources_groups
      redirect_component: ru-resources-groups
      seed: []
      fields: 
          ru-resources-groups-resource-id: &ru-resources-groups-resource-id
            name: ru-resources-groups-resource-id
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: ru-resources-groups-resource-id
              label: Ru resources groups resource id
              name: ru_resources_groups_resource_id
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: resources
              dataset_id: resources_id
              dataset_label: resources_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: resources
          ru-resources-groups-group-id: &ru-resources-groups-group-id
            name: ru-resources-groups-group-id
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: ru-resources-groups-group-id
              label: Ru resources groups group id
              name: ru_resources_groups_group_id
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: groups
              dataset_id: groups_id
              dataset_label: groups_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: groups
          ru-resources-groups-fields: &ru-resources-groups-fields
            name: ru-resources-groups-fields
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: ru-resources-groups-fields
              label: Ru resources groups fields
              name: ru_resources_groups_fields
              format: s
              mysql_type: varchar(255)
              nullable: null
              is_password: false
<br>      
  ru-resources-group: &ru-resources-group
    name: ru-resources-group
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Ru resources group form
      content: *ru-resources-group-form
      footer: *footer
<br>        
  ru-resources-groups-table: &ru-resources-groups-table
    name: ru-resources-groups-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: ru_resources_groups 
        acl: *acl
      fields: [[Id, ru_resources_groups_id], [Ru resources groups resource id, resources_0_name], [Ru resources groups group id, groups_1_name], [Ru resources groups fields, ru_resources_groups_fields]]
      action_page: ru-resources-group
      select: [ru_resources_groups_id, resources_0.resources_name as resources_0_name, groups_1.groups_name as groups_1_name, ru_resources_groups_fields]
      from: ru_resources_groups
      joins: [join resources as resources_0 on resources_0.resources_id = ru_resources_groups.ru_resources_groups_resource_id, join groups as groups_1 on groups_1.groups_id = ru_resources_groups.ru_resources_groups_group_id]
      referencing: []
      form: *ru-resources-group-form
<br>        
  ru-resources-groups: &ru-resources-groups
    name: ru-resources-groups
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Ru resources groups table
      content: *ru-resources-groups-table
      footer: *footer
<br>
  r-resources-group-form: &r-resources-group-form
    name: r-resources-group-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: r_resources_groups 
        acl: *acl
      action_component: r-resources-group-form
      insert_table: r_resources_groups
      redirect_component: r-resources-groups
      seed: [[1,8,3,,1], [2,9,3,,1], [3,10,2,,1], [4,11,3,,1]]
      fields: 
          r-resources-groups-resource-id: &r-resources-groups-resource-id
            name: r-resources-groups-resource-id
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: r-resources-groups-resource-id
              label: R resources groups resource id
              name: r_resources_groups_resource_id
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: resources
              dataset_id: resources_id
              dataset_label: resources_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: resources
          r-resources-groups-group-id: &r-resources-groups-group-id
            name: r-resources-groups-group-id
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: r-resources-groups-group-id
              label: R resources groups group id
              name: r_resources_groups_group_id
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: groups
              dataset_id: groups_id
              dataset_label: groups_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: groups
          r-resources-groups-fields: &r-resources-groups-fields
            name: r-resources-groups-fields
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: r-resources-groups-fields
              label: R resources groups fields
              name: r_resources_groups_fields
              format: s
              mysql_type: varchar(255)
              nullable: null
              is_password: false
<br>      
  r-resources-group: &r-resources-group
    name: r-resources-group
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: R resources group form
      content: *r-resources-group-form
      footer: *footer
<br>        
  r-resources-groups-table: &r-resources-groups-table
    name: r-resources-groups-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: r_resources_groups 
        acl: *acl
      fields: [[Id, r_resources_groups_id], [R resources groups resource id, resources_0_name], [R resources groups group id, groups_1_name], [R resources groups fields, r_resources_groups_fields]]
      action_page: r-resources-group
      select: [r_resources_groups_id, resources_0.resources_name as resources_0_name, groups_1.groups_name as groups_1_name, r_resources_groups_fields]
      from: r_resources_groups
      joins: [join resources as resources_0 on resources_0.resources_id = r_resources_groups.r_resources_groups_resource_id, join groups as groups_1 on groups_1.groups_id = r_resources_groups.r_resources_groups_group_id]
      referencing: []
      form: *r-resources-group-form
<br>        
  r-resources-groups: &r-resources-groups
    name: r-resources-groups
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: R resources groups table
      content: *r-resources-groups-table
      footer: *footer
<br>
  user-form: &user-form
    name: user-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: users 
        acl: *acl
      action_component: user-form
      insert_table: users
      redirect_component: users
      seed: [[1,utente1,admin1@admins.it,$2y$10$OInzFMTtEMxb3Q6yobRltuO3ZSQ.ez9lry4TGNJ9cZfPO0zBWkgpm,1],[2,Trainer 1,trainer1@trainers.it,$2y$10$OInzFMTtEMxb3Q6yobRltuO3ZSQ.ez9lry4TGNJ9cZfPO0zBWkgpm,3,1],[3,Account 1,account1@accounts.it,$2y$10$OInzFMTtEMxb3Q6yobRltuO3ZSQ.ez9lry4TGNJ9cZfPO0zBWkgpm,2,1]]
      fields: 
          users-name: &users-name
            name: users-name
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: users-name
              label: Users name
              name: users_name
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
          users-email: &users-email
            name: users-email
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: users-email
              label: Users email
              name: users_email
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
          users-password: &users-password
            name: users-password
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: users-password
              label: Users password
              name: users_password
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: true
          users-group-id: &users-group-id
            name: users-group-id
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: users-group-id
              label: Users group id
              name: users_group_id
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: groups
              dataset_id: groups_id
              dataset_label: groups_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: groups
<br>      
  user: &user
    name: user
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: User form
      content: *user-form
      footer: *footer
<br>        
  users-table: &users-table
    name: users-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: users 
        acl: *acl
      fields: [[Id, users_id], [Users name, users_name], [Users email, users_email], [Users password, users_password], [Users group id, groups_3_name]]
      action_page: user
      select: [users_id, users_name, users_email, users_password, groups_3.groups_name as groups_3_name]
      from: users
      joins: [join groups as groups_3 on groups_3.groups_id = users.users_group_id]
      referencing: []
      form: *user-form
 <br>       
  users: &users
    name: users
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Users table
      content: *users-table
      footer: *footer
 <br> 
  sharing-form: &sharing-form
    name: sharing-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: sharings 
        acl: *acl
      action_component: sharing-form
      insert_table: sharings
      redirect_component: sharings
      seed: [[1,2,3,1],[2,3,2,1]]
      fields: 
          sharings-owner: &sharings-owner
            name: sharings-owner
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: sharings-owner
              label: Sharings owner
              name: sharings_owner
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: groups
              dataset_id: groups_id
              dataset_label: groups_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: groups
          sharings-sharing-with: &sharings-sharing-with
            name: sharings-sharing-with
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: sharings-sharing-with
              label: Sharings sharing with
              name: sharings_sharing_with
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: groups
              dataset_id: groups_id
              dataset_label: groups_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: groups
<br>      
  sharing: &sharing
    name: sharing
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Sharing form
      content: *sharing-form
      footer: *footer
<br>        
  sharings-table: &sharings-table
    name: sharings-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: sharings 
        acl: *acl
      fields: [[Id, sharings_id], [Sharings owner, groups_0_name], [Sharings sharing with, groups_1_name]]
      action_page: sharing
      select: [sharings_id, groups_0.groups_name as groups_0_name, groups_1.groups_name as groups_1_name]
      from: sharings
      joins: [join groups as groups_0 on groups_0.groups_id = sharings.sharings_owner, join groups as groups_1 on groups_1.groups_id = sharings.sharings_sharing_with]
      referencing: []
      form: *sharing-form
<br>        
  sharings: &sharings
    name: sharings
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Sharings table
      content: *sharings-table
      footer: *footer
<br>    
  training-card-form: &training-card-form
    name: training-card-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: training_cards 
        acl: *acl
      action_component: training-card-form
      insert_table: training_cards
      redirect_component: training-cards
      seed: []
      fields: 
          training-cards-athlete: &training-cards-athlete
            name: training-cards-athlete
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: training-cards-athlete
              label: Training cards athlete
              name: training_cards_athlete
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: users
              dataset_id: users_id
              dataset_label: users_name
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: users
          training-cards-description: &training-cards-description
            name: training-cards-description
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: training-cards-description
              label: Training cards description
              name: training_cards_description
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
<br>      
  training-card: &training-card
    name: training-card
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Training card form
      content: *training-card-form
      footer: *footer
<br>        
  training-cards-table: &training-cards-table
    name: training-cards-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: training_cards 
        acl: *acl
      fields: [[Id, training_cards_id], [Training cards athlete, users_0_name], [Training cards description, training_cards_description]]
      action_page: training-card
      select: [training_cards_id, users_0.users_name as users_0_name, training_cards_description]
      from: training_cards
      joins: [join users as users_0 on users_0.users_id = training_cards.training_cards_athlete]
      referencing: []
      form: *training-card-form
<br>        
  training-cards: &training-cards
    name: training-cards
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Training cards table
      content: *training-cards-table
      footer: *footer
<br> 
  annotation-form: &annotation-form
    name: annotation-form
    component: form.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: annotations 
        acl: *acl
      action_component: annotation-form
      insert_table: annotations
      redirect_component: annotations
      seed: []
      fields: 
          annotations-training-card: &annotations-training-card
            name: annotations-training-card
            component: select.php
            params:
              request: *request
              session: *session
              application: *application
              id: annotations-training-card
              label: Annotations training card
              name: annotations_training_card
              format: i
              mysql_type: bigint unsigned
              nullable: not null
              dataset_table: training_cards
              dataset_id: training_cards_id
              dataset_label: training_cards_id
              view: []
              multiselect: []
              auth:
                acl: *acl
                resource: training_cards
          annotations-text: &annotations-text
            name: annotations-text
            component: input-text.php
            params:
              request: *request
              session: *session
              application: *application
              id: annotations-text
              label: Annotations text
              name: annotations_text
              format: s
              mysql_type: varchar(255)
              nullable: not null
              is_password: false
<br>      
  annotation: &annotation
    name: annotation
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Annotation form
      content: *annotation-form
      footer: *footer
<br>        
  annotations-table: &annotations-table
    name: annotations-table
    component: table.php
    params:
      request: *request
      session: *session
      application: *application
      auth: 
        resource: annotations 
        acl: *acl
      fields: [[Id, annotations_id], [Annotations training card, training_cards_0_id], [Annotations text, annotations_text]]
      action_page: annotation
      select: [annotations_id, training_cards_0.training_cards_id as training_cards_0_id, annotations_text]
      from: annotations
      joins: [join training_cards as training_cards_0 on training_cards_0.training_cards_id = annotations.annotations_training_card]
      referencing: []
      form: *annotation-form
<br>        
  annotations: &annotations
    name: annotations
    component: page.php
    params:
      request: *request
      session: *session
      application: *application
      header: *header
      language: it
      menu: *menu
      title: Annotations table
      content: *annotations-table
      footer: *footer
<br>    
  index: &index
    name: index
    component: index.php
    params: 
      request: *request
      session: *session
      application: *application
      start_page: login
      bootstraps:          
         - *group
         - *groups          
         - *resource
         - *resources          
         - *cd-resources-group
         - *cd-resources-groups          
         - *ru-resources-group
         - *ru-resources-groups          
         - *r-resources-group
         - *r-resources-groups          
         - *user
         - *users          
         - *sharing
         - *sharings          
         - *training-card
         - *training-cards          
         - *annotation
         - *annotations
</pre>



At http://127.0.0.1:8000/demo.php start the demo and login
<pre>
email: admin1@admins.it 
password: test
</pre>


If you wanto to generate your own YAML schemas you can use crea-elemento.php form. You can post for example:
<pre>
elements:
  groups: &groups
    auth: 1
    plural_name: groups
    singular_name: group
    fields:
      - name
    seeds:
      - [1, Admins]
<br>
  resources: &resources
    auth: 1
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
    auth: 1
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
    auth: 1
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
    auth: 1
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
    auth: 1
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
    auth: 1
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
<br>
  training-cards: &training-cards
    auth: 1
    plural_name: training-cards
    singular_name: training-card
    fields:
      - 
        - athlete
        - *users
        - name
      - description
<br>
  annotations: &annotations
    auth: 1
    plural_name: annotations
    singular_name: annotation
    fields:
      - 
        - training-card
        - *training-cards
        - id
      - text
</pre>
