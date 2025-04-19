<?php
// 파일명: insert_demo_user.php
require_once 'db.php';

$userid = 'demo';
$password = password_hash('1234', PASSWORD_DEFAULT);  // 안전하게 암호화
$name = '데모 사용자';
$email = 'demo@example.com';

$sql = "INSERT INTO users (userid, password, name, email) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$userid, $password, $name, $email]);

echo "사용자 등록 완료!";
