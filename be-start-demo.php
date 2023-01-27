<?php 

require_once 'private/be/global_import.php';
require_once 'private/validators/required.php';

$language = 'it';
if (isset($request->get['lang'])) {
    $language = $request->get['lang'];
}

$rules = [
];

$error_messages = $request->validate($rules);

if (count($error_messages) > 0) {

    $session->set_error_messages($error_messages);
    $session->set_old_inputs($request->post);
    
    $host = $configuration->host;

    header("Location: $host/create_demo.php");

    exit;
    
} else {

    $session_id = $session->get_session_id();

    $template_id = $request->get['template_id'];

    $template = $application->get_template($template_id);

    $demo_name = "Demo creata il: " . date('Y-m-d H:i:s');

    $demo_id = $application->insert_demo($demo_name, $template['default_schema'], $session_id, $template_id);








    $application->delete_components($demo_id);

    $files = scandir($template['path']);

    array_shift($files);

    array_shift($files);

    foreach($files as $file) {

        $file_content = file_get_contents($template['path'] . '/' . $file);

        preg_match('/__construct\(.*\)/', $file_content, $matches);

        $application->insert_component($file, $matches[0], $file_content, $demo_id); 

    }





    $host = $configuration->host;

    header("Location: $host/preview.php?demo_id=$demo_id");

    exit;

}

