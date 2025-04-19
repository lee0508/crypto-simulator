<?php
$mysqli = new mysqli("localhost", "root", "1234", "crypto_insight");
if ($mysqli->connect_error) die("DB 연결 실패: " . $mysqli->connect_error);

// 초기값
$investment = 30000000;
$rate = 20;
$btc_ratio = 42;
$eth_ratio = 32;
$usdt_ratio = 26;
$error = '';
$result = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $investment = (int)$_POST["investment"];
  $rate = (float)$_POST["rate"];
  $btc_ratio = (float)$_POST["btc_ratio"];
  $eth_ratio = (float)$_POST["eth_ratio"];
  $usdt_ratio = (float)$_POST["usdt_ratio"];

  // 비율 합계 검증
  if ($btc_ratio + $eth_ratio + $usdt_ratio > 100) {
    $error = "⚠️ BTC, ETH, USDT 비율 합계는 반드시 100%이어야 합니다.";
  } else {
    $profit = round($investment * (1 + $rate / 100));
    $btc = round($investment * ($btc_ratio / 100));
    $eth = round($investment * ($eth_ratio / 100));
    $usdt = round($investment * ($usdt_ratio / 100));

    $equal_share = round($investment / 3);
    $rebalance = [
      "BTC" => $btc - $equal_share,
      "ETH" => $eth - $equal_share,
      "USDT" => $usdt - $equal_share
    ];

    $stmt = $mysqli->prepare("INSERT INTO simulation_log (amount, profit, btc, eth, usdt, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiiii", $investment, $profit, $btc, $eth, $usdt);
    $stmt->execute();
    $stmt->close();

    $result = compact('investment', 'profit', 'btc', 'eth', 'usdt', 'rebalance');
  }
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <title>Crypto 투자 시뮬레이터</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-light">
  <div class="container py-5">
    <div class="text-center mb-4">
      <h1>💸 Crypto 투자 시뮬레이터</h1>
      <p class="text-muted">목표 수익률 & 포트폴리오 비율 설정</p>
    </div>

    <form method="POST" class="card p-4 shadow-sm mb-4">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">📥 투자금액 (₩)</label>
          <input type="number" name="investment" class="form-control" value="<?= $investment ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">🎯 목표 수익률 (%)</label>
          <input type="number" name="rate" class="form-control" step="0.1" value="<?= $rate ?>" required>
        </div>
      </div>

      <h5 class="mt-3">⚙️ 포트폴리오 비율 (%)</h5>
      <div class="row mb-3">
        <div class="col-md-4">
          <label>BTC</label>
          <input type="number" name="btc_ratio" class="form-control" step="0.1" value="<?= $btc_ratio ?>" required>
        </div>
        <div class="col-md-4">
          <label>ETH</label>
          <input type="number" name="eth_ratio" class="form-control" step="0.1" value="<?= $eth_ratio ?>" required>
        </div>
        <div class="col-md-4">
          <label>USDT</label>
          <input type="number" name="usdt_ratio" class="form-control" step="0.1" value="<?= $usdt_ratio ?>" required>
        </div>
      </div>

      <button class="btn btn-primary w-100">💡 시뮬레이션 실행</button>
    </form>

    <?php if ($result): ?>
      <div class="card p-4 shadow-sm mb-4">
        <h4 class="mb-3">📈 결과 요약</h4>
        <ul class="list-group mb-3">
          <li class="list-group-item">투자금: <strong><?= number_format($result['investment']) ?> 원</strong></li>
          <li class="list-group-item">목표 수익: <strong><?= number_format($result['profit']) ?> 원 (<?= $rate ?>%)</strong></li>
        </ul>

        <h5>💼 포트폴리오 구성</h5>
        <ul class="list-group mb-3">
          <li class="list-group-item">BTC: <?= number_format($result['btc']) ?> 원</li>
          <li class="list-group-item">ETH: <?= number_format($result['eth']) ?> 원</li>
          <li class="list-group-item">USDT: <?= number_format($result['usdt']) ?> 원</li>
        </ul>

        <canvas id="portfolioChart" height="180"></canvas>

        <h5 class="mt-4">⚖️ 리밸런싱 가이드</h5>
        <ul class="list-group mb-3">
          <?php foreach ($result['rebalance'] as $coin => $diff): ?>
            <li class="list-group-item">
              <?= $coin ?>:
              <?php if ($diff > 0): ?>
                <span class="text-danger">매도 <?= number_format($diff) ?> 원</span>
              <?php elseif ($diff < 0): ?>
                <span class="text-success">매수 <?= number_format(abs($diff)) ?> 원</span>
              <?php else: ?>
                <span class="text-muted">변동 없음</span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>

        <button class="btn btn-outline-secondary" onclick="window.print()">🖨️ 출력</button>
      </div>
    <?php endif; ?>
  </div>

  <?php if ($result): ?>
    <script>
      const ctx = document.getElementById('portfolioChart').getContext('2d');
      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: ['BTC', 'ETH', 'USDT'],
          datasets: [{
            data: [<?= $result['btc'] ?>, <?= $result['eth'] ?>, <?= $result['usdt'] ?>],
            backgroundColor: ['#f7931a', '#3c3c3d', '#26a17b']
          }]
        },
        options: {
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    </script>
  <?php endif; ?>
</body>

</html>