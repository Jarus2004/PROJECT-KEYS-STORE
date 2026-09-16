<?php
require_once '../config/data.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .product-page { color: #f8fafc; background: #07111f; }
        .product-page h1 { font: 700 2rem 'Space Grotesk', sans-serif; }
        .product-table-wrap { overflow-x: auto; border: 1px solid rgba(148,163,184,.18); border-radius: 14px; background: rgba(15,23,42,.82); }
        .product-table { min-width: 700px; margin: 0; color: #f8fafc; }
        .product-table thead { color: #38bdf8; background: #1e293b; }
        .product-table th, .product-table td { padding: 15px; border-color: #334155; vertical-align: middle; }
        .product-table tbody tr:hover { background: rgba(6,182,212,.06); }
        .add-product-btn { border: 0; border-radius: 9px; background: #06b6d4; color: #06202b; font-weight: 700; }
        .add-product-btn:hover { background: #38bdf8; color: #06202b; }
        .modal-content { color: #f8fafc; border: 1px solid #334155; background: #0f172a; }
        .modal-content .form-control { color: #f8fafc; border-color: #334155; background: #1e293b; }
    </style>
</head>

<body class="product-page">
    <div class="modal fade" id="gameModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">เพิ่มสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="insert_product.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="col-form-label">Name:</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="col-form-label">Price:</label>
                            <input type="text" class="form-control" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="col-form-label">Image:</label>
                            <input type="file" class="form-control" name="image" id="imageinput" required>
                            <img width="100%" id="preview" alt="">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="submit" class="btn btn-success">บันทึก</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>


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
    <div style="max-height: 1200px; overflow-y: auto;">
        <div class="row w-100">
            <div class="col-md-6">
                <h1>ตารางสินค้า</h1>
            </div>
            <div class="col-md-6 d-flex justify-content-end p-2">
                <button type="button" class="btn add-product-btn" data-bs-toggle="modal" data-bs-target="#gameModal"><i class="bi bi-plus-lg me-1"></i>เพิ่มสินค้า</button>
            </div>
        </div>
        <div class="product-table-wrap">
        <table class="table product-table">
            <thead style="position: sticky; top: 0; z-index: 2;">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Image</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT * FROM games_table");
                $stmt->execute();
                $games = $stmt->fetchAll();

                if (!$games) {
                    echo "<p><td colspan='5' class='text-center'>ไม่มีสินค้าในระบบ</td></p>";
                } else {
                    foreach ($games as $game) {
                ?>
                        <tr>
                            <th scope="row"><?php echo $game['id_games']; ?></th>
                            <td><?= $game['name_games'] ?></td>
                            <td><?= $game['price_games'] ?></td>
                            <td width="250px"><img width="100%" src="../upload/<?= $game['img_games'] ?>" alt="<?= $game['name_games'] ?>" class="rounded"></td>
                            <td>
                                <a href="edit_game.php?id=<?= $game['id_games'] ?>" class="btn btn-warning"><i class="bi bi-pencil-square"></i></a>
                                <a href="game_delete.php?delete_game=<?= $game["id_games"] ?>" class="btn btn-danger" onclick="return confirm('คุณต้องการลบสินค้าหรือไม่ ??')"><i class="bi bi-trash"></i></a>
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
        <script>
            let imageinput = document.getElementById('imageinput');
            let preview = document.getElementById('preview');

            imageinput.onchange = evt => {
                const [file] = imageinput.files;
                if (file) {
                    preview.src = URL.createObjectURL(file);
                }
            }
        </script>
</body>

</html>