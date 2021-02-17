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
    $query = 'UPDATE region_lists SET name=?, `update-ip`=? WHERE regid=?';
	$stmt = $conn->prepare($query);
	$stmt->bind_param('sss', $new, $ip, $regid);
    $stmt->execute();
} else if ($new && $id) {
    $query = 'INSERT region_lists(regid, `name`, `update-ip`) VALUES (?, ?, ?)';
    $stmt = $conn->prepare($query);
	$stmt->bind_param('sss', $id, $new, $ip);
    $stmt->execute();
    $stmt->close();
} else if ($del) {
    $query = 'DELETE FROM region_lists WHERE `name`=?';
	$stmt = $conn->prepare($query);
	$stmt->bind_param('s', $del);
	$stmt->execute();
    $stmt->close();
} else {
    $query = 'SELECT regid, `name` FROM region_lists ORDER BY uuid ASC';
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