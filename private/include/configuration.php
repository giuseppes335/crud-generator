<?php

class Configuration {

    function __construct() {

        // Host
        $this->host = 'http://127.0.0.1:8000';

        // Database
        $this->db_host = '127.0.0.1';

        $this->db_username = 'root';
    
        $this->db_password = '';
    
        $this->db_name = 'demos';

        // Texts
        $this->texts = [

            'it'=> [
                // Login
                'login_header_label' => 'Login',
                'login_email_label' => 'Email',
                'login_password_label' => 'Password',
                'login_button_submit' => 'Invia',
                'login_error_message' => 'Login e password errati',

                // Signup
                'signup_header_label' => 'Registrazione',
                'signup_email_label' => 'Email',
                'signup_password_label' => 'Password',
                'signup_confirm_password_label' => 'Conferma password',
                'signup_button_submit' => 'Invia',

                // Activations
                'activations_header_label' => 'Attivazioni',
                'activations_id_label' => 'Id',
                'activations_description_label' => 'Descrizione',
                'activations_credits_label' => 'Crediti',
                'activations_receipt_number_label' => 'Numero ricevuta',
                'activations_activated_on_label' => 'Data attivazione',
                'activations_new_link' => 'Nuova attivazione',

                // Activate
                'activate_header_label' => 'Nuova attivazione',
                'activate_receipt_number_label' => 'Numero ricevuta',
                'activate_button_submit' => 'Attiva',
                'activate_error_message' => 'Ricevuta non trovata',

                // Templates
                'templates_header_label' => 'Scegli un\'applicazione',
                'templates_id_label' => 'Id',
                'templates_name_label' => 'Nome',
                'templates_description_label' => 'Descrizione',
                'templates_path_label' => 'Percorso',
                'templates_credits_label' => 'Crediti',
                'templates_created_at_label' => 'Data creazione',
                'templates_start_demo_label' => 'Prova',

                // Create demo
                'update_demo_header_label' => 'Modifica demo',
                'create_demo_empty_name_label' => 'Nuova demo',
                'create_demo_empty_schema_label' => 'Schema vuoto',
                'create_demo_update_data_label' => 'Modifica dati',
                'create_demo_import_components_label' => 'Importa componenti',
                'create_demo_components_id_label' => 'Id',
                'create_demo_components_file_name_label' => 'Nome file',
                'create_demo_components_constructor_label' => 'Costruttore',
                'create_demo_components_created_at_label' => 'Data creazione',
                'create_demo_components_customize_label' => 'Personalizza',
                'create_demo_preview_label' => 'Preview',

                // Demos
                'demos_header_label' => 'Demo',
                'demos_id_label' => 'Id',
                'demos_name_label' => 'Name',
                'demos_created_at_label' => 'Data creazione',
                'demos_template_name_label' => 'Template',
                'demos_update_demo_label' => 'Modifica',
                'demos_download_demo_label' => 'Download',
                'demos_start_demo_label' => 'Prova',
                'demos_reset_and_start_demo_label' => 'Resetta e prova',
                'demos_confirm_text' => 'Confermi di voler resettare ?',

                // Customize demo
                'customize_demo_header_label' => 'Personalizza demo (beta)',
                'customize_demo_name_label' => 'Nome',
                'customize_demo_schema_label' => 'Parametri',
                'customize_demo_button_submit' => 'Modifica',

                //Customize component
                'customize_component_header_label' => 'Personalizza',
                'customize_component_content_label' => 'Contenuto',
                'customize_component_content_button_submit' => 'Invia',

                // Validator
                'required' => 'Il campo è richiesto',
                'equals' => 'Il campo non corrisponde',

            ]

        ];

        // Users
        $this->create_users_table_query = "create table if not exists users (id bigint unsigned auto_increment primary key, email varchar(255) unique, password varchar(255), verification_code varchar(255) null, verified boolean null, forgotten_password_code varchar(255) null, created_at timestamp, updated_at timestamp)";

        $this->insert_user_query = "insert into users(email, password, created_at, updated_at) values(?, ?, ?, ?)";

        $this->get_user_query = "select * from users where email = ?";

        // Activations
        $this->create_activations_table_query = "create table if not exists activations (id bigint unsigned auto_increment primary key, description varchar(255), credits tinyint, receipt_number varchar(255) unique, created_at timestamp, updated_at timestamp, user_id bigint unsigned, constraint fk_activation_to_user foreign key(user_id) references users(id))";

        $this->insert_activation_query = "insert into activations(description, credits, receipt_number, created_at, updated_at, user_id) values(?, ?, ?, ?, ?, ?)";

        $this->get_activations_query = "select * from activations where user_id = ?";

        // Templates
        $this->create_templates_table_query = "create table if not exists templates (id bigint unsigned auto_increment primary key, name varchar(255) unique, description varchar(255) null, path varchar(255), default_params text null, default_schema text null, credits tinyint, image_name varchar(255) null, created_at timestamp default CURRENT_TIMESTAMP, updated_at timestamp default CURRENT_TIMESTAMP)";

        $this->get_templates_query = "select * from templates";

        $this->get_template_query = "select * from templates where id = ?";

        // Demos
        $this->create_demos_table_query = "create table if not exists demos (id bigint unsigned auto_increment primary key, name varchar(255) null unique, params text null, schema0 text null, created_at timestamp default CURRENT_TIMESTAMP, updated_at timestamp default CURRENT_TIMESTAMP, session_id varchar(255), template_id bigint unsigned, constraint fk_demo_to_template foreign key(template_id) references templates(id))";

        $this->insert_demo_query = "insert into demos(name, schema0, created_at, updated_at, session_id, template_id) values(?, ?, ?, ?, ?, ?)";

        $this->update_demo_query = "update demos set name = ?, params = ?, schema0 = ?, updated_at = ? where id = ?";


        $this->get_demos_query = "select demos.*, templates.name as template_name from demos join templates on templates.id = demos.template_id where session_id = ? order by created_at desc limit 1";

        $this->get_demo_query = "select * from demos where id = ?";

        $this->get_sessions_id_for_clear_query = "select distinct session_id from demos;";

        $this->get_demos_for_clear_query = "select * from demos where session_id = ? order by id desc limit 1000 offset 1";

        $this->delete_demo_query = "delete from demos where id = ?";

        $this->get_demo_for_timeout_query = "select * from demos where session_id = ? order by id desc limit 1";

        // Components
        $this->create_components_table_query = "create table if not exists components (id bigint unsigned auto_increment primary key, file_name varchar(255), constructor varchar(255), content text, created_at timestamp default CURRENT_TIMESTAMP, updated_at timestamp default CURRENT_TIMESTAMP, demo_id bigint unsigned, constraint fk_component_to_demo foreign key(demo_id) references demos(id))";

        $this->insert_components_query = "insert into components(file_name, constructor, content, created_at, updated_at, demo_id) values(?, ?, ?, ?, ?, ?)";

        $this->update_component_query = "update components set content = ?, updated_at = ? where id = ?";

        $this->get_components_query = "select * from components where demo_id = ?";

        $this->get_component_query = "select * from components where id = ?";

        $this->delete_components_query = "delete from components where demo_id = ?";


    }



}