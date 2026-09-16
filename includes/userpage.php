<?php
require_once __DIR__ . '/../config/data.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USER</title>
    <style>
        .user-page { color: #f8fafc; background: #07111f; }
        .user-page h1 { font: 700 clamp(1.6rem, 4vw, 2.2rem) 'Space Grotesk', sans-serif; }
        .user-table-wrap { max-height: 1200px; overflow: auto; border: 1px solid rgba(148,163,184,.18); border-radius: 14px; background: rgba(15,23,42,.82); }
        .user-table { min-width: 700px; margin: 0; color: #f8fafc; }
        .user-table thead { position: sticky; top: 0; z-index: 2; color: #38bdf8; background: #1e293b; }
        .user-table th, .user-table td { padding: 14px; border-color: #334155; vertical-align: middle; }
        .user-table tbody tr:hover { background: rgba(6,182,212,.06); }
        .user-page .modal-content { color: #f8fafc; border-color: #334155; background: #0f172a; }
        .user-page .form-control { color: #f8fafc; border-color: #334155; background: #1e293b; }
        .user-page .form-control:focus { color: #f8fafc; border-color: #06b6d4; background: #1e293b; }
        @media (max-width: 575.98px) { .user-page .container { margin-top: 1rem !important; } }
    </style>
</head>

<body class="user-page">

    <!-- edit_key -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form action="edit_users.php" method="post">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit USER</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- hidden -->
                        <input type="hidden" name="id" id="modal_id">

                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" id="modal_username" value="">
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="text" class="form-control" name="email" id="modal_email" value="">
                        </div>

                        <div class="mb-3">
                            <label>User Role</label>
                            <input type="text" class="form-control" name="user_role" id="modal_role" value="">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" name="submit_edit_users" class="btn btn-success">
                            บันทึก
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>



    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h1>USERS</h1>
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

        <!-- แสดงรายการusers -->

        <div class="user-table-wrap">
        <table class="table user-table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">UserName</th>
                    <th scope="col">Email</th>
                    <th scope="col">UserRole</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->query("SELECT id, username, email, user_role FROM bob");
                $stmt->execute();
                $users = $stmt->fetchAll();

                if (!$users) {
                    echo "<tr><td colspan='5' class='text-center py-4'>ไม่มีผู้ใช้ในระบบ</td></tr>";
                } else {
                    foreach ($users as $user) {
                ?>
                        <tr>
                            <th scope="row"><?= (int)$user['id'] ?></th>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['user_role']) ?></td>
                            <td>
                                <button type="button"
                                    class="btn btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal"

                                    data-id="<?= $user['id'] ?>"
                                    data-username="<?= htmlspecialchars($user['username']) ?>"
                                    data-email="<?= htmlspecialchars($user['email']) ?>"
                                    data-role="<?= htmlspecialchars($user['user_role']) ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>


                                <a href="../admin/user_delete.php?delete_user=<?= $user["id"] ?>" class="btn btn-danger" onclick="return confirm('คุณต้องการลบผู้ใช้หรือไม่ ??')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                <?php   }
                }
                ?>
            </tbody>
        </table>
        </div>

    </div>
    <script>
        const editUserModal = document.getElementById('editUserModal');

        editUserModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            document.getElementById('modal_id').value = button.getAttribute('data-id');
            document.getElementById('modal_username').value = button.getAttribute('data-username');
            document.getElementById('modal_email').value = button.getAttribute('data-email');
            document.getElementById('modal_role').value = button.getAttribute('data-role');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>