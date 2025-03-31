<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход для администратора</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="logoA.png">
</head>
<body>
    <?php
    session_start();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $admin_pass = trim($_POST['admin-password']);
        if ($admin_pass === 'admin123') { // Замените на безопасный пароль
            $_SESSION['is_admin'] = true;
            header("Location: admin.php");
            exit;
        } else {
            echo "<p>Неверный пароль!</p>";
        }
    }
    ?>
    <header class="header">
        <div class="logo-container">
            <img src="logoA.png" alt="Логотип" class="logo-img">
            <span class="logo-text">Вход для администратора</span>
        </div>
    </header>

    <main class="main-container">
        <section class="register">
            <h2 class="section-title">Вход для администратора</h2>
            <form class="register-form" method="POST">
                <label for="admin-password">Пароль:</label>
                <input type="password" name="admin-password" placeholder="Введите пароль" required>
                <button type="submit" class="cta-button">Войти</button>
            </form>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="logoA.png" alt="Логотип" class="logo-img">
                <span>ВЕКТОР</span>
            </div>
        </div>
    </footer>
</body>
</html>
