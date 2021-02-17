<?php
$resid  = $_POST['res']  ?? false;
$res    = $_POST['new']  ?? false;
$when   = $_POST['when'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') { 
    $_DELETE = json_decode(file_get_contents('php://input'), true);
}
$del  = $_DELETE['res'] ?? false;
$list = $_GET['list']   ?? $_POST['list'] ?? $_DELETE['list'] ?? 'ndhu';

include_once('db.php');

if ($resid && $res) {
    $query = 'UPDATE restaurant_lists SET restaurant=?, `update-ip`=? WHERE rid=?';
	$stmt  = $conn->prepare($query);
	$stmt->bind_param('sss', $res, $ip, $resid);
    $stmt->execute();
} else if ($res && $when) {
    $query = 'INSERT restaurant_lists(restaurant, open_time, region, `update-ip`) VALUES (?, ?, ?, ?)';
	$stmt  = $conn->prepare($query);
	$stmt->bind_param('ssss', $res, $when, $list, $ip);
    $stmt->execute();
} else if ($del) {
    $query = 'DELETE FROM restaurant_lists WHERE rid=?';
	$stmt  = $conn->prepare($query);
	$stmt->bind_param('s', $del);
	$stmt->execute();
    $stmt->close();
} else {
    $query = 'SELECT rid, restaurant FROM restaurant_lists WHERE open_time=\'brunch\' AND region=?';
    $stmt  = $conn->prepare($query);
	$stmt->bind_param('s', $list);
    $stmt->execute();
    $stmt->bind_result($rid, $res);
    while ($stmt->fetch()) {
        $brunch[] = [
            'rid' => $rid,
            'restaurant' => $res,
        ];
    }
    $stmt->close();

    $query = 'SELECT rid, restaurant FROM restaurant_lists WHERE open_time=\'dinner\' AND region=?';
    $stmt  = $conn->prepare($query);
	$stmt->bind_param('s', $list);
    $stmt->execute();
    $stmt->bind_result($rid, $res);
    while ($stmt->fetch()) {
        $dinner[] = [
            'rid' => $rid,
            'restaurant' => $res,
        ];
    }
    $stmt->close();

    $food = [
        'brunch' => $brunch,
        'dinner' => $dinner,
    ];
    
    $food = json_encode($food,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    print_r($food);
}

$conn->close();
?>