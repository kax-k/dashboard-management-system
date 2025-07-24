<?php
include('../Backend/Auth/Auth.php');
requireLogin();
require '../Backend/connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <title>Expenses | Namu Africa</title>
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
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.25);
      padding: 2rem 2.5rem;
      position: relative;
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
    .table thead {
      background: #0d6efd;
      color: white;
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
        <h3 class="fw-bold text-primary">Expenses Overview</h3>
      </div>
      <div class="col-auto">
        <form method="GET" class="d-flex" role="search">
          <input class="form-control me-2" type="search" name="search" placeholder="Search expenses..." aria-label="Search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
          <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>
      </div>
    </div>

    <?php
  
$date = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : '';

  $countExpenses = mysqli_query($conn, "SELECT COUNT(*) as total FROM expenses");
    $totalExpenses = mysqli_fetch_assoc($countExpenses)['total'];
    if(!empty($date)) {
        $countExpenses = mysqli_query($conn, "SELECT COUNT(*) as total FROM expenses WHERE DATE(expense_date) = '$date'");
        $totalExpenses = mysqli_fetch_assoc($countExpenses)['total'];
    }
    $countAmount = mysqli_query($conn, "SELECT SUM(amount) as total_amount FROM expenses WHERE 1");
    if(!empty($date)) {
        $countAmount = mysqli_query($conn, "SELECT SUM(amount) as total_amount FROM expenses WHERE DATE(expense_date) = '$date'");
    }
    $totalAmount = mysqli_fetch_assoc($countAmount)['total_amount'];
    ?>

    <div class="row g-4 mb-3">
      <div class="col-md-3">
        <div class="card text-white bg-secondary mb-3">
          <div class="card-body text-center">
            <h5 class="card-title">Total Expenses</h5>
            <p class="card-text"><?= $totalExpenses ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
          <div class="card-body text-center">
            <h5 class="card-title">Total Amount</h5>
            <p class="card-text"><?= number_format($totalAmount, 2) ?> RWF</p>
          </div>
        </div>
      </div>
    </div>
    <hr>
     <div class="row mb-3">
                <div class="col">
                    <h5 class="fw-bold text-primary">Manage Expenses</h5>
                </div>
                <div class="col-auto">
                    <button id="openExpensePopup" class="btn btn-primary">Add New Expense</button>
                </div>
                
            </div>

    <form method="GET">
      <div class="row mb-4">
        <div class="col-md-4">
          <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
        </div>
       
    
        <div class="col-md-4">
          <button class="btn btn-primary w-100">Apply Filters</button>
        </div>
      </div>
    </form>

    <div class="container mt-2 bg-white p-4 rounded">
      <?php
      $filters = [];
      $search = isset($_GET['search']) ? trim($_GET['search']) : '';
      $date = isset($_GET['date']) ? trim($_GET['date']) : '';
      $category = isset($_GET['category']) ? trim($_GET['category']) : '';

      if ($search !== '') {
        $escaped = mysqli_real_escape_string($conn, $search);
        $filters[] = "(expense_name LIKE '%$escaped%' OR description LIKE '%$escaped%' OR amount LIKE '%$escaped%' OR expense_date LIKE '%$escaped%')";
      }
      if ($date !== '') {
        $escapedDate = mysqli_real_escape_string($conn, $date);
        $filters[] = "DATE(expense_date) = '$escapedDate'";
      }
      if ($category !== '') {
        $escapedCat = mysqli_real_escape_string($conn, $category);
        $filters[] = "category_id = '$escapedCat'";
      }

      $where = count($filters) > 0 ? "WHERE " . implode(" AND ", $filters) : '';
      $getExpenses = mysqli_query($conn, "SELECT * FROM expenses $where ORDER BY created_at DESC");

      $i = 1;
      ?>
      <div class="table-responsive">
      <table class="table table-bordered table-striped text-center">
        <thead>
          <tr>
            <th>#</th>
            <th>Expense Name</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Date</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php while($data = mysqli_fetch_assoc($getExpenses)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($data['expense_name']) ?></td>
            <td><?= number_format($data['amount'], 2) ?> RWF</td>
            <td><?= htmlspecialchars($data['description']) ?></td>
            <td><?= $data['expense_date'] ?></td>
            <td><?= $data['created_at'] ?></td>
            <td>
              <button class="btn btn-sm btn-warning edit-expense-btn"
                      data-id="<?= $data['expense_id'] ?>"
                      data-name="<?= htmlspecialchars($data['expense_name']) ?>"
                      data-amount="<?= $data['amount'] ?>"
                      data-description="<?= htmlspecialchars($data['description']) ?>"
                      data-date="<?= $data['expense_date'] ?>">
                Edit
              </button>
              <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                <input type="hidden" name="delete_id" value="<?= $data['expense_id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger" name="delete_expense">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>

        <?php
        if (isset($_POST['delete_expense'])) {
          $deleteId = $_POST['delete_id'];
          $deleteQuery = mysqli_query($conn, "DELETE FROM expenses WHERE expense_id = '$deleteId'");
          echo "<script>alert('Expense deleted successfully.'); window.location.href='expenses.php';</script>";
        }
        ?>
        </tbody>
      </table>
</div>
      <div class="row mt-3">
        <div class="col">
          <a href="GenerateExpenseReport.php?dateEpx=<?= $date ?>" class="btn btn-primary">Download PDF Report</a>
        </div>
      </div>
    </div>
  </div>

  <!-- ADD Expense Popup -->
  <div class="blur-overlay"></div>
  <div class="popup-form">
    <div class="container-box">
      <h2>Add Expense</h2>
      <button id="closeExpensePopup" type="button" class="close-btn">&times;</button>
      <form action="../Backend/expense.php" method="POST">
        <div class="mb-3">
          <label class="form-label">Expense Name</label>
          <input type="text" class="form-control" name="expenseName" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Amount</label>
          <input type="number" step="0.01" class="form-control" name="amount" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" rows="2" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Expense Date</label>
          <input type="date" class="form-control" name="expense_date" required>
        </div>
        <button type="submit" class="btn btn-primary w-100" name="addexpense">Add Expense</button>
      </form>
    </div>
  </div>

  <!-- Edit Expense Popup -->
  <div class="blur-overlay" id="edit-blur-overlay" style="display:none;"></div>
  <div class="popup-form" id="edit-popup-form" style="display:none;">
    <div class="container-box">
      <h2>Edit Expense</h2>
      <button id="closeEditPopup" type="button" class="close-btn">&times;</button>
      <form id="editExpenseForm" action="../Backend/expense.php" method="POST">
        <input type="hidden" name="expense_id" id="editExpenseId">
        <div class="mb-3">
          <label class="form-label">Expense Name</label>
          <input type="text" class="form-control" name="expenseName" id="editExpenseName" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Amount</label>
          <input type="number" step="0.01" class="form-control" name="amount" id="editAmount" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" name="description" id="editDescription" rows="2" required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Expense Date</label>
          <input type="date" class="form-control" name="expense_date" id="editExpenseDate" required>
        </div>
        <button type="submit" class="btn btn-primary w-100" name="editexpense">Update Expense</button>
      </form>
    </div>
  </div>

</div>

<script>
  document.getElementById('openExpensePopup')?.addEventListener('click', () => {
    document.querySelector('.popup-form').style.display = 'block';
    document.querySelector('.blur-overlay').style.display = 'block';
  });
  document.getElementById('closeExpensePopup')?.addEventListener('click', () => {
    document.querySelector('.popup-form').style.display = 'none';
    document.querySelector('.blur-overlay').style.display = 'none';
  });
  document.querySelectorAll('.edit-expense-btn').forEach(button => {
    button.addEventListener('click', () => {
      document.getElementById('editExpenseId').value = button.getAttribute('data-id');
      document.getElementById('editExpenseName').value = button.getAttribute('data-name');
      document.getElementById('editAmount').value = button.getAttribute('data-amount');
      document.getElementById('editDescription').value = button.getAttribute('data-description');
      document.getElementById('editExpenseDate').value = button.getAttribute('data-date');
      document.getElementById('edit-popup-form').style.display = 'block';
      document.getElementById('edit-blur-overlay').style.display = 'block';
    });
  });
  document.getElementById('closeEditPopup')?.addEventListener('click', () => {
    document.getElementById('edit-popup-form').style.display = 'none';
    document.getElementById('edit-blur-overlay').style.display = 'none';
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
