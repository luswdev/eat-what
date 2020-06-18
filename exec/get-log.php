<?php
$config = json_decode(file_get_contents("../data/config.json"));
$DBHOST = $config->db->host;
$DBUSER = $config->db->user;
$DBPASS = $config->db->password;
$DBNAME = $config->db->table;

$mysqli = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
$sql = "SELECT a.pid, b.restaurant, b.open_time, a.pick_time, a.picker_ip FROM picked_log AS a LEFT JOIN restaurant_lists AS b ON a.rid = b.rid ORDER BY pid ASC";
$stmt = $mysqli->prepare($sql);
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
$mysqli->close();
?>