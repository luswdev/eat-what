<?php
$config = json_decode(file_get_contents('../data/config.json'));
$info = $config->webinfo;
$info = json_encode($info,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
print_r($info);
?>
