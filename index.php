<?php
require_once 'config/data.php';
session_start();
if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])) {
    header("Location: auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">


<head>
    <!-- link FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Press+Start+2P&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP ICON -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- BOOTSTRAP SYTLES -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEY-GAMES | Digital Marketplace</title>
    <style>
        .bigfront {
            font-size: 2rem;
            font-weight: bold;
        }

        @media (min-width: 768px) {
            .bigfront {
                font-size: 3rem;
            }
        }

        @media (min-width: 1200px) {
            .bigfront {
                font-size: 4rem;
            }
        }

        .imggift .col p {
            pointer-events: none;
        }

        .imggift .col:hover p {
            color: red;
            font-weight: bold;
        }

        .imggift .col:hover,
        .member .col:hover {
            transform: scale(1.1);
            transition: all 0.3s ease-in-out;
            opacity: 0.6;
        }

        nav a:hover {
            transform: scale(1.05);
            transition: all 0.3s ease-in-out;
        }

        .bg-black-rgb {
            background: #0f0c29;
            /* fallback for old browsers */
            background: -webkit-linear-gradient(to right, #24243e, #302b63, #0f0c29);
            /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to right, #24243e, #302b63, #0f0c29);
            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        }

        .rounded-edit {
            border-radius: 25px;
        }

        .back {
            background: #000000;
            /* fallback for old browsers */
            background: -webkit-linear-gradient(to right, #434343, #000000);
            /* Chrome 10-25, Safari 5.1-6 */
            background: linear-gradient(to right, #434343, #000000);
            /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        }

        .font-2P {
            font-family: "Press Start 2P", system-ui;
            font-weight: 400;
            font-style: normal;
        }

        .font-thai {
            font-family: "Kanit", sans-serif;
            font-weight: 700;
            font-style: normal;
        }

        .welcome-title {
            font-size: 2rem;
        }

        @media (min-width: 768px) {
            .welcome-title {
                font-size: 4rem;
            }
        }

        @media (min-width: 1200px) {
            .welcome-title {
                font-size: 5rem;
            }
        }

        .game-card {
            margin: 0 0 1rem 0;
        }

        @media (min-width: 576px) {
            .game-card {
                margin: 0 0.5rem 1rem 0.5rem;
            }
        }

        @media (min-width: 768px) {
            .game-card {
                margin: 0 1rem 1rem 1rem;
            }
        }

        :root { --navy: #0f172a; --slate: #1e293b; --cyan: #06b6d4; --ice: #38bdf8; --paper: #f8fafc; --muted: #94a3b8; }
        * { box-sizing: border-box; }
        body { color: var(--paper) !important; background: #07111f; font-family: 'Kanit', sans-serif; }
        .bg-black-rgb { background: radial-gradient(circle at 80% 8%, rgba(6,182,212,.16), transparent 28%), #07111f; }
        .back { z-index: 10; margin: 0 !important; padding: 18px max(24px, 5vw) !important; border-bottom: 1px solid rgba(148,163,184,.18); background: rgba(7,17,31,.86); backdrop-filter: blur(18px); }
        .back .fs-4 { color: var(--paper); font: 700 1.25rem 'Space Grotesk', sans-serif; letter-spacing: .02em; }
        .back .fs-4::before { content: 'KEY-'; color: var(--cyan); }
        .back .fs-4 { font-size: 0 !important; }
        .back .fs-4::after { content: 'GAMES'; color: var(--paper); font-size: 1.25rem; }
        .back .text-warning { color: #cbd5e1 !important; font-size: 1rem !important; transition: color .2s ease; }
        .back .text-warning:hover { color: var(--ice) !important; }
        .welcome-title { margin: 40px auto 16px; color: var(--paper); font: 700 clamp(2rem, 5vw, 4.5rem)/1 'Space Grotesk', sans-serif; letter-spacing: 0; }
        .welcome-title::first-line { color: var(--ice); }
        .container-xl { max-width: 1280px; }
        .bigfront { color: var(--paper); font: 700 clamp(1.7rem, 3vw, 2.5rem) 'Space Grotesk', sans-serif; letter-spacing: 0; }
        .navbar { padding: 0; }
        .navbar form { width: min(460px, 100%); }
        .navbar .form-control { border: 1px solid #334155; border-radius: 10px; color: var(--paper); background: rgba(30,41,59,.8); }
        .navbar .form-control::placeholder { color: var(--muted); }
        .navbar .btn-outline-success { border-color: var(--cyan); color: var(--cyan); border-radius: 10px; }
        .navbar .btn-outline-success:hover { background: var(--cyan); color: #06202b; }
        .game-card { overflow: hidden; padding: 10px !important; border: 1px solid rgba(148,163,184,.18); border-radius: 14px !important; background: rgba(15,23,42,.82) !important; box-shadow: 0 14px 34px rgba(0,0,0,.18); transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease; }
        .game-card:hover { transform: translateY(-7px); border-color: rgba(56,189,248,.65); box-shadow: 0 18px 38px rgba(6,182,212,.16); }
        .game-card img { border-radius: 9px !important; }
        .game-card h4, .game-card h5 { color: var(--paper) !important; }
        .game-card h4 { font-weight: 600; }
        .game-card h5 { color: var(--muted) !important; }
        .game-card h5:first-of-type { color: var(--ice) !important; font-size: 1.1rem !important; }
        .game-card hr { border-color: #334155; }
        .game-card .btn-success { width: 100%; border: 0; border-radius: 9px; background: var(--cyan); color: #06202b; font-weight: 700; }
        .game-card .btn-success:hover { background: var(--ice); }
        .game-card .btn-secondary { width: 100%; border-radius: 9px; }
        .pagination .page-link { color: var(--ice); border-color: #334155; background: var(--slate); }
        .pagination .active .page-link { border-color: var(--cyan); background: var(--cyan); color: #06202b; }
        .member .col, .imggift .col { padding: 10px; }
        .member img, .imggift img { border-radius: 14px; border: 1px solid rgba(148,163,184,.18); }
        footer { margin-top: 80px; border-top: 1px solid rgba(148,163,184,.18); color: var(--muted); background: #0b1424 !important; }
        .inbox-modal { overflow: hidden; color: var(--paper); border: 1px solid rgba(56,189,248,.35); border-radius: 16px; background: rgba(15,23,42,.96); box-shadow: 0 24px 70px rgba(0,0,0,.45); }
        .inbox-modal .modal-header { border-bottom-color: #334155; background: linear-gradient(135deg, rgba(6,182,212,.16), rgba(15,23,42,.4)); }
        .inbox-modal .modal-title { color: var(--paper); font: 700 1.25rem 'Space Grotesk', sans-serif; }
        .inbox-modal .modal-body { max-height: min(60vh, 520px); overflow-y: auto; }
        .inbox-modal .inbox-item { padding: 14px 0; margin: 0; color: #cbd5e1; border-bottom: 1px solid rgba(148,163,184,.18) !important; }
        .inbox-modal .inbox-item:last-child { border-bottom: 0 !important; }
        .inbox-modal .inbox-item strong { color: var(--ice); }
        .inbox-modal .inbox-item small { color: var(--muted); }
        .inbox-modal .inbox-empty { color: var(--muted); }
        .inbox-modal .modal-footer { border-top-color: #334155; }
        .inbox-modal .btn-close { filter: invert(1) grayscale(1); }
        .inbox-modal .btn-outline-warning { border-color: var(--cyan); color: var(--ice); }
        .inbox-modal .btn-outline-warning:hover { background: var(--cyan); color: #06202b; }
        @media (max-width: 767px) { .back .d-flex { gap: 12px; } .back .text-warning { font-size: .9rem !important; } }
    </style>
</head>

<body class="bg-black-rgb font-thai">
    <?php
    if (isset($_SESSION['admin_login'])) {
        $user_id = $_SESSION['admin_login'];
    } elseif (isset($_SESSION['user_login'])) {
        $user_id = $_SESSION['user_login'];
    }

    if (isset($user_id)) {
        $stmt = $conn->prepare("SELECT * FROM bob WHERE id = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    ?>
    <div class="container-fluid back p-3 mb-5 position-sticky top-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="fs-4 fw-bold mb-2 mb-md-0">
                ยินดีต้อนรับ, <?php echo htmlspecialchars($row['username'] ?? 'ผู้ใช้งาน'); ?>
            </div>
            <div class="d-flex flex-column flex-md-row gap-2 gap-md-4">
                <?php if (isset($_SESSION['admin_login'])) { ?>
                    <a href="admin/admin_page.php?page=dashboard" class="text-decoration-none text-warning"><i class="bi bi-speedometer2"></i> กลับหน้า Admin</a>
                <?php } ?>
                <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal-inbox">
                    กล่องจดหมาย<i class="bi bi-backpack"></i>
                </button>
                <a href="cart/cart_page.php" class="text-decoration-none fs-6 fs-md-4 fw-bold text-warning"><i class="bi bi-bag-fill"></i> ตะกร้าสินค้า </a>
                <a href="auth/logout.php" class="text-decoration-none fs-6 fs-md-4 fw-bold text-warning">ออกจากระบบ</a>
            </div>
        </div>
    </div>

    
    <!-- //modal -->
    <div class="modal fade" id="modal-inbox" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content inbox-modal">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">กล่องจดหมาย</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
        $stmt = $conn->prepare("
    SELECT ik.product_id, ik.game_key, ik.created_at,gt.name_games
    FROM inbox_keys ik
    JOIN games_table gt ON ik.product_id = gt.id_games
    WHERE ik.user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$inbox = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($inbox) {
            foreach ($inbox as $item) { ?>
            <p class="inbox-item fs-5">
            <i class="bi bi-controller me-1"></i> <strong>สินค้า:</strong> <?= htmlspecialchars($item['name_games']) ?><br>
            <i class="bi bi-key me-1"></i> <strong>Key:</strong> <?= htmlspecialchars($item['game_key']) ?><br>
            🕒 <small><?= $item['created_at'] ?></small>
            
        </p>
        <?php }?>
        <?php }else {
            echo "<p class=\"inbox-empty text-center fs-5 py-4\">ไม่มีข้อความ</p>";
        }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-warning" data-bs-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

    <h1 class="text-center p-3 font-2P welcome-title">WELCOME TO JARUS STORE</h1>
    <?php
    if (isset($_SESSION['success']) && $_SESSION['success'] == 'สั่งซื้อสำเร็จ!') {
    ?>
        <div class="alert alert-success text-center">
            <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php } ?>

    <!-- สไลด์รูปภาพ -->
    <div class="container m-auto">
        <?php
        include 'includes/slide.php';
        ?>
    </div>

    <!-- เกมทั้งหมด -->
    <div class="container-xl mt-5 mb-5 mx-auto p-5">
        <h1 class="bigfront mb-3">สินค้าที่นิยม</h1>

        <!-- ช่องค้นหา -->
        <nav class="navbar navbar-light mb-3">
            <div class="container-fluid">
                <a class="navbar-brand"></a>
                <form class="d-flex" action="index.php" method="get">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search" aria-label="Search" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
                    <button class="btn btn-outline-success" type="submit">ค้นหา</button>
                </form>
            </div>
        </nav>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 justify-content-center">
            <?php
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';

            $limit = 9; // แสดง 10 รายการต่อหน้า
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $page = max($page, 1);
            $offset = ($page - 1) * $limit;

            $where = '';
            $params = [];

            if ($search !== '') {
                $where = "WHERE name_games LIKE :search";
                $params[':search'] = "%$search%";
            }

            // ดึงข้อมูลตามหน้า
            $sql = "SELECT 
    g.id_games,
    g.name_games,
    g.price_games,
    g.img_games,
    COUNT(gk.key_id) AS key_available
FROM games_table g
LEFT JOIN game_keys gk 
    ON g.id_games = gk.product_id 
    AND gk.status = 'available'
$where
GROUP BY g.id_games
LIMIT :limit OFFSET :offset
";


            $stmt = $conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($games)) {
                foreach ($games as $game) {
                    $max = max(1, (int)$game['key_available']);
            ?>
                    <div class="col game-card bg-black-rgb rounded-edit">
                        <img width="100%" src="upload/<?= $game['img_games'] ?>" alt="<?= $game['name_games'] ?>" class="rounded-edit mt-2 img-fluid" style="height: 200px; object-fit: cover;" />
                        <h4 class="text-center mt-3 text-warning fs-5 fs-md-4"><?= $game['name_games'] ?></h4>
                        <hr class="bg-white">
                        <h5 class="text-center mb-3 text-warning fs-6 fs-md-5">ราคา :<?= $game['price_games'] ?>บาท</h5>
                        <h5 class="text-center mb-3 text-warning fs-6 fs-md-5">จำนวนคีย์ที่มี : <?= $game['key_available'] ?></h5>

                        <!-- ฟอร์มเพิ่มลงตะกร้า -->
                        <form action="cart/add_to_cart.php" method="post" class="text-center mb-2">
                            <input type="hidden" name="product_id" value="<?= $game['id_games'] ?>">
                            <input type="hidden" name="price" value="<?= $game['price_games'] ?>">
                            <?php if ($game['key_available'] > 0) { ?>
                                <?php ?>
                                <button type="submit" class="btn btn-success btn-sm">
                                    เพิ่มลงในรถเข็น
                                </button>
                            <?php } else { ?>
                                <button type="button" class="btn btn-secondary btn-sm" disabled>
                                    ไม่มีคีย์ให้บริการ
                                </button>
                            <?php } ?>
                        </form>
                    </div>

                <?php } ?>
            <?php } else {
                echo "<p class=\"text-center w-100 fs-4 m-5\">ไม่พบข้อมูลที่ค้นหา</p>";
            } ?>

            <?php
            $countSql = "SELECT COUNT(*) FROM games_table $where";
            $countStmt = $conn->prepare($countSql);
            if ($search !== '') {
                $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }
            $countStmt->execute();
            $totalGames = $countStmt->fetchColumn();
            $totalPages = ceil($totalGames / $limit);


            ?>
        </div>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link"
                            href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>

    </div>


    <!-- สิทธ์สมาชิก -->
    <div class="container text-center">
        <h1 class="text-start bigfront">สิทธิ์สมาชิก</h1>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 member">
            <div class="col">
                <img width="100%" src="https://a.storyblok.com/f/77562/960x920/df31ecb323/a-storyblok-2.png/m/" alt="MEMBER ICON" class="img-fluid" />

            </div>
            <div class="col">
                <img width="100%" src="https://a.storyblok.com/f/210486/960x920/924849b4d6/nintendo-switch.png/m/" alt="MEMBER ICON" class="img-fluid" />
            </div>
            <div class="col">
                <img width="100%" src="https://a.storyblok.com/f/77562/960x920/1187706966/a-storyblok-1.png/m/" alt="MEMBER ICON" class="img-fluid" />
            </div>
        </div>
    </div>


    <!-- บัตรของขวัญ -->
    <div class="container mt-5 mb-4 text-center">
        <h1 class="text-start bigfront">บัตรของขวัญ</h1>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 imggift font-2P fs-4">
            <div class="col position-relative"><img src="https://a.storyblok.com/f/210486/315x188/7ca7af17b2/playstation-gift-cards.png/m/" alt="GIFT ICON" class="img-fluid" />
                <p class="position-absolute end-0 start-0 top-40 bottom-50">PLAYSTATION</p>
            </div>
            <div class="col position-relative"><img src="https://a.storyblok.com/f/210486/315x188/7ca7af17b2/playstation-gift-cards.png/m/" alt="GIFT ICON" class="img-fluid" />
                <p class="position-absolute end-0 start-0 top-40 bottom-50">XBOX</p>
            </div>
            <div class="col position-relative"><img src="https://a.storyblok.com/f/210486/315x188/7ca7af17b2/playstation-gift-cards.png/m/" alt="GIFT ICON" class="img-fluid" />
                <p class="position-absolute end-0 start-0 top-40 bottom-50">STEAM</p>
            </div>
            <div class="col position-relative"><img src="https://a.storyblok.com/f/210486/315x188/7ca7af17b2/playstation-gift-cards.png/m/" alt="GIFT ICON" class="img-fluid" />
                <p class="position-absolute end-0 start-0 top-40 bottom-50">NINTENDO</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
<footer class="container-fluid p-1 bg-dark text-center">
    <h5 class="p-1">Made By.Jarus Saenubon </h5>
</footer>

</html>