<?php
$update = $_POST['rid'] ?? false;
$share  = $_GET['pid']  ?? false;
$list   = $_GET['list'] ?? $_POST['list'] ?? 'ndhu';

include_once('db.php');

if ($update) {
    $query = 'INSERT PickedLog(RID, PickFrom) VALUES (?, ?)';
    $stmt  = $conn->prepare($query);

    $stmt->bind_param('ss', $update, $ip);
    $stmt->execute();

    $LastID = $conn->insert_id;

    $stmt->close();
} else {
    $query = 'SELECT a.PID, b.Restaurant, b.OpenTime, a.PickTime, a.PickFrom FROM PickedLog AS a LEFT JOIN RestaurantList AS b ON a.RID = b.RID ORDER BY PID DESC';
    $stmt  = $conn->prepare($query);

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

    $history = json_encode($history, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    print_r($history);
}

$conn->close();

?>
