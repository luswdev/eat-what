<?php
$regid = $_POST['reg'] ?? false;
$new = $_POST['new'] ?? false;
$id = $_POST['id'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') { 
    parse_str(file_get_contents('php://input'), $_DELETE);
}

$del = $_DELETE['del'] ?? false;

$config = json_decode(file_get_contents("../data/config.json"));
$DBHOST = $config->db->host;
$DBUSER = $config->db->user;
$DBPASS = $config->db->password;
$DBNAME = $config->db->table;

$conn = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);

if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
    $ip = $_SERVER["HTTP_CLIENT_IP"];
} else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
    $ip = $_SERVER["REMOTE_ADDR"];
}

$now = date("Y/m/d H:i:s");

if ($regid && $new) {    
    $query = "UPDATE region_lists SET name=? WHERE regid=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("ss", $new, $regid);
    $stmt->execute();
    
    file_put_contents("../data/access.log", "[$now] ($ip) <ADD> Add a new region \"$res\" to $when/$list.".PHP_EOL, FILE_APPEND);
} else if ($new && $id) {
    $query = "INSERT region_lists(regid, `name`) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
	$stmt->bind_param("ss", $id, $new);
    $stmt->execute();
    $stmt->close();

    file_put_contents("../data/access.log", "[$now] ($ip) <ADD> Add a new region \"$new/$id\"".PHP_EOL, FILE_APPEND);
} else if ($del) {
    $query = "DELETE FROM region_lists WHERE `name`=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("s", $del);
	$stmt->execute();
    $stmt->close();

    file_put_contents("../data/access.log", "[$now] ($ip) <DEL> Delete a region \"$del\".".PHP_EOL, FILE_APPEND);
} else {
    $regs = [];
    $query = "SELECT regid, `name` FROM region_lists ORDER BY uuid ASC";
    $stmt = $conn->prepare($query);
	$stmt->bind_param("s", $list);
    $stmt->execute();
    $stmt->bind_result($rid, $reg);
    while ($stmt->fetch()) {
        $new = [
            "name" => $rid,
            "title" => $reg,
        ];
        $regs[] = $new;
    }
    $stmt->close();

    file_put_contents("../data/access.log", "[$now] ($ip) <INFO> Access region lists.".PHP_EOL, FILE_APPEND);
    $regs = json_encode($regs,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    print_r($regs);
}

$conn->close();
