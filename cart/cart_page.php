<?php
session_start();
require_once '../config/data.php';

if (isset($_SESSION['user_login'])) {
    $user_id = $_SESSION['user_login'];
} else {
    header("Location: ../auth/login.php");
    exit;
}

// อัพเดทจำนวนสินค้า
if (isset($_POST['update_qty'])) {
    $product_id = $_POST['product_id'];
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        $stmt = $conn->prepare("
            UPDATE cart_item 
            SET quantity = ? 
            WHERE cart_id = (
                SELECT cart_id FROM cart WHERE user_id = ?
            ) AND product_id = ?
        ");
        $stmt->execute([$quantity, $user_id, $product_id]);
    }
}

// ลบสินค้า
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM cart_item 
        WHERE cart_id = (
            SELECT cart_id FROM cart WHERE user_id = ?
        ) AND product_id = ?
    ");
    $stmt->execute([$user_id, $product_id]);
}
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า | KEY-GAMES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root { --navy: #0f172a; --slate: #1e293b; --cyan: #06b6d4; --ice: #38bdf8; --paper: #f8fafc; --muted: #94a3b8; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; color: var(--paper); font-family: 'Kanit', sans-serif; background: radial-gradient(circle at 80% 0%, rgba(6,182,212,.14), transparent 30%), #07111f; }
        .cart-header { border: 1px solid rgba(56,189,248,.24); border-radius: 18px; background: rgba(15,23,42,.8) !important; box-shadow: 0 18px 50px rgba(0,0,0,.25); }
        .cart-header h1 { font: 700 clamp(2rem, 4vw, 3.5rem) 'Space Grotesk', sans-serif; }
        .cart-header h1 span { color: var(--ice); }
        .cart-header a { color: var(--muted) !important; }
        .cart-header a:hover { color: var(--ice) !important; }
        .cart-table-wrap { overflow-x: auto; border: 1px solid rgba(148,163,184,.18); border-radius: 14px; background: rgba(15,23,42,.82); }
        .cart-table { min-width: 700px; margin: 0; color: var(--paper); }
        .cart-table thead { color: var(--ice); background: rgba(30,41,59,.8); }
        .cart-table th { padding: 18px 14px; border-color: #334155; font-weight: 500; white-space: nowrap; }
        .cart-table td { padding: 16px 14px; border-color: #263449; vertical-align: middle; }
        .cart-table tbody tr:hover { background: rgba(6,182,212,.06); }
        .cart-table .form-control { width: 85px; border: 1px solid #334155; color: var(--paper); background: var(--slate); }
        .btn-update { border: 1px solid rgba(56,189,248,.55); color: var(--ice); }
        .btn-update:hover { background: var(--ice); color: #06202b; }
        .checkout-btn { border: 0; border-radius: 10px; background: var(--cyan); color: #06202b; font-weight: 700; }
        .checkout-btn:hover { background: var(--ice); color: #06202b; }
        .empty-cart { color: var(--muted); padding: 48px !important; }
        @media (max-width: 576px) { .cart-header { margin-top: 18px !important; } .cart-table-wrap { margin: 0 -4px; } }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
</head>

<body>

    <header class="container cart-header text-center p-4 mt-5">
        <div class="text-uppercase text-info small fw-bold" style="letter-spacing: .16em;">KEY-GAMES / YOUR PICKS</div>
        <h1 class="mt-2 mb-2">Shopping <span>Cart</span></h1>
        <a href="../index.php" class="text-dark text-decoration-none">กลับไปหน้าหลัก</a>
    </header>
        
        <div class="cart-table-wrap">
        <table class="table cart-table text-center">
        <?php if(isset($_SESSION['error'])): ?>
                    <td colspan='6' class='text-center empty-cart'>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                                    <button type="submit" name="update_qty" class="btn btn-sm btn-update">
        <?php endif; ?>
        </table>
        </div>
            <thead>
            <a href="orders.php?user_order=<?= $user_id ?>" class="btn checkout-btn text-center d-block mt-4 py-3">ดำเนินการชำระเงิน <i class="bi bi-arrow-right"></i></a>
                    <th>แก้ไขจำนวน</th>
                    <th>สินค้า</th>
                    <th>จำนวน</th>
                    <th>ราคา</th>
                    <th>ราคารวม</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $stmt = $conn->prepare("
SELECT 
    c.product_id,
    c.quantity,
    c.price,
    g.name_games,
    COUNT(gk.key_id) AS key_available
FROM cart_item c
JOIN cart ct ON c.cart_id = ct.cart_id
JOIN games_table g ON c.product_id = g.id_games
LEFT JOIN game_keys gk 
    ON g.id_games = gk.product_id 
    AND gk.status = 'available'
WHERE ct.user_id = ?
GROUP BY c.product_id, c.quantity, c.price, g.name_games
");
                $stmt->execute([$user_id]);

                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$cart_items) {
                    echo "
                <tr>
                    <td colspan='6' class='text-center text-danger'>
                        ไม่มีสินค้าในตะกร้า
                    </td>
                </tr>
            ";
                } else {
                    foreach ($cart_items as $item) {
                        $total_price = $item['price'] * $item['quantity'];
                        $max = max(1, (int)$item['key_available']);
                ?>
                        <tr>
                            <!-- ฟอร์มแก้ไขจำนวนสินค้า -->
                            <td>
                                <form method="post" action="cart_page.php" class="d-inline-block">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity']; ?>" min="1" max="<?= $max ?>" class="form-control mb-1">
                                    <button type="submit" name="update_qty" class="btn btn-sm btn-warning">
                                        แก้จำนวน
                                    </button>
                                </form>
                            </td>

                            <!-- แสดงข้อมูลสินค้า -->
                            <td><?= $item['name_games']; ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td><?= number_format($item['price'], 2); ?></td>
                            <td><?= number_format($total_price, 2); ?></td>

                            <!-- ลบสินค้า -->
                            <td>
                                <a href="?delete=<?= $item['product_id']; ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('ต้องการลบสินค้านี้หรือไม่?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>

                <?php } ?>
            </tbody>
        </table>
        <?php if (!empty($cart_items)) { ?>
            <a href="orders.php?user_order=<?= $user_id ?>" class="btn btn-success text-center d-block mt-3">ชำระเงิน</a>
        <?php } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>