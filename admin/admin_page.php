<?php
require_once '../config/data.php';
session_start();
if (!isset($_SESSION['admin_login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$stmt = $conn->prepare("SELECT username FROM bob WHERE id = ?");
$stmt->execute([$_SESSION['admin_login']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEY-GAMES | Admin</title>
    <style>
        :root { --slate: #1e293b; --cyan: #06b6d4; --ice: #38bdf8; --paper: #f8fafc; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; color: var(--paper); font-family: 'Kanit', sans-serif; background: radial-gradient(circle at 80% 0%, rgba(6,182,212,.12), transparent 28%), #07111f; }
        .admin-layout { min-height: 100vh; }
        .admin-sidebar { width: 260px; min-height: 100vh; padding: 28px 18px; flex-shrink: 0; border-right: 1px solid rgba(148,163,184,.16); background: rgba(15,23,42,.9); }
        .admin-brand { color: var(--paper); font: 700 1.15rem 'Space Grotesk', sans-serif; letter-spacing: .06em; }
        .admin-brand:hover { color: var(--paper); }
        .admin-brand span { color: var(--cyan); }
        .admin-sidebar hr { border-color: #334155; }
        .admin-sidebar .nav-link { margin: 5px 0; padding: 12px 14px; border-radius: 9px; color: #cbd5e1; transition: .2s ease; }
        .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active { color: #06202b; background: var(--cyan); }
        .admin-sidebar .dropdown-toggle { width: 100%; color: #cbd5e1; }
        .admin-sidebar .dropdown-toggle:hover { color: var(--ice); }
        .admin-content { min-width: 0; padding: 28px; }
        .admin-sidebar .dropdown-menu { background: var(--slate); border-color: #334155; }
        .admin-sidebar .dropdown-item { color: #cbd5e1; }
        .admin-sidebar .dropdown-item:hover { color: var(--paper); background: #334155; }
        .admin-content .table { color: #f8fafc; border-color: #334155; }
        .admin-content .table thead { color: var(--ice); background: #1e293b; }
        .admin-content .table td, .admin-content .table th { border-color: #334155; vertical-align: middle; }
        .admin-content .table tbody tr:hover { background: rgba(6,182,212,.06); }
        .admin-content .form-control, .admin-content .form-select { color: #f8fafc; border-color: #334155; background: #1e293b; }
        .admin-content .modal-content { color: #f8fafc; border-color: #334155; background: #0f172a; }
        .admin-content .btn-success { border: 0; background: var(--cyan); color: #06202b; }
        @media (max-width: 760px) { .admin-layout { display: block !important; } .admin-sidebar { width: 100%; min-height: auto; } .admin-sidebar .nav { display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px; } .admin-content { padding: 18px; } }
    </style>
</head>
<body>
    <main>
        <div class="admin-layout d-flex">
            <nav class="admin-sidebar">
                <div class="d-flex flex-column h-100">
                    <a href="admin_page.php?page=dashboard" class="admin-brand d-flex align-items-center mb-3 text-decoration-none">
                        <img src="../loadpicture/KING.png" alt="" width="40" height="40" class="me-2">
                        <span><span>KEY-</span>GAMES / ADMIN</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="admin_page.php?page=dashboard" class="nav-link <?= ($_GET['page'] ?? 'dashboard') == 'dashboard' ? 'active' : '' ?>">
                                <i class="bi bi-grid-1x2-fill me-2"></i>DASHBOARD
                            </a>

                        </li>
                        <li>
                            <a href="admin_page.php?page=products" class="nav-link <?= ($_GET['page'] ?? '') == 'products' ? 'active' : '' ?>">
                                <i class="bi bi-controller me-2"></i>PRODUCT
                            </a>
                        </li>
                        <li>
                            <a href="admin_page.php?page=user" class="nav-link <?= ($_GET['page'] ?? '') == 'user' ? 'active' : '' ?>">
                                <i class="bi bi-people me-2"></i>USER
                            </a>
                        </li>
                        <li>
                            <a href="admin_page.php?page=key" class="nav-link <?= ($_GET['page'] ?? '') == 'key' ? 'active' : '' ?>">
                                <i class="bi bi-key me-2"></i>KEYS
                            </a>
                        </li>
                        <li>
                            <a href="admin_page.php?page=orders" class="nav-link <?= ($_GET['page'] ?? '') == 'orders' ? 'active' : '' ?>">
                                <i class="bi bi-receipt me-2"></i>ORDERS
                            </a>
                        </li>
                        <li>
                            <a href="../index.php" class="nav-link">
                                <i class="bi bi-house-door me-2"></i>HOMEPAGE
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <button
                            type="button"
                            class="btn d-flex align-items-center dropdown-toggle"
                            id="dropdownUser2"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <img src="../loadpicture/KING.png" width="32" height="32" class="rounded-circle me-2">
                            <strong><?= $row['username'] ?></strong>
                        </button>

                        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                            <li><a class="dropdown-item" href="#">New project...</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../auth/logout.php">Sign out</a></li>
                        </ul>
                    </div>


                </div>
            </nav>


            <div class="admin-content flex-grow-1">
                <?php
                $page = $_GET['page'] ?? 'dashboard';

                switch ($page) {
                    case 'user':
                        include '../includes/userpage.php';
                        break;
                    case 'products':
                        include 'product.php';
                        break;
                    case 'key':
                        include 'key_games.php';
                        break;
                    case 'orders':
                        include 'orders_admin.php';
                        break;
                    default:
                        include 'dashbord.php';
                        break;
                }
                ?>


            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>