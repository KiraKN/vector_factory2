<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Регистрация | Вектор</title>
    <link rel="icon" href="image/logoA.png">
</head>
<body>
    <header class="header">
        <div class="logo-container">
            <img src="image/logoA.png" alt="Логотип компании Вектор" class="logo-img">
            <h1 class="logo-text">Регистрация</h1>
        </div>
        <nav class="nav-menu">
            <a href="index.html" class="nav-link">На главную</a>
            <a href="login.php" class="auth-link login">Вход</a>
        </nav>
    </header>

    <main class="main-container">
        <section class="register">
            <h2 class="section-title">Регистрация</h2>
            <?php
            require 'db_config.php';
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = trim($_POST['reg-email']);
                $password = password_hash(trim($_POST['reg-password']), PASSWORD_DEFAULT);
                $phone = trim($_POST['reg-phone']);
                $full_name = trim($_POST['reg-full-name']);
                $address = trim($_POST['reg-address']);

                try {
                    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, phone, full_name, address) VALUES (:email, :password_hash, :phone, :full_name, :address)");
                    $stmt->execute([
                        'email' => $email,
                        'password_hash' => $password,
                        'phone' => $phone,
                        'full_name' => $full_name,
                        'address' => $address
                    ]);
                    echo "<p>Регистрация успешна! <a href='login.php'>Войдите</a></p>";
                } catch (PDOException $e) {
                    if ($e->getCode() == 23000) {
                        echo "<p>Ошибка: Этот email уже зарегистрирован.</p>";
                    } else {
                        echo "<p>Ошибка: " . $e->getMessage() . "</p>";
                    }
                }
            }
            ?>
            <form class="register-form" method="POST">
    <input type="email" name="reg-email" placeholder="Email" required>
    <div class="password-container">
        <input type="password" name="reg-password" id="reg-password" placeholder="Пароль" required>
        <span class="toggle-password" onclick="togglePassword('reg-password')"></span>
    </div>
    <input type="text" name="reg-phone" placeholder="Номер телефона" required>
    <input type="text" name="reg-full-name" placeholder="ФИО" required>
    <textarea name="reg-address" placeholder="Адрес" required></textarea>
    <button type="submit" class="cta-button">Зарегистрироваться</button>
</form>

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
            <p class="register-login">Уже есть аккаунт? <a href="login.php">Войдите</a></p>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="image/logoA.png" alt="Логотип Вектор" class="logo-img">
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