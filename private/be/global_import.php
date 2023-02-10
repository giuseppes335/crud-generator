<?php 

session_start([

    'cookie_lifetime' => 0
]);

require __DIR__ . '/../../vendor/autoload.php';

require_once 'private/include/configuration.php';
require_once 'private/include/request.php';
require_once 'private/include/session.php';
require_once 'private/include/application.php';

$configuration = new Configuration();
$session = new Session();
$application = new Application($configuration);
$request = new Request($_GET, $_POST, $session, $application, $configuration);

?>