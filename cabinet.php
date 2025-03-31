<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="image/logoA.png">
    <script src="script.js"></script>
</head>
<body>
    <?php
    session_start();
    require 'db_config.php';
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Получение данных пользователя
    $stmt = $pdo->prepare("SELECT email, phone, full_name, address FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Обновление данных
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update-profile'])) {
        $phone = trim($_POST['phone']);
        $full_name = trim($_POST['full_name']);
        $address = trim($_POST['address']);
        $stmt = $pdo->prepare("UPDATE users SET phone = :phone, full_name = :full_name, address = :address WHERE id = :id");
        $stmt->execute(['phone' => $phone, 'full_name' => $full_name, 'address' => $address, 'id' => $user_id]);
        header("Location: cabinet.php");
        exit;
    }

    // Обработка заказов (оставляем как было)
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit-order'])) {
        $order_type = $_POST['order-type'];
        $width = $_POST['width'];
        $height = $_POST['height'];
        $window_config = $_POST['window-config'] ?? '';
        $door_config = $_POST['door-config'] ?? '';
        $balcony_config = $_POST['balcony-config'] ?? '';
        $profile_system = $_POST['profile-system'];
        $sill = $_POST['sill'];
        $ledge = $_POST['ledge'];
        $installation = isset($_POST['installation']) ? 'Да' : 'Нет';
        $delivery = isset($_POST['delivery']) ? 'Да' : 'Нет';
        $district = $_POST['district'];

        $order_id_text = "#".str_pad(count($orders) + 1, 3, "0", STR_PAD_LEFT);
        $type = "$order_type: Ширина $width см, Высота $height см, ";
        if ($order_type == 'window') $type .= "Конфигурация: $window_config, ";
        elseif ($order_type == 'door') $type .= "Конфигурация: $door_config, ";
        elseif ($order_type == 'balcony') $type .= "Конфигурация: $balcony_config, ";
        $type .= "Профиль: $profile_system, Подоконник: $sill, Отлив: $ledge, Монтаж: $installation, Доставка: $delivery, Район: $district";

        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_id_text, type, status) VALUES (:user_id, :order_id_text, :type, 'В производстве')");
        $stmt->execute(['user_id' => $user_id, 'order_id_text' => $order_id_text, 'type' => $type]);
        header("Location: cabinet.php");
        exit;
    }

    if (isset($_POST['delete-order'])) {
        $order_id = $_POST['order_id'];
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $order_id, 'user_id' => $user_id]);
        header("Location: cabinet.php");
        exit;
    }
    ?>
    <header class="header">
        <div class="logo-container">
            <img src="image/logoA.png" alt="Логотип" class="logo-img">
            <span class="logo-text">Личный кабинет</span>
        </div>
        <nav class="nav-menu">
            <a href="index.html" class="nav-link">Главная</a>
            <a href="logout.php" class="auth-link logout">Выйти</a>
        </nav>
    </header>

    <main class="main-container">
        <section class="cabinet">
        <div class="user-info">
    <h2>Ваши данные</h2>
    <form method="POST">
        <div class="user-info-item">
            <span class="user-info-label">Email:</span>
            <span class="user-info-value"><?php echo htmlspecialchars($user['email']); ?></span>
        </div>
        <div class="user-info-item">
            <label class="user-info-label" for="phone">Номер телефона:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
        </div>
        <div class="user-info-item">
            <label class="user-info-label" for="full_name">ФИО:</label>
            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
        </div>
        <div class="user-info-item">
            <label class="user-info-label" for="address">Адрес:</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="update-profile" class="cta-button">Сохранить</button>
    </form>
</div>

            <div class="orders">
                <h3>Ваши заказы</h3>
                <div id="order-list" class="order-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-item">
                            <p>Заказ <?php echo htmlspecialchars($order['order_id_text']); ?></p>
                            <p>Тип: <?php echo htmlspecialchars($order['type']); ?></p>
                            <p>Статус: <span class="status"><?php echo htmlspecialchars($order['status']); ?></span></p>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" name="delete-order" class="delete-btn">Удалить</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Форма заказа остаётся без изменений -->
            <div class="new-order">
                <h3>Новый заказ</h3>
                <form class="order-form" id="order-form" method="POST">
                    <label for="order-type">Тип:</label>
                    <select id="order-type" name="order-type" required>
                        <option value="window">Окно</option>
                        <option value="door">Дверь</option>
                        <option value="balcony">Балкон</option>
                    </select>
                    <label for="width">Ширина (см):</label>
                    <input type="number" id="width" name="width" min="10" max="1000" placeholder="100" required>
                    <label for="height">Высота (см):</label>
                    <input type="number" id="height" name="height" min="10" max="1000" placeholder="120" required>
                    <div id="config-window" class="config-group">
                        <label for="window-config">Конфигурация окна:</label>
                        <select id="window-config" name="window-config">
                            <option value="single-fixed">Одностворчатое глухое — 4,500 ₽/м²</option>
                            <option value="single-open">Одностворчатое распашное — 5,000 ₽/м²</option>
                            <option value="single-tilt-turn">Одностворчатое поворотно-откидное — 5,500 ₽/м²</option>
                            <option value="double-fixed">Двухстворчатое глухое — 5,500 ₽/м²</option>
                            <option value="double-open">Двухстворчатое распашное — 6,000 ₽/м²</option>
                            <option value="double-tilt-turn">Двухстворчатое с поворотно-откидной створкой — 6,500 ₽/м²</option>
                            <option value="triple">Трёхстворчатое — 6,500 ₽/м²</option>
                        </select>
                    </div>
                    <div id="config-door" class="config-group">
                        <label for="door-config">Конфигурация двери:</label>
                        <select id="door-config" name="door-config">
                            <option value="single-in">Одиночная внутрь — 6,500 ₽/м²</option>
                            <option value="single-out">Одиночная наружу — 6,500 ₽/м²</option>
                            <option value="double">Двойная — 7,000 ₽/м²</option>
                            <option value="sliding">Раздвижная — 7,500 ₽/м²</option>
                        </select>
                    </div>
                    <div id="config-balcony" class="config-group">
                        <label for="balcony-config">Конфигурация балкона:</label>
                        <select id="balcony-config" name="balcony-config">
                            <option value="standard">Стандарт — 8,000 ₽/м²</option>
                            <option value="extended">Расширенный — 9,000 ₽/м²</option>
                            <option value="french">Французский — 9,500 ₽/м²</option>
                        </select>
                    </div>
                    <label for="profile-system">Профильная система:</label>
                    <select id="profile-system" name="profile-system" required>
                        <option value="rula6">Rula 60 (+2,000 ₽)</option>
                        <option value="rula7">Rula 70 (+3,000 ₽)</option>
                        <option value="rula5">Rula 58 (+1,000 ₽)</option>
                    </select>
                    <label for="sill">Подоконник:</label>
                    <select id="sill" name="sill" required>
                        <option value="none">Без подоконника — 0 ₽</option>
                        <option value="standard">Стандартный — 1,500 ₽</option>
                        <option value="premium">Премиум — 3,000 ₽</option>
                    </select>
                    <label for="ledge">Отлив:</label>
                    <select id="ledge" name="ledge" required>
                        <option value="none">Без отлива — 0 ₽</option>
                        <option value="standard">Стандартный — 1,000 ₽</option>
                        <option value="premium">Премиум — 2,000 ₽</option>
                    </select>
                    <div class="checkbox-group">
                        <label><input type="checkbox" id="installation" name="installation"> Монтаж</label>
                        <label><input type="checkbox" id="delivery" name="delivery"> Доставка</label>
                    </div>
                    <label for="district">Район:</label>
                    <select id="district" name="district" required>
                        <option value="center">Центр</option>
                        <option value="near">До 10 км от центра</option>
                        <option value="far">Более 10 км от центра</option>
                    </select>
                    <button type="button" id="calculate-order-btn" class="cta-button">Рассчитать</button>
                    <p id="order-result">Итоговая сумма: <span>0 ₽</span></p>
                    <button type="submit" id="submit-order-btn" name="submit-order" class="cta-button" disabled>Сделать заказ</button>
                </form>
            </div>
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