<?php 

require_once 'private/be/global_import.php';
require_once 'private/validators/required.php';

$language = 'it';
if (isset($request->get['lang'])) {
    $language = $request->get['lang'];
}

$rules = [
    'name' => [
        new Required($configuration->texts, $language)
    ],
    'schema' => [
        new Required($configuration->texts, $language)
    ]
];

$error_messages = $request->validate($rules);

$query_string = '?demo_id=' . $request->post['demo_id'];

if (count($error_messages) > 0) {

    $session->set_error_messages($error_messages);
    $session->set_old_inputs($request->post);
    
    $host = $configuration->host;

    header("Location: $host/personalizza-demo.php$query_string");

    exit;
    
} else {

    $application->update_demo($request->post['name'], null, $request->post['schema'], $request->post['demo_id']);

    header("Location: $host/demo.php$query_string");

    exit;

}

