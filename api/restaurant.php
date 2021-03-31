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
    $query = 'SELECT Restaurant FROM RestaurantList WHERE RID=?';
	$stmt  = $conn->prepare($query);
	$stmt->bind_param('s', $resid);
    $stmt->execute();
    $stmt->bind_result($backupName);
    $stmt->fetch();
    $stmt->close();

    $query = 'UPDATE RestaurantList SET BackupName=?, Restaurant=?, UpdatedFrom=? WHERE RID=?';
	$stmt  = $conn->prepare($query);
	$stmt->bind_param('ssss', $backupName, $res, $ip, $resid);
    $stmt->execute();
    $stmt->close();
} else if ($res && $when) {
    $query = 'INSERT RestaurantList(restaurant, OpenTime, Region, UpdatedFrom, BackupName) VALUES (?, ?, ?, ?, ?)';
	$stmt  = $conn->prepare($query);
	$stmt->bind_param('sssss', $res, $when, $list, $ip, $res);
    $stmt->execute();
} else if ($del) {
    $query = 'DELETE FROM RestaurantList WHERE rid=?';
	$stmt  = $conn->prepare($query);
	$stmt->bind_param('s', $del);
	$stmt->execute();
    $stmt->close();
} else {
    $query = 'SELECT RID, Restaurant FROM RestaurantList WHERE OpenTime=\'brunch\' AND Region=?';
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

    $query = 'SELECT RID, Restaurant FROM RestaurantList WHERE OpenTime=\'dinner\' AND Region=?';
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
