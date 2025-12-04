<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Origin - Fashion Store'; ?></title>
    <link rel="stylesheet" href="web.css" ?v=<?php echo time(); ?>>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="aurora-container">
    <div class="aurora-blob blob-teal"></div>
    <div class="aurora-blob blob-orange"></div>
</div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="web.js"></script>

    <nav>
        <div class="nav-content">
            <h1 class="site-title">Origin</h1>
            <ul class="nav-menu">
                <li><a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="products.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Products</a></li>
                <li><a href="about.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About</a></li>
                <li><a href="contact.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
            </ul>
            <div class="nav-icons">
                <a class="icon" id="cartIcon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
                <div class="vertical-line"></div>
                <button class="login-btn" id="loginButton">
                    <span><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Log In'; ?></span>
                </button>
            </div>
        </div>
    </nav>

    <?php if (isset($_GET['action']) && $_GET['action'] == 'login'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const authModal = document.getElementById('authModal');
                if (authModal) {
                    authModal.classList.add('active');
                    document.body.classList.add('modal-open');
                }
            });
        </script>
    <?php endif; ?>