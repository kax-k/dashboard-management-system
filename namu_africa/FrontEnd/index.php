<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome | Namu Africa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #0d6efd, #003366);
      color: #fff;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .welcome-box {
      background: #ffffff10;
      border-radius: 15px;
      padding: 3rem 2rem;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
      max-width: 500px;
      width: 100%;
    }
    .welcome-box h1 {
      font-weight: bold;
      margin-bottom: 1rem;
    }
    .welcome-box p {
      font-size: 1.1rem;
      margin-bottom: 2rem;
    }
    .btn-login {
      background-color: #fff;
      color: #0d6efd;
      border: none;
      padding: 0.6rem 1.5rem;
      font-weight: 600;
      border-radius: 8px;
      transition: all 0.3s;
    }
    .btn-login:hover {
      background-color: #f0f0f0;
      color: #003366;
    }
  </style>
</head>
<body>
  <div class="welcome-box">
    <h1>Welcome to Namu Africa</h1>
    <p>Your one-stop solution for managing expenses and products.</p>
    <a href="/Backend/FrontEnd/login.php" class="btn btn-login">Login</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
