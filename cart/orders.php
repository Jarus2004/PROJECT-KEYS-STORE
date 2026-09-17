<?php
require_once '../config/data.php';
session_start();

if (!isset($_SESSION['user_login'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = (int) $_SESSION['user_login'];
$api_key = getenv('THUNDER_API_KEY');
$promptpay_msisdn = getenv('THUNDER_PROMPTPAY_MSISDN');
$error = null;
$qr = null;

function thunderRequest(string $url, array $headers, array|string $body): array
{
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_TIMEOUT => 30,
    ]);
    $response = curl_exec($curl);
    $curl_error = curl_error($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($response === false || $curl_error) {
        throw new Exception('ไม่สามารถเชื่อมต่อบริการชำระเงินได้');
    }

    $result = json_decode($response, true);
    if (!is_array($result) || $http_code < 200 || $http_code >= 300 || (($result['success'] ?? true) === false) || (int) ($result['status'] ?? 200) !== 200) {
        throw new Exception($result['error']['message'] ?? $result['message'] ?? 'บริการชำระเงินตอบกลับผิดพลาด');
    }

    return $result['data'] ?? [];
}

$stmt = $conn->prepare(
    'SELECT ci.product_id, ci.quantity, ci.price, g.name_games '
    . 'FROM cart_item ci '
    . 'JOIN cart c ON ci.cart_id = c.cart_id '
    . 'JOIN games_table g ON ci.product_id = g.id_games '
    . 'WHERE c.user_id = ?'
);
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = 0.0;
foreach ($items as $item) {
    $total += (float) $item['price'] * (int) $item['quantity'];
}

if (!$items) {
    header('Location: cart_page.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_slip'])) {
    if (!$api_key || !$promptpay_msisdn) {
        $error = 'ระบบชำระเงินยังไม่ได้ตั้งค่า';
    } elseif (!isset($_FILES['slip']) || $_FILES['slip']['error'] !== UPLOAD_ERR_OK) {
        $error = 'กรุณาเลือกไฟล์สลิป';
    } else {
        $file = $_FILES['slip'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if ($file['size'] > 4 * 1024 * 1024 || !in_array($mime, $allowed_types, true)) {
            $error = 'รองรับเฉพาะไฟล์ JPG, PNG, GIF หรือ WEBP ขนาดไม่เกิน 4 MB';
        } else {
            try {
                $slip = thunderRequest(
                    'https://api.thunder.in.th/v2/verify/bank',
                    ['Authorization: Bearer ' . $api_key],
                    [
                        'image' => new CURLFile($file['tmp_name'], $mime, $file['name']),
                        'matchAccount' => 'true',
                        'matchAmount' => (string) round($total, 2),
                        'checkDuplicate' => 'true',
                    ]
                );

                if (($slip['isDuplicate'] ?? false) || !($slip['isAmountMatched'] ?? false) || empty($slip['matchedAccount'])) {
                    throw new Exception('สลิปซ้ำ ยอดเงิน หรือบัญชีผู้รับไม่ตรงกับคำสั่งซื้อ');
                }

                $conn->beginTransaction();
                $stmt = $conn->prepare('INSERT INTO orders (user_id, total_price, order_status) VALUES (?, ?, ?)');
                $stmt->execute([$user_id, $total, 'completed']);
                $order_id = (int) $conn->lastInsertId();

                foreach ($items as $item) {
                    $quantity = (int) $item['quantity'];
                    $stmt = $conn->prepare(
                        'SELECT key_id, game_key FROM game_keys '
                        . "WHERE product_id = ? AND status = 'available' "
                        . 'ORDER BY key_id LIMIT ' . $quantity . ' FOR UPDATE'
                    );
                    $stmt->execute([(int) $item['product_id']]);
                    $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($keys) < $quantity) {
                        throw new Exception('คีย์สินค้ามีไม่เพียงพอ');
                    }

                    $key_ids = array_column($keys, 'key_id');
                    $placeholders = implode(',', array_fill(0, count($key_ids), '?'));
                    $stmt = $conn->prepare("UPDATE game_keys SET status = 'used', order_id = ? WHERE key_id IN ($placeholders)");
                    $stmt->execute(array_merge([$order_id], $key_ids));

                    $stmt = $conn->prepare('INSERT INTO inbox_keys (order_id, user_id, product_id, key_id, game_key) VALUES (?, ?, ?, ?, ?)');
                    foreach ($keys as $key) {
                        $stmt->execute([$order_id, $user_id, $item['product_id'], $key['key_id'], $key['game_key']]);
                    }
                }

                $stmt = $conn->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
                foreach ($items as $item) {
                    $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                }

                $stmt = $conn->prepare('DELETE ci FROM cart_item ci JOIN cart c ON ci.cart_id = c.cart_id WHERE c.user_id = ?');
                $stmt->execute([$user_id]);
                $conn->commit();

                $_SESSION['success'] = 'ชำระเงินและตรวจสอบสลิปสำเร็จ!';
                header('Location: ../index.php');
                exit;
            } catch (Exception $exception) {
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                error_log('Payment verification error: ' . $exception->getMessage());
                $error = $exception->getMessage();
            }
        }
    }
} elseif ($api_key && $promptpay_msisdn) {
    try {
        $qr = thunderRequest(
            'https://api.thunder.in.th/v1/qr/generate',
            ['Authorization: Bearer ' . $api_key, 'Content-Type: application/json'],
            json_encode(['type' => 'PROMPTPAY', 'msisdn' => $promptpay_msisdn, 'amount' => round($total, 2)])
        );
    } catch (Exception $exception) {
        $error = $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <title>ชำระเงิน | KEY-GAMES</title>
    <style>
        :root { --cyan: #06b6d4; --ice: #38bdf8; --paper: #f8fafc; --muted: #94a3b8; --slate: #1e293b; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; color: var(--paper); font-family: 'Kanit', sans-serif; background: radial-gradient(circle at 15% 0%, rgba(56,189,248,.13), transparent 28%), radial-gradient(circle at 90% 80%, rgba(6,182,212,.1), transparent 30%), #07111f; }
        .checkout-shell { max-width: 820px; }
        .checkout-header { padding: 30px 24px; border: 1px solid rgba(56,189,248,.24); border-radius: 18px; background: rgba(15,23,42,.8); box-shadow: 0 18px 50px rgba(0,0,0,.25); }
        .eyebrow { color: var(--cyan); font: 600 .78rem 'Space Grotesk', sans-serif; letter-spacing: .16em; }
        .checkout-header h1 { font: 700 clamp(2rem, 5vw, 3.5rem)/1.1 'Space Grotesk', sans-serif; }
        .checkout-header h1 span { color: var(--ice); }
        .checkout-header p { color: var(--muted); }
        .payment-panel { border: 1px solid rgba(148,163,184,.18); border-radius: 16px; background: rgba(15,23,42,.86); box-shadow: 0 18px 45px rgba(0,0,0,.2); }
        .payment-panel h2 { font: 600 1.35rem 'Space Grotesk', sans-serif; }
        .amount { color: var(--ice); font: 700 clamp(1.8rem, 5vw, 2.6rem) 'Space Grotesk', sans-serif; }
        .qr-frame { display: inline-flex; padding: 14px; border: 1px solid rgba(56,189,248,.45); border-radius: 14px; background: #fff; box-shadow: 0 12px 35px rgba(6,182,212,.12); }
        .qr-frame img { display: block; width: min(320px, 68vw); }
        .payment-note { color: var(--muted); }
        .upload-box { margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(148,163,184,.18); }
        .form-label { color: #cbd5e1; }
        .form-control { border: 1px solid #334155; color: var(--paper); background: var(--slate); }
        .form-control:focus { border-color: var(--cyan); color: var(--paper); background: var(--slate); box-shadow: 0 0 0 .2rem rgba(6,182,212,.16); }
        .form-control::file-selector-button { color: var(--paper); background: #334155; }
        .submit-btn { border: 0; border-radius: 10px; background: var(--cyan); color: #06202b; font-weight: 700; }
        .submit-btn:hover { background: var(--ice); color: #06202b; }
        .back-link { color: var(--muted); }
        .back-link:hover { color: var(--ice); }
        .alert { border-radius: 10px; }
        @media (max-width: 576px) { .checkout-header { margin-top: 18px !important; padding: 24px 18px; } .payment-panel { padding: 22px !important; } }
    </style>
</head>
<body>
    <main class="container checkout-shell py-4 py-md-5">
        <header class="checkout-header text-center mb-4">
            <div class="eyebrow text-uppercase">KEY-GAMES / CHECKOUT</div>
            <h1 class="mt-2 mb-2">ชำระเงินด้วย <span>PromptPay</span></h1>
            <p class="mb-0">สแกน QR แล้วส่งสลิปเพื่อรับคีย์เกมของคุณ</p>
        </header>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <div class="payment-panel p-4 p-md-5 text-center">
                <div class="text-uppercase small text-secondary mb-2">ยอดชำระทั้งหมด</div>
                <h2 class="amount mb-4"><?= number_format($total, 2) ?> บาท</h2>
                <?php if ($qr): ?>
                    <div class="qr-frame my-2">
                        <img src="data:<?= htmlspecialchars($qr['mime'] ?? 'image/png') ?>;base64,<?= htmlspecialchars($qr['image']) ?>" alt="PromptPay QR Code">
                    </div>
                    <p class="payment-note mt-3 mb-0">สแกน QR เพื่อชำระเงิน แล้วอัปโหลดสลิปด้านล่าง</p>
                    <form method="post" enctype="multipart/form-data" class="upload-box text-start">
                        <label for="slip" class="form-label"><i class="bi bi-cloud-arrow-up me-1"></i> รูปสลิปการโอนเงิน</label>
                        <input type="file" id="slip" name="slip" accept="image/jpeg,image/png,image/gif,image/webp" required class="form-control mb-3">
                        <button type="submit" name="verify_slip" value="1" class="btn submit-btn w-100 py-2"><i class="bi bi-shield-check me-1"></i> ส่งสลิปเพื่อตรวจสอบ</button>
                    </form>
                <?php elseif (!$api_key || !$promptpay_msisdn): ?>
                    <p class="text-danger mb-0">ระบบชำระเงินยังไม่ได้ตั้งค่า กรุณาติดต่อผู้ดูแลระบบ</p>
                <?php endif; ?>
                <a href="cart_page.php" class="back-link d-inline-block mt-4 text-decoration-none"><i class="bi bi-arrow-left me-1"></i> กลับไปที่ตะกร้าสินค้า</a>
        </div>
    </main>
</body>
</html>
