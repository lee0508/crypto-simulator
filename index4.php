<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Crypto Investment Simulator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- html2pdf.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <style>
    canvas {
      max-width: 100%;
    }

    .chart-container {
      display: none;
    }
  </style>
</head>

<body class="bg-light">
  <div class="container py-4">
    <h2 class="mb-4 text-center">🚀 Crypto Investment Simulator</h2>

    <!-- 투자금 입력 -->
    <div class="card mb-4">
      <div class="card-body">
        <form id="simulatorForm">
          <label class="form-label">💰 Investment Amount (KRW)</label>
          <input type="number" id="investment" class="form-control" value="47000000" required>
          <label class="form-label mt-3">🎯 Target Profit Rate (%)</label>
          <input type="number" id="targetProfit" class="form-control" value="30" required>
          <button type="submit" class="btn btn-primary mt-3 w-100">Run Simulation</button>
        </form>
      </div>
    </div>

    <!-- 결과 출력 영역 -->
    <div id="resultSection" style="display:none">
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title">📈 Simulation Results</h5>
          <p><strong>Investment:</strong> <span id="resInvestment"></span> 원</p>
          <p><strong>Target Profit:</strong> <span id="resTarget"></span>%</p>
          <p><strong>Expected Return:</strong> <span id="resProfit"></span> 원</p>
          <p><strong>Total Value:</strong> <span id="resTotal"></span> 원</p>
        </div>
      </div>

      <!-- 리밸런싱 결과 -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title">🔁 Rebalancing Guide</h5>
          <ul id="rebalanceList"></ul>
        </div>
      </div>

      <!-- 차트 탭 버튼 -->
      <div class="btn-group mb-3 w-100" role="group">
        <button class="btn btn-outline-primary" onclick="showChart('pie')">Pie Chart</button>
        <button class="btn btn-outline-secondary" onclick="showChart('bar')">Bar Chart</button>
        <button class="btn btn-outline-success" onclick="showChart('doughnut')">Doughnut Chart</button>
      </div>

      <!-- 차트들 -->
      <div class="chart-container" id="chartContainer">
        <canvas id="chartCanvas"></canvas>
      </div>

      <!-- PDF 저장 버튼 -->
      <div class="text-center">
        <button class="btn btn-danger mt-3" onclick="savePDF()">📄 Save as PDF</button>
      </div>
    </div>
  </div>

  <script>
    const portfolio = {
      "Bitcoin": 50,
      "Ethereum": 30,
      "Solana": 20
    };

    let chart;

    document.getElementById('simulatorForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const investment = parseFloat(document.getElementById('investment').value);
      const targetProfit = parseFloat(document.getElementById('targetProfit').value);

      const profit = Math.round(investment * (targetProfit / 100));
      const totalValue = investment + profit;

      document.getElementById('resInvestment').innerText = investment.toLocaleString();
      document.getElementById('resTarget').innerText = targetProfit;
      document.getElementById('resProfit').innerText = profit.toLocaleString();
      document.getElementById('resTotal').innerText = totalValue.toLocaleString();

      // 리밸런싱 계산
      const rebalanceHTML = [];
      for (const coin in portfolio) {
        const percent = portfolio[coin];
        const amount = Math.round(totalValue * (percent / 100));
        rebalanceHTML.push(`<li>${coin}: ${percent}% → ${amount.toLocaleString()} 원</li>`);
      }
      document.getElementById('rebalanceList').innerHTML = rebalanceHTML.join("");

      // 차트 초기화 및 Pie 차트 표시
      drawChart("pie", portfolio, totalValue);

      // 결과 섹션 표시
      document.getElementById('resultSection').style.display = 'block';
    });

    function drawChart(type, data, total) {
      const labels = Object.keys(data);
      const values = labels.map(coin => total * (data[coin] / 100));
      const colors = ['#f7931a', '#627eea', '#00ffa3'];

      if (chart) chart.destroy();

      chart = new Chart(document.getElementById('chartCanvas'), {
        type: type,
        data: {
          labels: labels,
          datasets: [{
            label: 'KRW',
            data: values,
            backgroundColor: colors
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });

      document.getElementById('chartContainer').style.display = 'block';
    }

    function showChart(type) {
      const investment = parseFloat(document.getElementById('investment').value);
      const targetProfit = parseFloat(document.getElementById('targetProfit').value);
      const totalValue = investment + Math.round(investment * (targetProfit / 100));
      drawChart(type, portfolio, totalValue);
    }

    function savePDF() {
      const element = document.getElementById('resultSection');
      html2pdf().set({
        margin: 1,
        filename: 'investment_result.pdf',
        html2canvas: {
          scale: 2
        },
        jsPDF: {
          unit: 'mm',
          format: 'a4',
          orientation: 'portrait'
        }
      }).from(element).save();
    }
  </script>
</body>

</html>