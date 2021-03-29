<?php
$regid = $_POST['reg'] ?? false;
$new = $_POST['new'] ?? false;
$id = $_POST['id'] ?? false;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') { 
    $_DELETE = json_decode(file_get_contents('php://input'), true);
}
$del = $_DELETE['del'] ?? false;

include_once('db.php');

if ($regid && $new) {
    $query = 'SELECT RegionName FROM RegionList WHERE RegionID=?';
	$stmt  = $conn->prepare($query);
	$stmt->bind_param('s', $regid);
    $stmt->execute();
    $stmt->bind_result($backupName);
    $stmt->fetch();
    $stmt->close();

    $query = 'UPDATE RegionList SET RegionName=?, UpdatedFrom=?, BackupName=? WHERE RegionID=?';
	$stmt = $conn->prepare($query);
	$stmt->bind_param('ssss', $new, $ip, $backupName, $regid);
    $stmt->execute();
} else if ($new && $id) {
    $query = 'INSERT RegionList(RegionID, RegionName, UpdatedFrom) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($query);
	$stmt->bind_param('sss', $id, $new, $ip);
    $stmt->execute();
    $stmt->close();
} else if ($del) {
    $query = 'DELETE FROM RegionList WHERE RegionName=?';
	$stmt = $conn->prepare($query);
	$stmt->bind_param('s', $del);
	$stmt->execute();
    $stmt->close();
} else {
    $query = 'SELECT RegionID, RegionName FROM RegionList ORDER BY UUID ASC';
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $stmt->bind_result($rid, $reg);
    while ($stmt->fetch()) {
        $regs[] = [
            'name' => $rid,
            'title' => $reg,
        ];
    }
    $stmt->close();

    $regs = json_encode($regs,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    print_r($regs);
}

$conn->close();

?>
