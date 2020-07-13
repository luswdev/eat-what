<?php
$config = json_decode(file_get_contents("../data/config.json"));
$DBHOST = $config->db->host;
$DBUSER = $config->db->user;
$DBPASS = $config->db->password;
$DBNAME = $config->db->table;

$mysqli = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);

$brunch = [];
$sql = "SELECT * FROM restaurant_lists WHERE open_time='brunch'";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$stmt->bind_result($rid, $res, $when);
while ($stmt->fetch()) {
    $new = [
        "rid" => $rid,
        "restaurant" => $res,
    ];
    $brunch[] = $new;
}
$stmt->close();

$dinner = [];
$sql = "SELECT * FROM restaurant_lists WHERE open_time='dinner'";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$stmt->bind_result($rid, $res, $when);
while ($stmt->fetch()) {
    $new = [
        "rid" => $rid,
        "restaurant" => $res,
    ];
    $dinner[] = $new;
}
$stmt->close();

$mysqli->close();

$food = [
    "brunch" => $brunch,
    "dinner" => $dinner,
];
$food = json_encode($food,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

print_r($food);
?>