<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Вход | Вектор</title>
    <link rel="icon" href="logoA.png">
</head>
<body>
    <header class="header">
        <div class="logo-container">
            <img src="logoA.png" alt="Логотип компании Вектор" class="logo-img">
            <h1 class="logo-text">Вход</h1>
        </div>
        <nav class="nav-menu">
            <a href="index.html" class="nav-link">На главную</a>
            <a href="register.php" class="auth-link login">Регистрация</a>
        </nav>
    </header>

    <main class="main-container">
        <section class="login">
            <h2 class="section-title">Вход</h2>
            <?php
            session_start();
            require 'db_config.php';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = trim($_POST['login-email']);
                $password = trim($_POST['login-password']);

                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    header("Location: cabinet.php");
                    exit;
                } else {
                    echo "<p>Неверный email или пароль!</p>";
                }
            }
            ?>
            <form class="register-form" method="POST">
                <input type="email" name="login-email" placeholder="Email" required>
                <div class="password-container">
                    <input type="password" name="login-password" id="login-password" placeholder="Пароль" required>
                    <span class="toggle-password" onclick="togglePassword('login-password')"></span>
                </div>
                <button type="submit" class="cta-button">Войти</button>
            </form>
            <p class="register-login">Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
            <p class="register-login"><a href="reset-password.php">Забыли пароль?</a></p>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="logoA.png" alt="Логотип Вектор" class="logo-img">
                <span>ВЕКТОР</span>
            </div>
            <nav class="footer-nav">
                <a href="index.html#about">О нас</a>
                <a href="index.html#services">Услуги</a>
                <a href="index.html#contacts">Контакты</a>
            </nav>
        </div>
    </footer>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggle = input.nextElementSibling;
            if (input.type === "password") {
                input.type = "text";
                toggle.classList.add("visible");
            } else {
                input.type = "password";
                toggle.classList.remove("visible");
            }
        }
    </script>
</body>
</html>
