<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
  echo "로그인이 필요합니다.";
  exit;
}

$investment = (int)$_POST['investment'];
$targetReturn = (float)$_POST['target_return'];

$btc = (float)$_POST['btc'];
$eth = (float)$_POST['eth'];
$alt = (float)$_POST['alt'];

// 수익금 계산
$targetProfit = $investment * ($targetReturn / 100);
$totalWithProfit = $investment + $targetProfit;

// 포트폴리오 분배 계산
$btcAmount = $totalWithProfit * ($btc / 100);
$ethAmount = $totalWithProfit * ($eth / 100);
$altAmount = $totalWithProfit * ($alt / 100);

// DB 저장
$sql = "INSERT INTO simulations (user_id, investment, target_return, btc_ratio, eth_ratio, alt_ratio, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['user_id'], $investment, $targetReturn, $btc, $eth, $alt]);

// 결과 출력
echo "
<div class='card p-3'>
  <h5 class='mb-3'>📊 시뮬레이션 결과</h5>
  <ul class='list-group'>
    <li class='list-group-item'>💰 목표 수익금: <strong>" . number_format($targetProfit) . " 원</strong></li>
    <li class='list-group-item'>📈 총 예상 자산: <strong>" . number_format($totalWithProfit) . " 원</strong></li>
    <li class='list-group-item'>📦 포트폴리오 분배:
      <ul>
        <li>BTC: " . number_format($btcAmount) . " 원</li>
        <li>ETH: " . number_format($ethAmount) . " 원</li>
        <li>Altcoins: " . number_format($altAmount) . " 원</li>
      </ul>
    </li>
  </ul>
</div>
";
