<?php
$name = $_POST['name'] ?? 'Anonymous';
$mail = $_POST['mail'] ?? 'unknown@lusw.dev';
$subj = $_POST['subj'] ?? '';
$mesg = $_POST['mesg'] ?? '';

if ($name == '') {
    $name = 'Anonymous';
}
if ($mail == '') {
    $mail = 'unknown@lusw.dev';
}

$config = json_decode(file_get_contents('../data/config.json'));

if ($mesg) {
    $to       = $config->feedback->mailto;
    $subject  = '吃什麼-意見回饋 ['.$subj.']';
    $message  = $mesg;
    $headers  = 'From: 吃什麼-意見回饋 <no-reply@lusw.dev>' . '\r\n';
    $headers .= 'Reply-To: '.$name.' <'.$mail.'>' . '\r\n';
    $headers .= 'Organization: LuSkywalker\r\n';
    $headers .= 'MIME-Version: 1.0\r\n';
    $headers .= 'Content-type: text/html; charset=utf-8\r\n';
    $headers .= 'X-Priority: 3\r\n';
    $headers .= 'Return-Path: no-reply@lusw.dev\r\n';
    $headers .= 'X-Mailer: PHP/'.phpversion();
    $res = mail($to, $subject, $message, $headers, '-fno-reply@lusw.dev');
}

?>
