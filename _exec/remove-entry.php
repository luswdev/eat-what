<?php
$del = $_POST['del'] ? $_POST['del'] : "";
$type = $_POST['type'] ? $_POST['type'] : "";
$code = $_POST['code'] ? $_POST['code'] : "";
$old = json_decode(file_get_contents("../_data/food.json"));

if ($del && $type && $code = "remove-chip-code") {
    $old->tmp = [];
    foreach ($old->$type as $chip) {
        if ($chip != $del) {
            $old->tmp[] = $chip;
        } else {
            echo "Delete ".$type."[".$key."] => ".$chip;
        }
    }

    $old->$type = $old->tmp;
    unset($old->tmp);

    $fh = fopen("../_data/food.json", "w");
    fwrite($fh, json_encode($old, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fclose($fh);
}
?>