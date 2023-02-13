<?php 

require_once 'private/be/global_import.php';
require_once 'private/include/custom-schema.php';

function del_tree($dir) {
    $files = array_diff(scandir($dir), array('.','..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? del_tree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}



$session_id = $session->get_session_id();

$template_id = $_POST['template_id'];

$template = $application->get_template($template_id);

$demo_name = $_POST['name'];

$simple_schema = $_POST['schema'];

$demo_id = '';

$params = [];

$paramsa['database_host'] = $_POST['database_host'];

$paramsa['database_username'] = $_POST['database_username'];

$paramsa['database_password'] = $_POST['database_password'];

$paramsa['database_name'] = $_POST['database_name'];

$params = json_encode($paramsa);

if (isset($_GET['demo_id']) && $_GET['demo_id']) {
    
    $demo_id = $_GET['demo_id'];
    
    $application->update_demo($demo_name, $params, $simple_schema, $demo_id, $template_id);
    
} else {
    
    $demo_id = $application->insert_demo($demo_name, $params, $simple_schema, $session_id, $template_id);
    
}





$custom_schema = new CustomSchema($simple_schema, $paramsa);

$schema = spyc_load($custom_schema->get());

if (file_exists('private/demos/' . $demo_id)) {
    del_tree('private/demos/' . $demo_id);
}

mkdir('private/demos/' . $demo_id . '/include', 0755, true);

mkdir('private/demos/' . $demo_id . '/img', 0755, true);

mkdir('private/demos/' . $demo_id . '/database', 0755, true);

touch('private/demos/' . $demo_id . '/database/demos.dbi');


/*
if (!isset($request->get['debug'])) {

    $components = $application->get_components($request->get['demo_id']);

    foreach($components as $component) {

        $filename = 'private/demos/' . $request->get['demo_id'] . '/include/' . $component['file_name'];
        file_put_contents($filename, $component['content']);
        chmod($filename, 0755);
    }

    foreach($schema['imports'] as $trigger) {

        $filename = 'private/triggers/' . $trigger;

        $destination = 'private/demos/' . $request->get['demo_id'] . '/include/' . $trigger;
        
        copy($filename, $destination);

        chmod($destination, 0755);
    }

}
*/

$images = scandir($_SERVER['DOCUMENT_ROOT']. '/img');

array_shift($images);

array_shift($images);

foreach($images as $file) {

    $filename = $_SERVER['DOCUMENT_ROOT']. '/img/' . $file;

    $destination = 'private/demos/' . $demo_id . '/img/' . $file;
    
    copy($filename, $destination);

    chmod($destination, 0755);

}



// TODO
function node_is_component($node) {
    return is_array($node) && isset($node['name']) && isset($node['component']);
}

// TODO
function node_is_generic_map($node) {
    return is_array($node) && !isset($node[0]);
}

// TODO
function node_is_array($node) {
    return is_array($node);
}

function get_php_output_recursive($node, &$dependencies, &$imports) {

    $params_list_output = '';

    $params_list_output_array = [];

    $component_output = '';

    if (node_is_component($node)) {

        $current_type_0 = preg_replace_callback('/-([a-z]{1})/', function($matches) {

            return strtoupper($matches[1]);
    
        }, $node['component']);
    
        $current_type_1 = ucfirst(explode('.', $current_type_0)[0]);

        $var_name = $node['name'];

        if (!in_array($var_name, $dependencies)) {

            $prev_c_o = '';
            
            if (isset($node['params'])) {
                
                foreach($node['params'] as $key => $param) {

                    $output = get_php_output_recursive($param, $dependencies, $imports);

                    array_push($params_list_output_array, "'$key' => $output[0]");

                    $prev_c_o .= $output[1];

                }

            } 

            $params_list_output = "[" . implode(", ", $params_list_output_array) . "]";

            $var_name = str_replace('-', '_', $var_name);

            $component_output = $prev_c_o . "\$$var_name = new $current_type_1($params_list_output);\n";
            
            array_push($dependencies, $var_name);

        }

        if (!in_array($node['component'], $imports)) {
            
            array_push($imports, $node['component']);

        }

        return ["\$$var_name", $component_output];

    } else if (node_is_generic_map($node)) {

        $out = '';

        $params = [];

        $keys = array_keys($node);

        if ($keys) {

            foreach($keys as $key) {

                $child = $node[$key];

                $output = get_php_output_recursive($child, $dependencies, $imports);

                $out .= $output[1];

                array_push($params, "'$key' => $output[0]");

            }

        }

        $params2 = implode(', ', $params);

        return ["[$params2]", $out];

    } else if (node_is_array($node)) {

        $out = '';

        $params = [];

        foreach($node as $child) {

            $output = get_php_output_recursive($child, $dependencies, $imports);

            $out .= $output[1];

            array_push($params, $output[0]);

        }

        $params2 = implode(', ', $params);

        return ["[$params2]", $out];

    } else {

        if ($params_list_output === '') {
            
            $params_list_output .= "'$node'";

        } else {

            $params_list_output .= ", '$node'";

        }

        return [$params_list_output, ''];
        
    }

}

foreach($schema['components'] as $component) {

    $imports = [];

    $dependencies = [];

    $php_open_tag = "<?php if(session_status() === PHP_SESSION_NONE) session_start();\n";

    $output0 = get_php_output_recursive($component, $dependencies, $imports);

    $output1 = $output0[1];

    $output2 = <<<EOT
    if ('GET' === \$_SERVER['REQUEST_METHOD']) {
        $output0[0]->get();
    } else if ('POST' === \$_SERVER['REQUEST_METHOD']) {
        $output0[0]->post();
    }  
EOT;

    $imports_map = array_map(function($component) use ($request, $template, $schema) {
        
        if (!isset($request->get['debug'])) {

            return "require_once 'include/$component';";

        } else {

            $imports = $schema['imports'];

            $path = '';

            if (in_array($component, $imports)) {

                return "require_once '../../triggers/$component';";

            } else {

                $path = $template['path'];

                return "require_once '../../../$path/$component';";

            }

            

        }
        
    }, $imports);

    $imports_output = implode("\n", $imports_map) . "\n";

    $filename = 'private/demos/' . $demo_id . '/' . $component['name'] . '.php';
    file_put_contents('private/demos/' . $demo_id . '/' . $component['name'] . '.php', $php_open_tag . $imports_output . $output1 . $output2);
    chmod($filename, 0755);

}

//copy('LICENSE', 'private/demos/' . $request->get['demo_id'] . '/LICENSE');
//chmod('LICENSE', 'private/demos/' . $request->get['demo_id'] . '/LICENSE', 0755);



$reset = '';

if (isset($request->get['reset'])) {

    $reset = '?reset';

}

header("Location: private/demos/$demo_id/$reset");

exit;



