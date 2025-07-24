<?php
include '../Backend/Auth/Auth.php';
requireLogin();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Register</title>
    <style>
        body {
            background: #000;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-container {
            background: #fff;
            color: #000;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
            padding: 2rem 2.5rem;
            max-width: 400px;
            width: 100%;
        }
        h4 {
            color: #0d6efd;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        label {
            color: #0d6efd;
            font-weight: 500;
        }
        input[type="text"], input[type="password"] {
            border: 1px solid #0d6efd;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        input[type="submit"] {
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 0.5rem 1.5rem;
            font-weight: bold;
            width: 100%;
            transition: background 0.2s;
        }
        input[type="submit"]:hover {
            background: #003366;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h4>Create an Account</h4>
        <form action="..//Backend/register.php" method="POST">
            <div class="mb-3">
                <label for="username">Username:</label>
                <input class="form-control" type="text" id="username" name="username" required>
            </div>
              <div class="mb-3">
                <label for="role">Role:</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password">Password:</label>
                <input class="form-control" type="password" id="password" name="password" required>
            </div>
            <div>
                <input type="submit" name="register" value="Register">
            </div>
        </form>
    </div>
</body>