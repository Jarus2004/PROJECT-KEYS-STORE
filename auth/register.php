<?php
require_once '../config/data.php';
?>
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก | KEY-GAMES</title>
    <style>
        :root { --navy: #0f172a; --panel: rgba(15, 23, 42, .84); --cyan: #06b6d4; --ice: #38bdf8; --ink: #f8fafc; --muted: #94a3b8; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; color: var(--ink); font-family: 'Kanit', sans-serif; background: #07111f url('../upload/7bb0bf4cf11ce39404ed0b656db19043.jpeg') center/cover fixed; }
        body::before { content: ''; position: fixed; inset: 0; background: linear-gradient(110deg, rgba(7,17,31,.96), rgba(7,17,31,.63)); z-index: 0; }
        .auth-shell { position: relative; z-index: 1; width: min(1060px, 92%); min-height: 620px; display: grid; grid-template-columns: 1fr 1fr; overflow: hidden; border: 1px solid rgba(56,189,248,.25); border-radius: 24px; background: var(--panel); box-shadow: 0 24px 80px rgba(0,0,0,.45); backdrop-filter: blur(18px); }
        .auth-brand { display: flex; flex-direction: column; justify-content: flex-end; padding: 54px; background: linear-gradient(145deg, rgba(6,182,212,.2), rgba(15,23,42,.1)); }
        .brand-mark { color: var(--ice); font: 700 1.15rem 'Space Grotesk', sans-serif; letter-spacing: .16em; }
        .auth-brand h1 { max-width: 390px; margin: 18px 0 12px; font: 700 clamp(2.6rem,5vw,4.8rem)/.95 'Space Grotesk', sans-serif; }
        .auth-brand p { max-width: 340px; margin: 0; color: #cbd5e1; font-size: 1.1rem; }
        .auth-form { display: flex; align-items: center; padding: 54px; }
        .form-card { width: 100%; max-width: 400px; margin: auto; }
        .form-card h2 { margin-bottom: 8px; font: 700 2rem 'Space Grotesk', sans-serif; }
        .form-intro { color: var(--muted); margin-bottom: 28px; }
        .form-label { color: #cbd5e1; }
        .form-control { min-height: 50px; border: 1px solid #334155; border-radius: 10px; color: var(--ink); background: rgba(30,41,59,.72); }
        .form-control:focus { color: var(--ink); border-color: var(--cyan); background: rgba(30,41,59,.9); box-shadow: 0 0 0 3px rgba(6,182,212,.16); }
        .btn-primary { min-height: 52px; border: 0; border-radius: 10px; background: var(--cyan); color: #06202b; font-weight: 700; }
        .btn-primary:hover { background: var(--ice); color: #06202b; }
        .login-link { color: var(--ice); text-decoration: none; }
        .login-link:hover { color: #bae6fd; }
        @media (max-width: 760px) { .auth-shell { display: block; min-height: auto; } .auth-brand { min-height: 250px; padding: 34px; } .auth-brand h1 { font-size: 3rem; } .auth-form { padding: 34px 24px 40px; } }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center py-4">
    <main class="auth-shell">
        <section class="auth-brand">
            <div class="brand-mark">KEY-GAMES / MARKETPLACE</div>
            <h1>Build your<br>library.</h1>
            <p>สร้างคลังเกมของคุณด้วยดีลที่คุ้มค่า และคีย์ดิจิทัลที่พร้อมใช้งาน</p>
        </section>
        <section class="auth-form">
          <form class="form-card" action="roe.php" method="POST">
            <h2>สร้างบัญชีใหม่</h2>
            <p class="form-intro">สมัครสมาชิกเพื่อเริ่มช้อปเกม</p>
            <?php if (isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php }; ?>

            <div class="mb-3">
                <label for="email" class="form-label fw-bold fs-5">อีเมล</label>
                <input type="email" class="form-control shadow fs-5" id="email" name="re-email" aria-describedby="emailHelp" required placeholder="กรุณากรอกอีเมล">
            </div>
            <div class="mb-3">
                <label for="username" class="form-label fw-bold fs-5">ชื่อผู้ใช้</label>
                <input type="text" class="form-control shadow fs-5" id="username" name="re-username" aria-describedby="emailHelp" required placeholder="กรุณากรอกชื่อผู้ใช้">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label fw-bold fs-5">รหัสผ่าน</label>
                <input type="password" class="form-control shadow fs-5" id="password" name="re-password" required placeholder="กรุณากรอกรหัสผ่าน">
            </div>

            <input type="submit" value="สมัครสมาชิก" class="btn btn-primary w-100 shadow fw-bold" name="re">
            <p class="text-center text-secondary mt-4 mb-0">มีบัญชีอยู่แล้ว? <a href="login.php" class="login-link fw-bold" target="_self">เข้าสู่ระบบ</a></p>
        </form>
        </section>
    </main>
</body>

</html>