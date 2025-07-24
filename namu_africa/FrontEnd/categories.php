<?php
include('../Backend/Auth/Auth.php');
requireLogin();
require '../Backend/connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Categories | Namu Africa</title>
  <style>
    body {
      background: #f0f0f0;
      color: #000;
    }
    .main-content {
      margin-left: 270px;
      padding: 2rem;
    }
    .container-box {
      background: #fff;
      color: #000;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.25);
      padding: 2rem 2.5rem;
      position: relative;
    }
    .container-box h2 {
      color: #0d6efd;
      font-weight: bold;
      margin-bottom: 1.5rem;
      text-align: center;
    }
    h2, h3, h5 {
      color: #0d6efd;
      font-weight: bold;
    }
    .btn-primary {
      background: #0d6efd;
      border: none;
    }
    .btn-primary:hover {
      background: #003366;
    }
    label {
      color: #0d6efd;
      font-weight: 500;
    }
    .table thead {
      background: #0d6efd;
      color: #fff;
    }
    .blur-overlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0,0,0,0.3);
      backdrop-filter: blur(6px);
      z-index: 1040;
      display: none;
    }
    .popup-form {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1050;
      display: none;
      width: 100%;
      max-width: 500px;
    }
    .close-btn {
      position: absolute;
      top: 18px;
      right: 22px;
      color: #dc3545;
      background: none;
      border: none;
      font-size: 2rem;
      cursor: pointer;
      line-height: 1;
    }
  </style>
</head>
<body>
<?php include('sidebar.php'); ?>
<div class="main-content">
  <div class="container-fluid">
    <div class="row align-items-center mb-4">
      <div class="col">
        <h3 class="fw-bold text-primary">Categories Overview</h3>
      </div>
      <div class="col-auto">
        <form method="GET" class="d-flex" role="search">
          <input class="form-control me-2" type="search" name="search" placeholder="Search categories..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
          <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
      </div>
    </div>

    <?php
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM categories");
    $totalCategories = mysqli_fetch_assoc($result)['total'];
    ?>

    <div class="row g-4">
      <div class="col-md-3">
        <div class="card text-white bg-secondary mb-3">
          <div class="card-body text-center">
            <h5 class="card-title">Total Categories</h5>
            <p class="card-text"><?= $totalCategories ?></p>
          </div>
        </div>
      </div>
    </div>

    <hr>

    <div class="row mb-3">
      <div class="col">
        <h5 class="fw-bold text-primary">Manage Categories</h5>
      </div>
      <div class="col-auto">
        <button id="openCategoryPopup" class="btn btn-primary">Add New Category</button>
      </div>
    </div>
  </div>

  <div class="container mt-4 bg-white p-4 rounded">
    <div class="table-responsive">
    <table class="table table-striped table-bordered text-center">
      <thead>
        <tr>
          <th>#</th>
          <th>Category Name</th>
          <th>Description</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $search = $_GET['search'] ?? '';
        $i = 1;
        if (!empty($search)) {
          $escaped = mysqli_real_escape_string($conn, $search);
          $query = "SELECT * FROM categories WHERE category_name LIKE '%$escaped%' OR description LIKE '%$escaped%' ORDER BY category_id DESC";
        } else {
          $query = "SELECT * FROM categories ORDER BY category_id DESC";
        }
        $getCategories = mysqli_query($conn, $query);
        while ($category = mysqli_fetch_assoc($getCategories)) :
        ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($category['category_name']) ?></td>
            <td><?= htmlspecialchars($category['description']) ?></td>
            <td>
              <!-- You can link to a modal for editing -->
              <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this category?');">
                <input type="hidden" name="delete_id" value="<?= $category['category_id'] ?>">
                <button class="btn btn-sm btn-danger" name="delete_category">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    </div>
  </div>

  <!-- Handle deletion -->
  <?php
  if (isset($_POST['delete_category'])) {
    $id = $_POST['delete_id'];
    $deleteQuery = mysqli_query($conn, "DELETE FROM categories WHERE category_id = '$id'");
    if ($deleteQuery) {
      echo "<script>alert('Category deleted successfully.'); window.location.href='categories.php';</script>";
    } else {
      echo "<script>alert('Failed to delete category.');</script>";
    }
  }
  ?>

  <!-- Popup for adding new category -->
  <div class="blur-overlay"></div>
  <div class="popup-form">
    <div class="container-box">
      <h2>Add Category</h2>
      <button id="closeCategoryPopup" type="button" class="close-btn">&times;</button>
      <form method="POST" action="../Backend/categoty.php">
        <div class="mb-3">
          <label for="categoryName" class="form-label">Category Name</label>
          <input type="text" class="form-control" id="categoryName" name="categoryName" required>
        </div>
        <div class="mb-3">
          <label for="categoryDesc" class="form-label">Description</label>
          <textarea class="form-control" id="categoryDesc" name="categoryDesc" rows="2" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100" name="add_category">Add Category</button>
      </form>
    </div>
  </div>



</div>

<script>
  document.getElementById('openCategoryPopup').onclick = function () {
    document.querySelector('.popup-form').style.display = 'block';
    document.querySelector('.blur-overlay').style.display = 'block';
  };
  document.getElementById('closeCategoryPopup').onclick = function () {
    document.querySelector('.popup-form').style.display = 'none';
    document.querySelector('.blur-overlay').style.display = 'none';
  };
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
