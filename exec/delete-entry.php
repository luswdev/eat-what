<?php
$del = $_POST['del'] ? $_POST['del'] : "";
$type = $_POST['type'] ? $_POST['type'] : "";
$code = $_POST['code'] ? $_POST['code'] : "";
$old = json_decode(file_get_contents("../data/food.json"));

if ($del && $type && $code == "delete-chip-code") {
    # create a tmp empty array
    $old->tmp = [];
    foreach ($old->$type as $chip) {
        if ($chip != $del) {
            # copy to tmp if this is not target
            $old->tmp[] = $chip;
        } else {
            $key = array_search($del, $old->$type);
            echo "Delete ".$type."[".$key."] => ".$chip;
        }
    }

    # copy result into local and free tmp
    $old->$type = $old->tmp;
    unset($old->tmp);

    #upload new json
    $fh = fopen("../data/food.json", "w");
    fwrite($fh, json_encode($old, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fclose($fh);
}
?>