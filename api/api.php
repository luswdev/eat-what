<?php
header('Content-Type: text/plain; charset=utf-8');

require './base.php';

$api = new EatWhat\API();
$info = $api->Run();
print_r(json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

?>
