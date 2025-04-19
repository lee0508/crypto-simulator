<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userid = $_POST['userid'];
  $passwd = $_POST['passwd'];

  $sql = "SELECT * FROM users WHERE userid = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$userid]);
  $user = $stmt->fetch();

  if ($user && password_verify($passwd, $user['password'])) {
    $_SESSION['user_id'] = $user['userid'];
    $_SESSION['name'] = $user['name'];
    header("Location: index.php");
    exit;
  } else {
    echo "<script>alert('아이디 또는 비밀번호가 틀립니다');history.back();</script>";
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Crypto Investment Simulator - 투자 시뮬레이터">
  <title>Crypto Investment Simulator</title>

  <!-- Bootstrap5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- html2pdf.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <!-- html2canvas.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
  <!-- jsPDF.js -->
  <!-- https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>

  <!-- fontawesome.js -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
  <style>
    body {
      padding: 2rem;
    }

    .login-box {
      max-width: 400px;
      margin: 100px auto;
    }

    canvas {
      max-width: 100%;
    }

    .chart-container {
      display: none;
    }
  </style>
</head>

<body>

  <?php if (!isset($_SESSION['user_id'])): ?>
    <!-- ✅ 로그인되지 않은 경우 -->
    <div class="login-box card p-4 shadow">
      <h3 class="text-center"><i class="fas fa-user"></i> 로그인</h3>
      <form method="post" action="login.php">
        <div class="mb-3">
          <label for="userid" class="form-label">아이디</label>
          <input type="text" class="form-control" id="userid" name="userid" required>
        </div>
        <div class="mb-3">
          <label for="passwd" class="form-label">비밀번호</label>
          <input type="password" class="form-control" id="passwd" name="passwd" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">로그인</button>
      </form>

      <!-- SNS 로그인 버튼 (예: Google) -->
      <hr>
      <div class="text-center">또는</div>
      <div class="text-center mt-2">
        <a href="google-login-start.php" class="btn btn-outline-danger w-100">🔑 구글로 로그인</a>
      </div>
    </div>

  <?php else: ?>
    <!-- ✅ 로그인된 경우 -->
    <div class="d-flex justify-content-between mb-4">
      <h2>💸 투자 시뮬레이터</h2>
      <div>
        <span class="me-3"><i class="fas fa-user-check"></i> <?= htmlspecialchars($_SESSION['user_id']) ?></span>
        <a href="logout.php" class="btn btn-outline-secondary">로그아웃</a>
      </div>
    </div>

    <!-- 여기부터 투자금 입력, 시뮬레이션 탭 구성 시작 -->
    <!-- 시뮬레이션 입력 폼 -->
    <form id="simulationForm" class="card p-4 shadow-sm mb-4">
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label"><i class="fas fa-pencil-alt"></i> 투자금액 (₩)</label>
          <input type="number" name="investment" class="form-control" value="30000000" required>
        </div>
        <div class="col-md-6">
          <label class="form-label"><i class="fas fa-star-of-life"></i> 목표 수익률 (%)</label>
          <input type="number" name="target_return" class="form-control" step="0.1" value="30" required>
        </div>
      </div>

      <h5 class="mt-3"><i class="fas fa-cog"></i> 포트폴리오 비율 (%)</h5>
      <div class="row mb-3">
        <div class="col-md-4">
          <label><i class="fab fa-btc"></i> BTC</label>
          <input type="number" name="btc" class="form-control" value="50" required>
        </div>
        <div class="col-md-4">
          <label><i class="fab fa-ethereum"></i> ETH</label>
          <input type="number" name="eth" class="form-control" value="20" required>
        </div>
        <div class="col-md-4">
          <label><i class="fab fa-watchman-monitoring"></i> ATC</label>
          <input type="number" name="alt" class="form-control" value="30" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary mt-3">시뮬레이션 시작</button>
    </form>

    <!-- 결과 출력 영역 -->
    <div id="simulationResults" class="mt-4">
      <!-- 시뮬레이션 결과 표시 영역 -->
    </div>
  <?php endif; ?>
  <script>
    document.getElementById('simulationForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('simulate.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.text())
        .then(data => {
          document.getElementById('simulationResults').innerHTML = data;
        })
        .catch(err => {
          alert('시뮬레이션 처리 중 오류 발생!');
          console.error(err);
        });
    });
  </script>
</body>

</html>