<?php
require_once '../config/data.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEY-GAMES | Keys</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .key-page { color: #f8fafc; background: #07111f; }
        .key-page h1 { font: 700 clamp(1.6rem, 4vw, 2.2rem) 'Space Grotesk', sans-serif; }
        .key-table-wrap { max-height: 1200px; overflow: auto; border: 1px solid rgba(148,163,184,.18); border-radius: 14px; background: rgba(15,23,42,.82); }
        .key-table { min-width: 760px; margin: 0; color: #f8fafc; }
        .key-table thead { position: sticky; top: 0; z-index: 2; color: #38bdf8; background: #1e293b; }
        .key-table th, .key-table td { padding: 14px; border-color: #334155; vertical-align: middle; }
        .key-table tbody tr:hover { background: rgba(6,182,212,.06); }
        .key-value { max-width: 330px; overflow-wrap: anywhere; }
        .key-add-btn { border: 0; border-radius: 9px; background: #06b6d4; color: #06202b; font-weight: 700; }
        .key-add-btn:hover { background: #38bdf8; color: #06202b; }
        .key-page .modal-content { color: #f8fafc; border-color: #334155; background: #0f172a; }
        .key-page .form-control { color: #f8fafc; border-color: #334155; background: #1e293b; }
        .key-page .form-control:focus { color: #f8fafc; border-color: #06b6d4; background: #1e293b; }
        .key-page .btn-success { border: 0; background: #06b6d4; color: #06202b; }
        @media (max-width: 575.98px) {
            .key-page .container { margin-top: 1rem !important; }
            .key-page .row > div:last-child { margin-top: .75rem; justify-content: flex-start !important; }
        }
    </style>
</head>
<body class="key-page">
    <div class="modal fade" id="keyModal" tabindex="-1" aria-labelledby="addKeyTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addKeyTitle">เพิ่ม Game Keys</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="insert_keys.php" method="post">
                        <div class="mb-3">
                            <label for="add_product_id" class="col-form-label">Product ID:</label>
                            <input type="text" class="form-control" name="pid" id="add_product_id" required>
                        </div>
                        <div class="mb-3">
                            <label for="keys" class="col-form-label">Game Keys</label>
                            <textarea name="keys" id="keys" class="form-control" rows="10" placeholder="วางคีย์เกม 1 บรรทัด ต่อ 1 คีย์" required></textarea>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" name="submitkeys" class="btn btn-success">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editKeyModal" tabindex="-1" aria-labelledby="editKeyTitle" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="edit_keys.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editKeyTitle">แก้ไข Game Key</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="key_id" id="modal_key_id">
                        <div class="mb-3">
                            <label for="modal_pid">Product ID</label>
                            <input type="text" class="form-control" name="pid" id="modal_pid" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="modal_game_key">Game Key</label>
                            <input type="text" class="form-control" name="game_key" id="modal_game_key" value="" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" name="submit_edit_keys" class="btn btn-success">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1>GAME KEYS</h1>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end">
                <button type="button" class="btn key-add-btn p-2" data-bs-toggle="modal" data-bs-target="#keyModal"><i class="bi bi-plus-lg me-1"></i>เพิ่ม KEYS</button>
            </div>
        </div>
        <hr>
        <?php if (isset($_SESSION['success'])) { ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php } ?>
        <?php if (isset($_SESSION['error'])) { ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php } ?>

        <div class="key-table-wrap">
            <table class="table key-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Product ID</th>
                        <th scope="col">Game Key</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("SELECT * FROM game_keys");
                    $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!$keys) {
                        echo "<tr><td colspan='5' class='text-center py-4'>ไม่มีคีย์ในระบบ</td></tr>";
                    } else {
                        foreach ($keys as $key) {
                    ?>
                            <tr>
                                <th scope="row"><?= (int)$key['key_id'] ?></th>
                                <td><?= htmlspecialchars($key['product_id']) ?></td>
                                <td class="key-value"><?= htmlspecialchars($key['game_key']) ?></td>
                                <td><?= htmlspecialchars($key['status']) ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editKeyModal" data-id="<?= (int)$key['key_id'] ?>" data-pid="<?= htmlspecialchars($key['product_id'], ENT_QUOTES) ?>" data-key="<?= htmlspecialchars($key['game_key'], ENT_QUOTES) ?>" title="แก้ไข">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <a href="key_delete.php?delete_key=<?= (int)$key['key_id'] ?>" class="btn btn-danger" onclick="return confirm('คุณต้องการลบคีย์นี้หรือไม่?')" title="ลบ">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const editKeyModal = document.getElementById('editKeyModal');
        editKeyModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('modal_key_id').value = button.getAttribute('data-id');
            document.getElementById('modal_pid').value = button.getAttribute('data-pid');
            document.getElementById('modal_game_key').value = button.getAttribute('data-key');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
