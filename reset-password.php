<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="image/logoA.png">
</head>
<body>
    <?php
    session_start();
    require 'db_config.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['reset-email'])) {
            // Шаг 1: Отправка email с токеном
            $email = trim($_POST['reset-email']);
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Генерация токена
                $token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Сохранение токена в базе
                $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
                $stmt->execute(['email' => $email, 'token' => $token, 'expires_at' => $expires_at]);

                // Отправка email
                $reset_link = "http://localhost:8888/vector_factory/reset-password.php?token=$token";
                $mail = new PHPMailer(true);
                try {
                    // Настройки SMTP (пример с Gmail)
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'your-email@gmail.com'; // Ваш Gmail
                    $mail->Password = 'your-app-password';    // Пароль приложения Gmail
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('your-email@gmail.com', 'Вектор');
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Сброс пароля';
                    $mail->Body = "Перейдите по ссылке для сброса пароля: <a href='$reset_link'>$reset_link</a>. Ссылка действительна 1 час.";
                    $mail->send();
                    $message = "Ссылка для сброса пароля отправлена на ваш email!";
                } catch (Exception $e) {
                    $message = "Ошибка отправки email: " . $mail->ErrorInfo;
                }
            } else {
                $message = "Email не найден!";
            }
        } elseif (isset($_POST['new-password']) && isset($_GET['token'])) {
            // Шаг 2: Обработка нового пароля
            $token = $_GET['token'];
            $new_password = trim($_POST['new-password']);

            if (strlen($new_password) < 3) {
                $message = "Пароль должен быть длиннее 2 символов!";
            } else {
                $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW()");
                $stmt->execute(['token' => $token]);
                $reset = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($reset) {
                    $email = $reset['email'];
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE email = :email");
                    $stmt->execute(['password_hash' => $password_hash, 'email' => $email]);

                    // Удаление использованного токена
                    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
                    $stmt->execute(['token' => $token]);

                    $message = "Пароль успешно изменён! <a href='login.php'>Войдите</a>";
                } else {
                    $message = "Недействительный или просроченный токен!";
                }
            }
        }
    }
    ?>
    <header class="header">
        <div class="logo-container">
            <img src="image/logoA.png" alt="Логотип" class="logo-img">
            <span class="logo-text">ВЕКТОР</span>
        </div>
        <nav class="nav-menu">
            <a href="index.html" class="nav-link">Главная</a>
            <a href="register.php" class="nav-link">Регистрация</a>
            <a href="login.php" class="auth-link login">Вход</a>
        </nav>
    </header>

    <main class="main-container">
        <section class="register">
            <h2 class="section-title">Сброс пароля</h2>
            <?php if ($message) echo "<p>$message</p>"; ?>
            <?php if (!isset($_GET['token'])): ?>
                <form class="register-form" method="POST">
                    <label for="reset-email">Email:</label>
                    <input type="email" id="reset-email" name="reset-email" placeholder="email@example.com" required>
                    <button type="submit" class="cta-button">Сбросить пароль</button>
                </form>
            <?php elseif (isset($_GET['token'])): ?>
                <form class="register-form" method="POST">
                    <label for="new-password">Новый пароль:</label>
                    <input type="password" id="new-password" name="new-password" placeholder="Введите новый пароль" required>
                    <button type="submit" class="cta-button">Установить новый пароль</button>
                </form>
            <?php endif; ?>
            <p class="register-login">
                Вспомнили пароль? <a href="login.php">Войти</a>
            </p>
        </section>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="image/logoA.png" alt="Логотип" class="logo-img">
                <span>ВЕКТОР</span>
            </div>
            <div class="footer-contacts">
                <p>© 2025 Компания по производству окон и дверей</p>
                <p><a href="admin_login.php" class="footer-admin-link">Вход для администратора</a></p>
            </div>
            <div class="footer-nav">
                <a href="index.html">Главная</a>
                <a href="cabinet.php">Личный кабинет</a>
            </div>
        </div>
    </footer>
</body>
</html>