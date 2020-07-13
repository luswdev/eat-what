<?php
$res = isset($_POST['new']) ? $_POST['new'] : '';
$when = isset($_POST['when']) ? $_POST['when'] : '';

if ($res && $when) {
    $config = json_decode(file_get_contents("../data/config.json"));
    $DBHOST = $config->db->host;
    $DBUSER = $config->db->user;
    $DBPASS = $config->db->password;
    $DBNAME = $config->db->table;

    $mysqli = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    $sql = "INSERT restaurant_lists(restaurant, open_time) VALUES (?, ?)";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("ss", $res, $when);
	$stmt->execute();
    $stmt->close();
    $mysqli->close();
}
?>