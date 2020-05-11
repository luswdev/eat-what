<?php
$new = $_POST['new'] ? $_POST['new'] : "";
$type = $_POST['type'] ? $_POST['type'] : "";
$code = $_POST['code'] ? $_POST['code'] : "";
$old = json_decode(file_get_contents("../data/food.json"));

if ($new && $type && $code == "add-chip-code") {
    # check if this is a new entry
    $key = array_search($new, $old->$type);
    if (!$key) {
        # insert new entry into rear
        $old->$type[] = $new;
        $key = array_search($new, $old->$type);
        echo "Add ".$type."[".$key."] => ".$new;
    }

    # upload new json
    $fh = fopen("../data/food.json", "w");
    fwrite($fh, json_encode($old, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fclose($fh);
}
?>