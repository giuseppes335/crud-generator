<?php

abstract class Relation {

    function __construct(Array $params) {

        $this->request = $params['request'];

        $this->session = $params['session'];

        $this->application = $params['application'];
        
        $this->auth = null;

        if (isset($params['auth']) && $params['auth']) {
            
            $this->auth = $params['auth'];
            
        }
        
        

    }

    public function get_query_param_part($field, $param) {
        
        $param_part = [];
        
        $index = 0;
        
        while(isset($param[$index]) && isset($param[$index + 1])) {
            
            
            $param_part = [
                'op' => $param[$index + 1],
                'value' => $param[$index]
            ];
            
            $index++;
            
        }
        
        return $param_part;
        
    }


    public function authorized_r($users_table, $resource, $r_resources_groups_table) {

        $logged_user_id = $this->session->get_logged_user();

        $logged_user = $this->application->find($users_table, $logged_user_id);

        if ($logged_user) {

            $resources_groups = $this->application->select($r_resources_groups_table, ['join resources on resources.resources_id = r_resources_groups.r_resources_groups_resource_id'], [
                'r_resources_groups_group_id' => $this->get_query_param_part('r_resources_groups_group_id', [$logged_user['users_group_id'], 'eq'])
            ], [], false, 0, []);

            if (count($resources_groups) > 0) {
                
                $resources_groups0 = array_column($resources_groups, 'resources_name');

                $m_fields = [];

                foreach($resources_groups as $resources_group) {

                    $fields = [];
                    
                    // different from '' or null
                    if ($resources_group['r_resources_groups_fields']) {
                    
                        $fields = explode(',', $resources_group['r_resources_groups_fields']);
                        
                    }

                    $m_fields = array_merge($m_fields, $fields);

                }

                if (in_array($resource, $resources_groups0)) {

                    return $m_fields;

                } else {

                    return false;

                }

            } else {

                return false;

            }

        } else {

            return false;

        }

    }

    public function authorized_ru($users_table, $resource, $ru_resources_groups_table) {

        $logged_user_id = $this->session->get_logged_user();

        $logged_user = $this->application->find($users_table, $logged_user_id);

        if ($logged_user) {

            $resources_groups = $this->application->select($ru_resources_groups_table, ['join resources on resources.resources_id = ru_resources_groups.ru_resources_groups_resource_id'], [
                'ru_resources_groups_group_id' => $this->get_query_param_part('ru_resources_groups_group_id', [$logged_user['users_group_id'], 'eq'])
            ], [], false, 0, []);

            if (count($resources_groups) > 0) {

                $resources_groups0 = array_column($resources_groups, 'resources_name');

                $m_fields = [];

                foreach($resources_groups as $resources_group) {
                    
                    $fields = [];
                    
                    // different from '' or null
                    if ($resources_group['ru_resources_groups_fields']) {
                        
                        $fields = explode(',', $resources_group['ru_resources_groups_fields']);
                        
                    }

                    $m_fields = array_merge($m_fields, $fields);

                }

                if (in_array($resource, $resources_groups0)) {

                    return $m_fields;

                } else {

                    return false;

                }

                

            } else {

                return false;

            }

        } else {

            return false;

        }

    }

    public function authorized_cd($users_table, $resource, $cd_resources_groups_table) {

        $logged_user_id = $this->session->get_logged_user();

        $logged_user = $this->application->find($users_table, $logged_user_id);

        if ($logged_user) {


            $resources_groups = $this->application->select($cd_resources_groups_table, ['join resources on resources.resources_id = cd_resources_groups.cd_resources_groups_resource_id'], [
                'cd_resources_groups_group_id' => $this->get_query_param_part('cd_resources_groups_group_id', [$logged_user['users_group_id'], 'eq'])
            ], [], false, 0, []);

            if (count($resources_groups) > 0) {

                $resources_groups0 = array_column($resources_groups, 'resources_name');

                //print_r($resources_groups);

                //print_r($resource);

                //echo count(array_intersect($resources_groups0, $resources));

                if (in_array($resource, $resources_groups0)) {

                    return true;

                } else {

                    return false;

                }

            } else {

                return false;

            }

        } else {

            return false;

        }

    }
    
    function update_creators() {
        
        if ($this->auth) {
            
            unset($this->creators);
            
            $creators = null;
            
            if (isset($this->auth['storage'])) {
                
                $creators = [];
                
                $logged_user_id = $this->session->get_logged_user();
                
                array_push($creators, $logged_user_id);
                
            } else {
                
                $creators = [];
                
                $logged_user_id = $this->session->get_logged_user();
                
                array_push($creators, $logged_user_id);
                
                $users_table_name = $this->auth['acl']['users_table_name'];
                
                $users_table_name_id = $users_table_name . '_id';
                
                $users_table_name_group_id = $users_table_name . '_group_id';
                
                $sharings_table_name = $this->auth['acl']['sharings_table_name'];
                
                $sharings_table_name_sharing_with = $sharings_table_name . '_sharing_with';
                            
                $sharings_table_name_owner = $sharings_table_name . '_owner';
                
                $logged_users = $this->application->select($users_table_name, [], [
                    $users_table_name_id => $this->get_query_param_part($users_table_name_id, [$logged_user_id, 'eq'])
                ]);
                
                $sharings = $this->application->select($sharings_table_name, [], [
                    $sharings_table_name_sharing_with => $this->get_query_param_part($sharings_table_name_sharing_with, [$logged_users[0][$users_table_name_group_id], 'eq'])
                ]);
                
                foreach($sharings as $sharing) {
                    
                    $sharings_users = $this->application->select($users_table_name, [], [
                        $users_table_name_group_id => $this->get_query_param_part($users_table_name_group_id, [$sharing[$sharings_table_name_owner], 'eq'])
                    ]);
                    
                    if ($sharings_users) {
                        
                        array_push($creators, $sharings_users[0][$users_table_name_id]);
                        
                    }
                       
                }
                
            }
            
            $this->creators = $creators;
            
        }
       
        
        
    }
    
    function print_errors_popup() {
        
        if ($this->session->errors()) {
            
            $popup_errors = $this->session->get_errors('popup_errors');
            
            $errors_output = '';
            
            if ($popup_errors && isset($popup_errors['errors'])) {
                
                $errors_output = implode(', ', $popup_errors['errors']);
                
                $application_host = $this->application->host;
                
                $application_path = $this->application->path;
                
                $img_close = "$application_host/$application_path/img/close_FILL0_wght700_GRAD0_opsz48.png";
                
                $popup_errors_content = <<<EOT
                <div id="error-overlay">
                <div id="popup" style="width: 200px; background-color: #ff5722">
                <div style="text-align: right;">
                <a href="#" class="close-button" onclick="event.preventDefault(); let e = document.getElementById('error-overlay'); e.parentNode.removeChild(e);"><img src="$img_close" alt=""></a>
                </div>
                <div id="popup-content">
                <div style="color: #fff">$errors_output</div>
                </div>
                </div>
                </div>
EOT;
                
                echo $popup_errors_content;
                
                // There errors where cleared
                $this->session->clear_error('popup_errors');
                
            }
            
        }
        
    }

    function update_authorized_fields($readable_only = true) {
        
        if ($this->auth) {
            
            unset($this->authorized_fields);
            
            if (isset($this->auth['storage'])) {
                 
                $users_table_name = $this->auth['storage']['table_name'];
                
                $username_field = $this->auth['storage']['field_username'];
                
                $username = $this->auth['username'];
                
                $rows = $this->application->select($users_table_name, [], [
                    $username_field = $this->get_query_param_part($username_field, [$username, 'eq'])
                ]);
                
                if (count($rows) > 0) {
                    
                    $this->authorized_fields = null;
                    
                } else {
                    
                    $errors = [];
                    
                    array_push($errors, 'Permesso negato.');
                    
                    $this->session->push_errors('popup_errors', [
                        'errors' => $errors
                    ]);
                    
                }
                
            } else {

                $resource = $this->auth['resource'];
                
                $users_table_name = $this->auth['acl']['users_table_name'];
                
                $cruds_table_name = $this->auth['acl']['cruds_table_name'];
                
                $rus_table_name = $this->auth['acl']['rus_table_name'];
                
                $rs_table_name = $this->auth['acl']['rs_table_name'];
                
                
                $cd_authorized = $this->authorized_cd($users_table_name, $resource, $cruds_table_name);

                $ru_authorized = $this->authorized_ru($users_table_name, $resource, $rus_table_name);

                $r_authorized = $this->authorized_r($users_table_name, $resource, $rs_table_name);

                if(false !== $cd_authorized) {
                    
                    $this->authorized_fields = null;
                    
                } else if (false !== $ru_authorized) {
                    
                    $this->authorized_fields = $ru_authorized;
                    
                } else if (false !== $r_authorized && $readable_only) {

                    $this->authorized_fields = $r_authorized;
                    
                } else {
                    
                    $errors = [];
                    
                    array_push($errors, 'Permesso negato.');
                    
                    $this->session->push_errors('popup_errors', [
                        'errors' => $errors
                    ]);
                    
                }

                
            }
            
        }
        
    }
    
    abstract function get();

    abstract function post();

    abstract function put();

    abstract function delete();

}