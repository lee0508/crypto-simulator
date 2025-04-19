<?php
// 결과 처리
$simulator_result = null;
$portfolio_total = null;
$rebalancing_result = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Simulator
  if (isset($_POST['simulator_submit'])) {
    $investment = floatval($_POST['investment']);
    $rate = floatval($_POST['rate']);
    $simulator_result = $investment * (1 + $rate / 100);
  }

  // Portfolio
  if (isset($_POST['portfolio_submit'])) {
    $btc = floatval($_POST['btc']);
    $eth = floatval($_POST['eth']);
    $usdt = floatval($_POST['usdt']);
    $portfolio_total = $btc + $eth + $usdt;
  }

  // Rebalancing
  if (isset($_POST['rebalancing_submit'])) {
    $total = floatval($_POST['total']);
    $btc = floatval($_POST['btc_re']);
    $eth = floatval($_POST['eth_re']);
    $usdt = floatval($_POST['usdt_re']);

    $target_each = $total / 3;

    $assets = [
      'BTC' => $btc,
      'ETH' => $eth,
      'USDT' => $usdt,
    ];

    foreach ($assets as $key => $value) {
      $diff = $value - $target_each;
      if (abs($diff) < 1000) {
        $rebalancing_result[] = ['asset' => $key, 'action' => 'hold', 'amount' => 0];
      } elseif ($diff > 0) {
        $rebalancing_result[] = ['asset' => $key, 'action' => 'sell', 'amount' => round($diff)];
      } else {
        $rebalancing_result[] = ['asset' => $key, 'action' => 'buy', 'amount' => round(abs($diff))];
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Crypto Consulting Tool</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container mt-5">
    <h2 class="text-center mb-4">💹 Crypto Investment Consultant</h2>
    <ul class="nav nav-tabs" id="myTab">
      <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#simulator">Simulator</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#portfolio">Portfolio</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#rebalancing">Rebalancing</a></li>
    </ul>

    <div class="tab-content mt-3">
      <!-- Simulator -->
      <div class="tab-pane fade show active" id="simulator">
        <form method="post">
          <div class="mb-2">
            <input type="number" name="investment" class="form-control" placeholder="Investment amount (₩)" required>
          </div>
          <div class="mb-2">
            <input type="number" name="rate" class="form-control" placeholder="Expected return rate (%)" required>
          </div>
          <button type="submit" name="simulator_submit" class="btn btn-primary">Simulate</button>
        </form>
        <?php if ($simulator_result): ?>
          <div class="alert alert-success mt-3">📈 Future Value: ₩<?= number_format($simulator_result) ?></div>
        <?php endif; ?>
      </div>

      <!-- Portfolio -->
      <div class="tab-pane fade" id="portfolio">
        <form method="post">
          <div class="mb-2">
            <input type="number" name="btc" class="form-control" placeholder="BTC value (₩)" required>
          </div>
          <div class="mb-2">
            <input type="number" name="eth" class="form-control" placeholder="ETH value (₩)" required>
          </div>
          <div class="mb-2">
            <input type="number" name="usdt" class="form-control" placeholder="USDT value (₩)" required>
          </div>
          <button type="submit" name="portfolio_submit" class="btn btn-primary">Calculate Total</button>
        </form>
        <?php if ($portfolio_total): ?>
          <div class="alert alert-info mt-3">💼 Total Portfolio Value: ₩<?= number_format($portfolio_total) ?></div>
        <?php endif; ?>
      </div>

      <!-- Rebalancing -->
      <div class="tab-pane fade" id="rebalancing">
        <form method="post">
          <div class="mb-2">
            <input type="number" name="total" class="form-control" placeholder="Total Asset Value (₩)" required>
          </div>
          <div class="mb-2">
            <input type="number" name="btc_re" class="form-control" placeholder="BTC current value" required>
          </div>
          <div class="mb-2">
            <input type="number" name="eth_re" class="form-control" placeholder="ETH current value" required>
          </div>
          <div class="mb-2">
            <input type="number" name="usdt_re" class="form-control" placeholder="USDT current value" required>
          </div>
          <button type="submit" name="rebalancing_submit" class="btn btn-success">Rebalance</button>
        </form>
        <?php if (!empty($rebalancing_result)): ?>
          <div class="mt-3">
            <?php foreach ($rebalancing_result as $r): ?>
              <div class="alert alert-secondary">
                <?php if ($r['action'] == 'buy'): ?>
                  🟢 Buy <?= $r['asset'] ?>: ₩<?= number_format($r['amount']) ?>
                <?php elseif ($r['action'] == 'sell'): ?>
                  🔴 Sell <?= $r['asset'] ?>: ₩<?= number_format($r['amount']) ?>
                <?php else: ?>
                  🟡 Hold <?= $r['asset'] ?>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>