<?php
$res  = $_POST['new']  ?? false;
$when = $_POST['when'] ?? false;

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

if ($res && $when) {
    $query = "INSERT restaurant_lists(restaurant, open_time) VALUES (?, ?)";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("ss", $res, $when);
    $stmt->execute();
    
    file_put_contents("../data/access.log", "[$now] ($ip) Add a restaurant \"$res\" to $when.".PHP_EOL, FILE_APPEND);
} else if ($del) {
    $conn = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    $query = "DELETE FROM restaurant_lists WHERE restaurant=?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("s", $del);
	$stmt->execute();
    $stmt->close();

    file_put_contents("../data/access.log", "[$now] ($ip) Delete a restaurant \"$del\".".PHP_EOL, FILE_APPEND);
} else {
    $brunch = [];
    $query = "SELECT * FROM restaurant_lists WHERE open_time='brunch'";
    $stmt = $conn->prepare($query);
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
    $query = "SELECT * FROM restaurant_lists WHERE open_time='dinner'";
    $stmt = $conn->prepare($query);
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

    $food = [
        "brunch" => $brunch,
        "dinner" => $dinner,
    ];
    $food = json_encode($food,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    file_put_contents("../data/access.log", "[$now] ($ip) Access restaurant lists.".PHP_EOL, FILE_APPEND);

    print_r($food);
}

$conn->close();
