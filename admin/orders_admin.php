<?php
require_once '../config/data.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORDERS</title>
    <style>
        .orders-page { color: #f8fafc; background: #07111f; }
        .orders-page h1 { font: 700 clamp(1.6rem, 4vw, 2.2rem) 'Space Grotesk', sans-serif; }
        .orders-table-wrap { max-height: 1200px; overflow: auto; border: 1px solid rgba(148,163,184,.18); border-radius: 14px; background: rgba(15,23,42,.82); }
        .orders-table { min-width: 900px; margin: 0; color: #f8fafc; }
        .orders-table thead { position: sticky; top: 0; z-index: 2; color: #38bdf8; background: #1e293b; }
        .orders-table th, .orders-table td { padding: 14px; border-color: #334155; vertical-align: middle; }
        .orders-table tbody tr:hover { background: rgba(6,182,212,.06); }
        @media (max-width: 575.98px) { .orders-page .container { margin-top: 1rem !important; } }
    </style>
</head>

<body class="orders-page">

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h1>ORDERS</h1>
            </div>
        </div>
        <hr>
        <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php } ?>
        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php } ?>

        <!-- แสดงรายการสินค้า -->
        <div class="orders-table-wrap">
        <table class="table orders-table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">ID user</th>
                    <th scope="col">Username</th>
                    <th scope="col">Total Price</th>
                    <th scope="col">status</th>
                    <th scope="col">date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql="SELECT o.*, u.username FROM orders o LEFT JOIN bob u ON o.user_id = u.id ORDER BY o.order_id ASC";
                $stmt = $conn->query($sql);
                $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$orders) {
                    echo "<tr><td colspan='7' class='text-center py-4'>ไม่มีออเดอร์ในระบบ</td></tr>";
                } else {
                    foreach ($orders as $order) {
                ?>
                        <tr>
                            <th scope="row"><?= (int)$order['order_id'] ?></th>
                            <td><?= (int)$order['user_id'] ?></td>
                            <td><?= htmlspecialchars($order['username'] ?? '-') ?></td>
                            <td><?= number_format((float)$order['total_price'], 2) ?></td>
                            <td><?= htmlspecialchars($order['order_status']) ?></td>
                            <td><?= htmlspecialchars($order['order_date']) ?></td>
                            <td>
                                <a href="order_delete.php?delete_order=<?= $order["order_id"] ?>" class="btn btn-danger" onclick="return confirm('คุณต้องการลบออเดอร์หรือไม่ ??')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                <?php   }
                }
                ?>
            </tbody>
        </table>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>