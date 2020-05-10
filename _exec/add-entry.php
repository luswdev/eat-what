<?php
$new = $_POST['new'] ? $_POST['new'] : "";
$type = $_POST['type'] ? $_POST['type'] : "";
$code = $_POST['code'] ? $_POST['code'] : "";
$old = json_decode(file_get_contents("../_data/food.json"));

if ($new && $type && $code = "new-chip-code") {
    $key = array_search($new, $old->$type);
    if (!$key) {
        $old->$type[] = $new;
        $key = array_search($new, $old->$type);
        echo "Add ".$type."[".$key."] => ".$new;
    }

    $fh = fopen("../_data/food.json", "w");
    fwrite($fh, json_encode($old, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fclose($fh);
}
?>