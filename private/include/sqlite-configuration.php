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


        // Templates
        $this->create_templates_table_query = "create table if not exists templates (id integer primary key autoincrement, name varchar(255) unique, description varchar(255) null, path varchar(255), default_params text null, default_schema text null, credits integer, image_name varchar(255) null, created_at timestamp, updated_at timestamp)";

        $this->get_templates_query = "select * from templates";

        $this->get_template_query = "select * from templates where id = ?";

        // Demos
        $this->create_demos_table_query = "create table if not exists demos (id integer primary key autoincrement, name varchar(255) null unique, params json null, schema0 text null, created_at timestamp, updated_at timestamp, session_id varchar(255), template_id integer, constraint fk_demo_to_template foreign key(template_id) references templates(id))";

        $this->insert_demo_query = "insert into demos(name, params, schema0, created_at, updated_at, session_id, template_id) values(?, ?, ?, ?, ?, ?, ?)";

        $this->update_demo_query = "update demos set name = ?, params = ?, schema0 = ?, updated_at = ? where id = ?";


        $this->get_demos_query = "select demos.*, templates.name as template_name from demos join templates on templates.id = demos.template_id where session_id = ? order by created_at desc limit 1";

        $this->get_demo_query = "select * from demos where id = ?";

        $this->delete_demo_query = "delete from demos where id = ?";


    }



}