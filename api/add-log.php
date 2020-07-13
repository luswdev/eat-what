<?php
$rid = isset($_POST['rid']) ? $_POST['rid'] : '';

if ($rid) {
    $config = json_decode(file_get_contents("../data/config.json"));
    $DBHOST = $config->db->host;
    $DBUSER = $config->db->user;
    $DBPASS = $config->db->password;
    $DBNAME = $config->db->table;

    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	} else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$ip = $_SERVER["REMOTE_ADDR"];
	}

    $mysqli = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    $sql = "INSERT picked_log(rid, picker_ip) VALUES (?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $rid, $ip);
    $stmt->execute();
    $LastID = $mysqli->insert_id;
    $stmt->close();
    $mysqli->close();

    echo $LastID;
}