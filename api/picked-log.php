<?php
$update = $_POST['rid'] ?? false;
$share  = $_GET['pid'] ?? false;
$list = $_GET['list'] ?? $_POST['list'] ?? 'ndhu';

include_once('db.php');

if ($update) {
    $conn = new mysqli($DBHOST, $DBUSER, $DBPASS, $DBNAME);
    $query = 'INSERT picked_log(rid, picker_ip) VALUES (?, ?)';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $update, $ip);
    $stmt->execute();
    $LastID = $conn->insert_id;
    $stmt->close();
} else {
    $query = 'SELECT a.pid, b.restaurant, b.open_time, a.pick_time, a.picker_ip FROM picked_log AS a LEFT JOIN restaurant_lists AS b ON a.rid = b.rid ORDER BY pid DESC';
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stmt->bind_result($pid, $res, $when, $time, $ip_r);
    $history = [];

    while($stmt->fetch()) {
        $when = $when == 'brunch' ? '早餐' : '晚餐';
        $history[] = [
            'pid' => $pid,
            'restaurant' => $res,
            'when' => $when,
            'time' => $time,
            'ip' => $ip_r
        ];
    }

    $stmt->close();

    $history = json_encode($history,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    print_r($history);
}

$conn->close();
?>