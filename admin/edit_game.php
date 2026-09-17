<?php  
    require_once '../config/data.php';
    session_start(); 
    if(!isset($_SESSION['admin_login'])){
        header("Location: ../auth/login.php");
        exit();
    }

    if(isset($_POST['update'])){
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_FILES['image'];

        $image2 = $_POST['image2'];
        $upload = $_FILES['image']['name'];

        if($upload != ''){
                    $allowed = array('jpg', 'jpeg', 'png');
                    $extension = explode('.', $image["name"]);
                    $filesactext = strtolower(end($extension));
                    // $filenew = rand() . "." . $filesactext;
                    $filenew = bin2hex(random_bytes(16)) . "." . $filesactext;
                    $filePath = "../upload/" . $filenew;

                     if(in_array($filesactext, $allowed)){
                        if($image['size']>0 && $image['error']==0){
                            move_uploaded_file($image['tmp_name'], $filePath);
                        }
                     }
        }else{
            $filenew = $image2;
        }
        $sql = $conn->prepare("UPDATE games_table SET name_games=:name_games, price_games=:price_games, img_games=:img_games WHERE id_games=:id_games");
        $sql->bindParam(":id_games", $id);
        $sql->bindParam(":name_games", $name);
        $sql->bindParam(":price_games", $price);
        $sql->bindParam(":img_games", $filenew);
        $sql->execute();

        if($sql){
                        $_SESSION['success'] = "แก้ไขสินค้าเรียบร้อยแล้ว";
                        header("Location: admin_page.php?page=products");
                    }else{
                        $_SESSION['error'] = "มีบางอย่างผิดพลาด";
                        header("Location: admin_page.php?page=products");
                    }
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า | KEY-GAMES</title>
    <style>
        :root { --cyan: #06b6d4; --ice: #38bdf8; --paper: #f8fafc; --muted: #94a3b8; --slate: #1e293b; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; color: var(--paper); font-family: 'Kanit', sans-serif; background: radial-gradient(circle at 80% 0%, rgba(6,182,212,.13), transparent 30%), #07111f; }
        .edit-shell { max-width: 760px; }
        .edit-header { padding: 28px 30px; border: 1px solid rgba(56,189,248,.24); border-radius: 18px; background: rgba(15,23,42,.82); box-shadow: 0 18px 50px rgba(0,0,0,.25); }
        .eyebrow { color: var(--cyan); font: 600 .78rem 'Space Grotesk', sans-serif; letter-spacing: .16em; }
        .edit-header h1 { font: 700 clamp(2rem, 5vw, 3rem)/1.1 'Space Grotesk', sans-serif; }
        .edit-header h1 span { color: var(--ice); }
        .edit-header p { color: var(--muted); }
        .edit-panel { border: 1px solid rgba(148,163,184,.18); border-radius: 16px; background: rgba(15,23,42,.86); box-shadow: 0 18px 45px rgba(0,0,0,.2); }
        .form-label { color: #cbd5e1; }
        .form-control { border: 1px solid #334155; color: var(--paper); background: var(--slate); }
        .form-control:focus { border-color: var(--cyan); color: var(--paper); background: var(--slate); box-shadow: 0 0 0 .2rem rgba(6,182,212,.16); }
        .form-control:disabled, .form-control[readonly] { color: var(--muted); background: #172235; }
        .form-control::file-selector-button { color: var(--paper); background: #334155; }
        .preview-wrap { margin-top: 10px; padding: 10px; border: 1px solid rgba(148,163,184,.18); border-radius: 12px; background: #0b1424; }
        .preview-wrap img { display: block; width: 100%; max-height: 320px; object-fit: contain; border-radius: 8px; }
        .form-actions { margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(148,163,184,.18); }
        .save-btn { border: 0; border-radius: 9px; background: var(--cyan); color: #06202b; font-weight: 700; }
        .save-btn:hover { background: var(--ice); color: #06202b; }
        .back-btn { border-color: #475569; color: #cbd5e1; }
        .back-btn:hover { border-color: var(--ice); color: var(--ice); }
        @media (max-width: 576px) { .edit-header { margin-top: 18px !important; padding: 24px 20px; } .edit-panel { padding: 22px !important; } }
    </style>
</head>
<body>
    <?php 
        if(isset($_SESSION['admin_login'])) {
            $admin_id = $_SESSION['admin_login'];
            $stmt = $conn->prepare("SELECT * FROM bob WHERE id = ?");
            $stmt->execute([$admin_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    ?>    
    <main class="container edit-shell py-4 py-md-5">
        <header class="edit-header text-center mb-4">
            <div class="eyebrow text-uppercase">KEY-GAMES / PRODUCT EDITOR</div>
            <h1 class="mt-2 mb-2">แก้ไข<span>สินค้า</span></h1>
            <p class="mb-0">อัปเดตรายละเอียดเกมและรูปภาพสินค้า</p>
        </header>
        <section class="edit-panel p-4 p-md-5">
            <form action="edit_game.php" method="post" enctype="multipart/form-data">
                <?php  
                    if(isset($_GET['id'])){
                        $id = $_GET['id'];
                        $stmt = $conn->prepare("SELECT * FROM games_table WHERE id_games = ?");
                        $stmt->execute([$id]);
                        $data = $stmt->fetch();
                    }
                ?>
                <div class="mb-3">
                    <label for="id" class="form-label">รหัสสินค้า</label>
                    <input type="text" readonly value="<?= $data['id_games'] ?>" class="form-control" name="id" id="id" required>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อเกม</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?= $data['name_games'] ?>" required>
                    <input type="hidden"  class="form-control" name="image2" value="<?= $data['img_games'] ?>" required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">ราคา</label>
                    <input type="text" class="form-control" name="price" id="price" value="<?= $data['price_games'] ?>">
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">รูปภาพสินค้า</label>
                    <input type="file" class="form-control" name="image" id="imageinput">
                    <div class="preview-wrap">
                        <img id="preview" src="../upload/<?= $data['img_games'] ?>" alt="ตัวอย่างรูปภาพสินค้า">
                    </div>
                </div>

                <div class="form-actions d-flex justify-content-end gap-2">
                    <a class="btn back-btn" href="admin_page.php?page=products"><i class="bi bi-arrow-left me-1"></i>กลับ</a>
                    <button type="submit" name="update" class="btn save-btn"><i class="bi bi-check2 me-1"></i>บันทึก</button>
                </div>

            </form>
        </section>
    </main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script>
    let imageinput = document.getElementById('imageinput');
    let preview = document.getElementById('preview');

    imageinput.onchange = evt => {
        const [file] = imageinput.files;
        if(file){
            preview.src = URL.createObjectURL(file);
        }
    }
</script>

</body>
</html>