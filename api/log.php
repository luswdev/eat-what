<?php
$update = $_POST['rid'] ?? false;
$share  = $_GET['pid'] ?? false;

$config = json_decode(file_get_contents("../data/config.json"));

$DBHOST = $config->db->host;
$DBUSER = $config->db->user;
$DBPASS = $config->db->password;
$DBNAME = $config->db->table;

$conn = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);

if ($update) {
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	} else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$ip = $_SERVER["REMOTE_ADDR"];
	}

    $conn = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    $query = "INSERT picked_log(rid, picker_ip) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $update, $ip);
    $stmt->execute();
    $LastID = $conn->insert_id;
    $stmt->close();

    echo $LastID;
} else if ($share) {
    $query = "SELECT a.pid, b.restaurant, b.open_time, a.pick_time, a.picker_ip FROM picked_log AS a LEFT JOIN restaurant_lists AS b ON a.rid = b.rid where a.pid=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $share);
    $stmt->execute();
    $stmt->bind_result($pid, $res, $when, $time, $ip);

    while($stmt->fetch()) {
        $when = $when == "brunch" ? "早餐" : "晚餐";
        echo "
            <tr>
                <td>$pid</td>
                <td>$res</td>
                <td>$when</td>
                <td>$time</td>
                <td>$ip</td>
            </tr>
        ";
    }

    $stmt->close();
} else {
    $query = "SELECT a.pid, b.restaurant, b.open_time, a.pick_time, a.picker_ip FROM picked_log AS a LEFT JOIN restaurant_lists AS b ON a.rid = b.rid ORDER BY pid DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stmt->bind_result($pid, $res, $when, $time, $ip);

    while($stmt->fetch()) {
        $when = $when == "brunch" ? "早餐" : "晚餐";
        echo "
            <tr>
                <td>$pid</td>
                <td>$res</td>
                <td>$when</td>
                <td>$time</td>
                <td>$ip</td>
            </tr>
        ";
    }

    $stmt->close();
}

$conn->close();