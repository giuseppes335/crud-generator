<?php 

require_once 'private/be/global_import.php';
require_once 'hzip.php';

$demo_id = $request->get['demo_id'];

HZip::zipDir("private/demos/$demo_id", "private/demos/$demo_id/$demo_id.zip");

chmod("private/demos/$demo_id/$demo_id.zip", 0755);

$host = $configuration->host;
 
header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=\"". $demo_id .".zip\""); 

readfile ("private/demos/$demo_id/$demo_id.zip");
exit;