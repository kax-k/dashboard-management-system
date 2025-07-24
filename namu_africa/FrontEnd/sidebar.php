<!-- Include Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Sidebar Styles */
    .sidebar {
        background: #0d6efd;
        color: #fff;
        padding: 2rem 1rem;
        width: 260px;
        min-height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1040;
        transition: all 0.3s ease-in-out;
    }

    .sidebar h2 {
        font-size: 2rem;
        margin-bottom: 2rem;
        color: #fff;
        text-align: center;
        letter-spacing: 2px;
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
    }

    .sidebar ul li {
        margin-bottom: 1.2rem;
    }

    .sidebar ul li a {
        color: #fff;
        text-decoration: none;
        font-size: 1.1rem;
        font-weight: 500;
        display: block;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
        background: #003366;
    }

    .sidebar ul li .logout:hover {
        background: #ff4d4d;
        color: #fff;
    }

    /* Toggle Button */
    .sidebar-toggle {
        display: none;
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 1050;
        background: #0d6efd;
        color: #fff;
        border: none;
        padding: 0.5rem 1rem;
        font-size: 1.2rem;
        border-radius: 6px;
    }

    /* Main Content */
    .main-content {
        margin-left: 260px;
        padding: 2rem;
        transition: margin-left 0.3s;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .body{
            background: #000;
            color: #fff;
        }
        .sidebar {
            left: -260px;

        }

        .sidebar.show {
            left: 0;

        }

        .sidebar-toggle {
            display: block;

        }

        .main-content {
            margin-left: 0;

        }

        .main-content.shifted {
            margin-left: 260px;

        }
    }
</style>

<!-- Toggle button -->
<button class="sidebar-toggle" id="toggleSidebar">&#9776;</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h2>Namu Africa</h2>
    <ul>
        <li><a href="dashboard.php" class="active">Dashboard</a></li>
        <li>
            <a href="#productSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Product</a>
            <div class="collapse" id="productSubmenu">
                <ul class="list-unstyled ms-3">
                    <li><a href="productin.php">Product In</a></li>
                    <li><a href="productout.php">Product Out</a></li>
                </ul>
            </div>
        </li>
        <li><a href="categories.php">Categories</a></li>
        <li><a href="expenses.php">Expenses</a></li>
        <?php
 if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { ?>
            <li><a href="users.php">Users</a></li>
        <?php } ?>

        <li><a href="report.php">Report</a></li>
        <li><a class="logout" href="../Backend/Auth/logout.php">Logout</a></li>
    </ul>
</div>

<!-- Optional JS to toggle sidebar -->
<script>
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.querySelector(".main-content");

    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("show");
        mainContent.classList.toggle("shifted");
    });

    // Auto-close sidebar on link click (for mobile)
    const sidebarLinks = sidebar.querySelectorAll("a");
    sidebarLinks.forEach(link => {
        link.addEventListener("click", () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove("show");
                mainContent.classList.remove("shifted");
            }
        });
    });

    // Optional: close sidebar if screen is resized to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove("show");
            mainContent.classList.remove("shifted");
        }
    });
</script>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
