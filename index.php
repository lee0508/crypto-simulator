<!-- index.php -->
<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <title>비트코인 투자 시뮬레이터</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Noto Sans KR', sans-serif;
      padding: 30px;
      background: #f8f9fa;
    }

    h2 {
      color: #333;
    }

    input,
    button {
      padding: 8px;
      margin: 5px 0;
      width: 100%;
      max-width: 300px;
    }

    .result {
      margin-top: 20px;
      font-size: 16px;
    }

    canvas {
      max-width: 500px;
    }
  </style>
</head>

<body>
  <h2>비트코인 투자 시뮬레이터</h2>
  <label>투자 금액 (원):</label>
  <input type="number" id="investAmount" value="74000000"><br>

  <label>비트코인 구매 당시 시세 (원):</label>
  <input type="number" id="buyPrice" value="80000000"><br>

  <label>현재 시세 (원):</label>
  <input type="number" id="currentPrice" value="114000000"><br>

  <label>목표가1 (예: 120000000):</label>
  <input type="number" id="target1" value="120000000"><br>

  <label>목표가2 (예: 150000000):</label>
  <input type="number" id="target2" value="150000000"><br>

  <label>목표가3 (예: 200000000):</label>
  <input type="number" id="target3" value="200000000"><br>

  <button onclick="simulate()">시뮬레이션 실행</button>

  <div class="result" id="output"></div>
  <canvas id="chart" height="100"></canvas>

  <script>
    function simulate() {
      const invest = parseFloat(document.getElementById('investAmount').value);
      const buyPrice = parseFloat(document.getElementById('buyPrice').value);
      const current = parseFloat(document.getElementById('currentPrice').value);
      const targets = [
        parseFloat(document.getElementById('target1').value),
        parseFloat(document.getElementById('target2').value),
        parseFloat(document.getElementById('target3').value)
      ];

      const btcAmount = invest / buyPrice;
      const sellRatio = [0.3, 0.3, 0.4];
      let result = '';
      let total = 0;
      let labels = [];
      let data = [];

      targets.forEach((price, idx) => {
        const sellAmount = btcAmount * sellRatio[idx];
        const profit = sellAmount * price;
        total += profit;
        result += `목표가 ${idx+1}: ${price.toLocaleString()}원에 ${sellRatio[idx]*100}% 매도 → 수익: ${Math.floor(profit).toLocaleString()}원<br>`;
        labels.push(`목표 ${idx+1}`);
        data.push(Math.floor(profit));
      });

      result += `<br><strong>총 수익: ${Math.floor(total).toLocaleString()} 원</strong><br>`;
      result += `수익률: <strong>${((total - invest) / invest * 100).toFixed(2)}%</strong>`;

      document.getElementById('output').innerHTML = result;

      // Chart
      const ctx = document.getElementById('chart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: '수익(원)',
            data: data,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc']
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: val => val.toLocaleString() + ' 원'
              }
            }
          }
        }
      });
    }
  </script>
</body>

</html>