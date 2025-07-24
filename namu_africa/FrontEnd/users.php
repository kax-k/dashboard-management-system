<?php
include('../Backend/Auth/Auth.php');
require '../Backend/connect.php';
requireLogin();

// Handle delete
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM users WHERE user_id = '$deleteId'");
    echo "<script>window.location.href='users.php';</script>";
}

// Handle search
$search = $_GET['search'] ?? '';
$searchQuery = "SELECT * FROM users";
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $searchQuery .= " WHERE username LIKE '%$search%' OR role LIKE '%$search%'";
}
$searchQuery .= " ORDER BY user_id DESC";
$result = mysqli_query($conn, $searchQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users | Namu Africa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f0f0; color: #000; }
        .main-content { margin-left: 270px; padding: 2rem; }
        .user-section { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.1); padding: 2rem; }
        .popup-form, .blur-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 1050; }
        .popup-form.active, .blur-overlay.active { display: block; }
        .popup-form .user-section-popup {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.1);
            padding: 2rem;
            max-width: 400px;
            margin: auto;
            position: relative;
            top: 20%;
        }
        .blur-overlay { background: rgba(0,0,0,0.3); backdrop-filter: blur(6px); }
    </style>
</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main-content">
    <div class="container-fluid">
        <div class="user-section mb-4">
            <div class="row align-items-center mb-3">
                <div class="col">
                    <h2 class="mb-0">User Management</h2>
                </div>
                <div class="col-auto">
                    <button id="openPopup" class="btn btn-primary">Add New User</button>
                </div>
            </div>
            <form class="d-flex mb-3" role="search" method="GET">
                <input class="form-control me-2" type="search" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; while ($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><span class='badge bg-<?= $user['role'] === 'Administrator' ? 'primary' : 'info text-dark' ?>'><?= $user['role'] ?></span></td>
                            <td>
                               
                                <a href='?delete_id=<?= $user['user_id'] ?>' class='btn btn-sm btn-danger' onclick='return confirm("Are you sure?")'>Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="blur-overlay"></div>
    <div class="popup-form">
        <div class="user-section-popup">
            <h2 class="mb-3" id="formTitle">Add User</h2>
            <button type="button" id="closePopup" style="position:absolute;top:20px;right:20px;color:red;background:none;border:none;font-size:2rem;">&times;</button>
            <form action="../Backend/register.php" method="POST">
                <input type="hidden" id="editId" name="id">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="Administrator">Administrator</option>
                        <option value="manager">Manager</option>
                    </select>
                </div>
                <div class="mb-3" id="passwordGroup">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="register">Save</button>
            </form>
        </div>
    </div>
</div>
<script>
    const popup = document.querySelector('.popup-form');
    const overlay = document.querySelector('.blur-overlay');
    document.getElementById('openPopup').onclick = () => {
        popup.classList.add('active');
        overlay.classList.add('active');
        document.getElementById('formTitle').innerText = 'Add User';
        document.getElementById('editId').value = '';
        document.getElementById('username').value = '';
        document.getElementById('role').value = '';
        document.getElementById('password').required = true;
        document.getElementById('password').value = '';
        document.getElementById('passwordGroup').style.display = 'block';
    };
    document.getElementById('closePopup').onclick = () => {
        popup.classList.remove('active');
        overlay.classList.remove('active');
    };
    function editUser(id, username, role) {
        popup.classList.add('active');
        overlay.classList.add('active');
        document.getElementById('formTitle').innerText = 'Edit User';
        document.getElementById('editId').value = id;
        document.getElementById('username').value = username;
        document.getElementById('role').value = role;
        document.getElementById('passwordGroup').style.display = 'none';
        document.getElementById('password').required = false;
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
