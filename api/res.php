<?php
$resid  = $_POST['res']  ?? false;
$res  = $_POST['new']  ?? false;
$when = $_POST['when'] ?? false;
$list = $_GET['list'] ?? $_POST['list'] ?? 'ndhu';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') { 
    parse_str(file_get_contents('php://input'), $_DELETE);
}

$del = $_DELETE['res'] ?? false;

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

if ($resid && $res) {
    $query = "UPDATE restaurant_lists SET restaurant=? WHERE rid=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("ss", $res, $resid);
    $stmt->execute();
    
    file_put_contents("../data/access.log", "[$now] ($ip) <ADD> Add a restaurant \"$res\" to $when/$list.".PHP_EOL, FILE_APPEND);
} else if ($res && $when) {
    $query = "INSERT restaurant_lists(restaurant, open_time, region) VALUES (?, ?, ?)";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("sss", $res, $when, $list);
    $stmt->execute();
    
    file_put_contents("../data/access.log", "[$now] ($ip) <ADD> Add a restaurant \"$res\" to $when/$list.".PHP_EOL, FILE_APPEND);
} else if ($del) {
    $conn = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    $query = "DELETE FROM restaurant_lists WHERE rid=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("s", $del);
	$stmt->execute();
    $stmt->close();

    file_put_contents("../data/access.log", "[$now] ($ip) <DEL> Delete a restaurant \"$del\".".PHP_EOL, FILE_APPEND);
} else {
    $brunch = [];
    $query = "SELECT rid, restaurant FROM restaurant_lists WHERE open_time='brunch' AND region=?";
    $stmt = $conn->prepare($query);
	$stmt->bind_param("s", $list);
    $stmt->execute();
    $stmt->bind_result($rid, $res);
    while ($stmt->fetch()) {
        $new = [
            "rid" => $rid,
            "restaurant" => $res,
        ];
        $brunch[] = $new;
    }
    $stmt->close();

    $dinner = [];
    $query = "SELECT rid, restaurant FROM restaurant_lists WHERE open_time='dinner' AND region=?";
    $stmt = $conn->prepare($query);
	$stmt->bind_param("s", $list);
    $stmt->execute();
    $stmt->bind_result($rid, $res);
    while ($stmt->fetch()) {
        $new = [
            "rid" => $rid,
            "restaurant" => $res,
        ];
        $dinner[] = $new;
    }
    $stmt->close();

    $food = [
        "brunch" => $brunch,
        "dinner" => $dinner,
    ];
    $food = json_encode($food,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    file_put_contents("../data/access.log", "[$now] ($ip) <INFO> Access restaurant lists.".PHP_EOL, FILE_APPEND);

    print_r($food);
}

$conn->close();
