<?php
require_once '../config/data.php';



// จำนวนเกมทั้งหมด
$stmt = $conn->query("SELECT COUNT(*) FROM games_table");
$totalGames = $stmt->fetchColumn();

// จำนวนรหัสเกมที่ยังไม่ใช้
$stmt = $conn->query("
    SELECT COUNT(*) 
    FROM game_keys 
    WHERE status = 'available'
");
$totalKeys = $stmt->fetchColumn();

// จำนวนออเดอร์ทั้งหมด
$stmt = $conn->query("SELECT COUNT(*) FROM orders");
$totalOrders = $stmt->fetchColumn();

//รายได้รวม (เฉพาะที่จ่ายแล้ว)
$stmt = $conn->query("
    SELECT IFNULL(SUM(total_price),0) 
    FROM orders 
    WHERE order_status = 'completed'
");
$totalRevenue = $stmt->fetchColumn();

// รายได้รายเดือน (6 เดือนล่าสุด)
$stmt = $conn->prepare("
    SELECT 
        DATE_FORMAT(order_date, '%Y-%m') AS month,
        SUM(total_price) AS total
    FROM orders
    WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY month
");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$labels = [];
$totals = [];

for ($i = 5; $i >= 0; $i--) {

    $monthKey = date("Y-m", strtotime("-$i month"));

    $thaiMonths = [
        "01" => "ม.ค.",
        "02" => "ก.พ.",
        "03" => "มี.ค.",
        "04" => "เม.ย.",
        "05" => "พ.ค.",
        "06" => "มิ.ย.",
        "07" => "ก.ค.",
        "08" => "ส.ค.",
        "09" => "ก.ย.",
        "10" => "ต.ค.",
        "11" => "พ.ย.",
        "12" => "ธ.ค."
    ];

    $m = date("m", strtotime("-$i month"));
    $y = date("Y", strtotime("-$i month"));

    $labels[] = $thaiMonths[$m] . " " . $y;


    $totals[] = $result[$monthKey] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>KEY-GAMES | Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-page { color: #f8fafc; background: #07111f; }
        .dashboard-page h2 { font: 700 clamp(1.55rem, 3vw, 2rem) 'Space Grotesk', sans-serif; }
        .dashboard-content { width: 100%; max-width: 1480px; margin: 0 auto; }
        .stat-card, .chart-card { border: 1px solid rgba(148,163,184,.18); border-radius: 14px; background: rgba(15,23,42,.82); box-shadow: 0 14px 34px rgba(0,0,0,.16); }
        .stat-card { min-height: 150px; height: 100%; color: #f8fafc; }
        .stat-card h6 { color: #94a3b8; }
        .stat-card h2 { color: #38bdf8; font: 700 clamp(1.65rem, 3vw, 2rem) 'Space Grotesk', sans-serif; overflow-wrap: anywhere; }
        .stat-card a { color: #06b6d4 !important; }
        .chart-card h5 { color: #f8fafc; }
        .chart-wrap { position: relative; height: clamp(230px, 32vw, 380px); }
        @media (max-width: 575.98px) {
            .dashboard-content { padding: 0; }
            .dashboard-page h2 { margin-bottom: 1rem !important; }
            .dashboard-page .row.g-4 { --bs-gutter-y: 1rem; }
            .stat-card { min-height: 125px; }
            .stat-card .card-body { padding: 1rem; }
            .stat-card h6 { font-size: .95rem; }
            .chart-card { margin-top: 1.5rem !important; }
            .chart-card .card-body { padding: 1rem; }
            .chart-wrap { height: 250px; }
        }
    </style>
</head>

<body class="dashboard-page">

    <div class="container-fluid">
        <div class="row">

            <!-- ===== Content ===== -->
            <div class="col-12 p-0 dashboard-content">

                <h2 class="mb-4">Dashboard</h2>

                <!-- ===== Summary Cards ===== -->
                <div class="row g-4">

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6><i class="bi bi-controller"></i> เกมทั้งหมด</h6>
                                <h2><?= $totalGames ?></h2>
                                <a href="admin_page.php?page=products" class="text-decoration-none text-white">ดูรายละเอียดเพิ่มเติม</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6><i class="bi bi-key"></i> รหัสเกมคงเหลือ</h6>
                                <h2><?= $totalKeys ?></h2>
                                <a href="admin_page.php?page=key" class="text-decoration-none text-white">ดูรายละเอียดเพิ่มเติม</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6><i class="bi bi-cart"></i> ออเดอร์ทั้งหมด</h6>
                                <h2><?= $totalOrders ?></h2>
                                <a href="admin_page.php?page=orders" class="text-decoration-none text-white">ดูรายละเอียดเพิ่มเติม</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6><i class="bi bi-cash"></i> รายได้รวม</h6>
                                <h2><?= number_format($totalRevenue, 2) ?> ฿</h2>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ===== Chart ===== -->
                <div class="card chart-card mt-5">
                    <div class="card-body">
                        <h5 class="mb-3">รายได้รายเดือน</h5>
                        <div class="chart-wrap">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#cbd5e1' }
                    }
                },
                scales: {
                    x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148,163,184,.12)' } },
                    y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(148,163,184,.12)' }, beginAtZero: true }
                }
            },
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'รายได้ (บาท)',
                    data: <?= json_encode($totals) ?>,
                    backgroundColor: 'rgba(6, 182, 212, .7)',
                    borderColor: '#38bdf8',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            }
        });
    </script>

</body>

</html>