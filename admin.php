<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Админ-панель | Вектор</title>
    <link rel="icon" href="logoA.png">
</head>
<body>
    <?php
    session_start();
    require 'db_config.php';
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header("Location: admin_login.php");
        exit;
    }

    // Получение всех пользователей
    $stmt = $pdo->query("SELECT id, email, phone, full_name, address FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Получение всех заказов
    $stmt = $pdo->query("SELECT o.*, u.email as user_email, u.id as user_id FROM orders o JOIN users u ON o.user_id = u.id");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Группировка заказов по пользователям
    $orders_by_user = [];
    foreach ($orders as $order) {
        $orders_by_user[$order['user_id']][] = $order;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update-status'])) {
        $order_id = $_POST['order_id'];
        $new_status = $_POST['status'];
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $new_status, 'id' => $order_id]);
        header("Location: admin.php");
        exit;
    }
    ?>
    <header class="header">
        <div class="logo-container">
            <img src="logoA.png" alt="Логотип компании Вектор" class="logo-img">
            <h1 class="logo-text">Админ-панель | Вектор</h1>
        </div>
        <nav class="nav-menu">
            <a href="index.html" class="nav-link">На главную</a>
            <a href="logout.php" class="auth-link logout">Выйти</a>
        </nav>
    </header>

    <main class="main-container">
        <section class="admin-panel">
            <h2 class="section-title">Панель администратора</h2>
            <div class="admin-info">
                <p>Добро пожаловать, <span>Администратор</span>!</p>
            </div>

            <div class="user-orders">
                <h3>Пользователи и их заказы</h3>
                <div class="user-orders-list">
                    <?php foreach ($users as $user): ?>
                        <div class="user-order-card">
                            <div class="user-order-header">
                                <h4 class="user-order-title"><?php echo htmlspecialchars($user['full_name'] ?? 'Не указано'); ?> (<?php echo htmlspecialchars($user['email']); ?>)</h4>
                                <input type="checkbox" id="user-<?php echo $user['id']; ?>" class="toggle-orders">
                                <label for="user-<?php echo $user['id']; ?>" class="toggle-label"></label>
                            </div>
                            <div class="user-order-details">
                                <p><strong>Телефон:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'Не указан'); ?></p>
                                <p><strong>Адрес:</strong> <?php echo htmlspecialchars($user['address'] ?? 'Не указан'); ?></p>
                                <h5>Заказы:</h5>
                                <?php if (isset($orders_by_user[$user['id']])): ?>
                                    <div class="order-list">
                                        <?php foreach ($orders_by_user[$user['id']] as $order): ?>
                                            <div class="order-item">
                                                <p><strong>Заказ:</strong> <?php echo htmlspecialchars($order['order_id_text']); ?></p>
                                                <p><strong>Тип:</strong> <?php echo htmlspecialchars($order['type']); ?></p>
                                                <p><strong>Статус:</strong> <span class="status"><?php echo htmlspecialchars($order['status']); ?></span></p>
                                                <form method="POST">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <select name="status" class="status-select">
                                                        <option value="В производстве" <?php echo $order['status'] == 'В производстве' ? 'selected' : ''; ?>>В производстве</option>
                                                        <option value="Готов к доставке" <?php echo $order['status'] == 'Готов к доставке' ? 'selected' : ''; ?>>Готов к доставке</option>
                                                        <option value="Доставлен" <?php echo $order['status'] == 'Доставлен' ? 'selected' : ''; ?>>Доставлен</option>
                                                    </select>
                                                    <button type="submit" name="update-status" class="save-btn">Сохранить</button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p>У пользователя пока нет заказов.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
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
</body>
</html>
