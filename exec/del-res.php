<?php
$res = isset($_POST['res']) ? $_POST['res'] : '';

if ($res) {
    $config = json_decode(file_get_contents("../data/config.json"));
    $DBHOST = $config->db->host;
    $DBUSER = $config->db->user;
    $DBPASS = $config->db->password;
    $DBNAME = $config->db->table;

    $mysqli = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    $sql = "DELETE FROM restaurant_lists WHERE restaurant=?";
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param("s", $res);
	$stmt->execute();
    $stmt->close();
    $mysqli->close();
}
?>