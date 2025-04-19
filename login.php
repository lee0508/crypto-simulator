<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userid = $_POST['userid'];
  $passwd = $_POST['passwd'];

  // 이 부분은 DB 사용자 확인 로직으로 바꾸면 됩니다
  if ($userid === 'demo' && $passwd === '1234') {
    $_SESSION['user_id'] = $userid;
    header('Location: index5.php');
    exit;
  } else {
    echo "<script>alert('아이디 또는 비밀번호가 틀립니다');history.back();</script>";
    exit;
  }
}
