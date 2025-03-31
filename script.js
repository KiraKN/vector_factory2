// Проверка авторизации на всех страницах
function checkAuth() {
    const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
    const isAdmin = localStorage.getItem('isAdmin') === 'true';
    const registerLink = document.getElementById('register-link');
    const loginLink = document.getElementById('login-link');
    const logoutLink = document.getElementById('logout-link');

    if (isLoggedIn || isAdmin) {
        if (registerLink) registerLink.style.display = 'none';
        if (loginLink) loginLink.style.display = 'none';
        if (logoutLink) logoutLink.style.display = 'inline-block';
    } else {
        if (registerLink) registerLink.style.display = 'inline-block';
        if (loginLink) loginLink.style.display = 'inline-block';
        if (logoutLink) logoutLink.style.display = 'none';
    }

    if (!isLoggedIn && window.location.pathname.includes('cabinet.html')) {
        window.location.href = 'login.html';
    }
}

// Калькулятор на главной странице
if (document.getElementById('calculate-btn')) {
    document.getElementById('calculate-btn').addEventListener('click', function() {
        const width = parseFloat(document.getElementById('width').value) / 100;
        const height = parseFloat(document.getElementById('height').value) / 100;
        const type = document.getElementById('type').value;

        let pricePerSquareMeter;
        switch (type) {
            case 'single':
                pricePerSquareMeter = 5000;
                break;
            case 'double':
                pricePerSquareMeter = 5500;
                break;
            case 'triple':
                pricePerSquareMeter = 6000;
                break;
            default:
                pricePerSquareMeter = 0;
        }

        if (isNaN(width) || isNaN(height) || width <= 0 || height <= 0) {
            document.getElementById('result').innerHTML = 'Пожалуйста, введите корректные размеры';
            return;
        }

        const area = width * height;
        const totalCost = Math.round(area * pricePerSquareMeter);
        document.getElementById('result').innerHTML = `Примерная стоимость: <span>${totalCost} ₽</span>`;
    });
}

// Регистрация
if (document.getElementById('register-form')) {
    document.getElementById('register-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('reg-name').value;
        const email = document.getElementById('reg-email').value;
        const password = document.getElementById('reg-password').value;

        const user = { name, email, password, orders: [], initialized: false };
        localStorage.setItem('user', JSON.stringify(user));
        localStorage.setItem('isLoggedIn', 'false');

        alert('Регистрация успешна! Пожалуйста, войдите.');
        window.location.href = 'login.html';
    });
}

// Вход
if (document.getElementById('login-form')) {
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const user = JSON.parse(localStorage.getItem('user'));

        if (user && user.email === email && user.password === password) {
            localStorage.setItem('isLoggedIn', 'true');
            window.location.href = 'cabinet.html';
        } else {
            alert('Неверный email или пароль!');
        }
    });
}

// Личный кабинет: динамическая загрузка данных и создание заказа
if (document.getElementById('user-name')) {
    let user = JSON.parse(localStorage.getItem('user')) || { name: 'Гость', email: 'guest@example.com', orders: [], initialized: false };
    const defaultOrders = [
        { id: '#001', type: 'Двухстворчатое окно', status: 'В производстве' },
        { id: '#002', type: 'Балконная дверь', status: 'Доставлен' }
    ];

    if (!user.initialized) {
        user.orders = defaultOrders;
        user.initialized = true;
        localStorage.setItem('user', JSON.stringify(user));
    }

    document.getElementById('user-name').textContent = user.name;
    document.getElementById('user-email').textContent = user.email;

    const orderList = document.getElementById('order-list');
    orderList.innerHTML = '';
    user.orders.forEach((order, index) => {
        const orderItem = document.createElement('div');
        orderItem.classList.add('order-item');
        orderItem.innerHTML = `
            <p>Заказ ${order.id}</p>
            <p>Тип: ${order.type}</p>
            <p>Статус: <span class="status">${order.status}</span></p>
            <button class="delete-btn" data-index="${index}">Удалить</button>
        `;
        orderList.appendChild(orderItem);
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-index'));
            if (confirm('Вы уверены, что хотите удалить этот заказ?')) {
                user.orders.splice(index, 1);
                localStorage.setItem('user', JSON.stringify(user));
                alert('Заказ удалён!');
                window.location.reload();
            }
        });
    });

    const orderTypeSelect = document.getElementById('order-type');
    const configWindow = document.getElementById('config-window');
    const configDoor = document.getElementById('config-door');
    const configBalcony = document.getElementById('config-balcony');

    orderTypeSelect.addEventListener('change', function() {
        configWindow.style.display = this.value === 'window' ? 'block' : 'none';
        configDoor.style.display = this.value === 'door' ? 'block' : 'none';
        configBalcony.style.display = this.value === 'balcony' ? 'block' : 'none';
    });

    document.getElementById('calculate-order-btn').addEventListener('click', function() {
        const orderType = document.getElementById('order-type').value;
        const widthInput = document.getElementById('width').value;
        const heightInput = document.getElementById('height').value;
        const windowConfig = document.getElementById('window-config').value;
        const doorConfig = document.getElementById('door-config').value;
        const balconyConfig = document.getElementById('balcony-config').value;
        const profileSystem = document.getElementById('profile-system').value;
        const sill = document.getElementById('sill').value;
        const ledge = document.getElementById('ledge').value;
        const installation = document.getElementById('installation').checked;
        const delivery = document.getElementById('delivery').checked;
        const district = document.getElementById('district').value;

        const width = parseInt(widthInput);
        const height = parseInt(heightInput);
        if (isNaN(width) || isNaN(height) || width < 50 || width > 1000 || height < 50 || height > 1000) {
            document.getElementById('order-result').innerHTML = 'Ширина и высота должны быть целыми числами от 50 до 1000 см';
            return;
        }
        if (widthInput !== width.toString() || heightInput !== height.toString()) {
            document.getElementById('order-result').innerHTML = 'Введите целые числа без лишних символов';
            return;
        }

        if (!orderType || !profileSystem || !sill || !ledge || !district) {
            document.getElementById('order-result').innerHTML = 'Заполните все обязательные поля';
            return;
        }

        let totalCost = 0;
        const area = (width / 100) * (height / 100);

        if (orderType === 'window') {
            switch (windowConfig) {
                case 'single-fixed':
                    totalCost += area * 4500;
                    break;
                case 'single-open':
                    totalCost += area * 5000;
                    break;
                case 'single-tilt-turn':
                    totalCost += area * 5500;
                    break;
                case 'double-fixed':
                    totalCost += area * 5500;
                    break;
                case 'double-open':
                    totalCost += area * 6000;
                    break;
                case 'double-tilt-turn':
                    totalCost += area * 6500;
                    break;
                case 'triple':
                    totalCost += area * 6500;
                    break;
            }
        } else if (orderType === 'door') {
            switch (doorConfig) {
                case 'single-in':
                case 'single-out':
                    totalCost += area * 6500;
                    break;
                case 'double':
                    totalCost += area * 7000;
                    break;
                case 'sliding':
                    totalCost += area * 7500;
                    break;
            }
        } else if (orderType === 'balcony') {
            switch (balconyConfig) {
                case 'standard':
                    totalCost += area * 8000;
                    break;
                case 'extended':
                    totalCost += area * 9000;
                    break;
                case 'french':
                    totalCost += area * 9500;
                    break;
            }
        }

        switch (profileSystem) {
            case 'rula6':
                totalCost += 2000;
                break;
            case 'rula7':
                totalCost += 3000;
                break;
            case 'rula5':
                totalCost += 1000;
                break;
        }

        switch (sill) {
            case 'standard':
                totalCost += 1500;
                break;
            case 'premium':
                totalCost += 3000;
                break;
        }

        switch (ledge) {
            case 'standard':
                totalCost += 1000;
                break;
            case 'premium':
                totalCost += 2000;
                break;
        }

        if (installation) {
            if (orderType === 'window') {
                switch (district) {
                    case 'center':
                        totalCost += 3000;
                        break;
                    case 'near':
                        totalCost += 4000;
                        break;
                    case 'far':
                        totalCost += 5000;
                        break;
                }
            } else if (orderType === 'door') {
                switch (district) {
                    case 'center':
                        totalCost += 4000;
                        break;
                    case 'near':
                        totalCost += 5000;
                        break;
                    case 'far':
                        totalCost += 6000;
                        break;
                }
            } else if (orderType === 'balcony') {
                switch (district) {
                    case 'center':
                        totalCost += 6000;
                        break;
                    case 'near':
                        totalCost += 7000;
                        break;
                    case 'far':
                        totalCost += 8000;
                        break;
                }
            }
        }

        if (delivery) {
            if (orderType === 'window') {
                switch (district) {
                    case 'center':
                        totalCost += 1000;
                        break;
                    case 'near':
                        totalCost += 1500;
                        break;
                    case 'far':
                        totalCost += 2000;
                        break;
                }
            } else if (orderType === 'door') {
                switch (district) {
                    case 'center':
                        totalCost += 1500;
                        break;
                    case 'near':
                        totalCost += 2000;
                        break;
                    case 'far':
                        totalCost += 2500;
                        break;
                }
            } else if (orderType === 'balcony') {
                switch (district) {
                    case 'center':
                        totalCost += 2000;
                        break;
                    case 'near':
                        totalCost += 2500;
                        break;
                    case 'far':
                        totalCost += 3000;
                        break;
                }
            }
        }

        totalCost = Math.round(totalCost);
        document.getElementById('order-result').innerHTML = `Итоговая сумма: <span>${totalCost} ₽</span>`;
        document.getElementById('submit-order-btn').disabled = false;
    });

    document.getElementById('order-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const orderType = document.getElementById('order-type').value;
        const width = document.getElementById('width').value;
        const height = document.getElementById('height').value;
        const windowConfig = document.getElementById('window-config').value;
        const doorConfig = document.getElementById('door-config').value;
        const balconyConfig = document.getElementById('balcony-config').value;
        const profileSystem = document.getElementById('profile-system').value;
        const sill = document.getElementById('sill').value;
        const ledge = document.getElementById('ledge').value;
        const installation = document.getElementById('installation').checked;
        const delivery = document.getElementById('delivery').checked;
        const district = document.getElementById('district').value;

        const orderId = `#${String(user.orders.length + 1).padStart(3, '0')}`;
        let typeText = '';
        if (orderType === 'window') {
            typeText = `${{
                'single-fixed': 'Одностворчатое глухое',
                'single-open': 'Одностворчатое распашное',
                'single-tilt-turn': 'Одностворчатое поворотно-откидное',
                'double-fixed': 'Двухстворчатое глухое',
                'double-open': 'Двухстворчатое распашное',
                'double-tilt-turn': 'Двухстворчатое с поворотно-откидной створкой',
                'triple': 'Трёхстворчатое'
            }[windowConfig]} окно (${width}x${height} см)`;
        } else if (orderType === 'door') {
            typeText = `${{
                'single-in': 'Одиночная внутрь',
                'single-out': 'Одиночная наружу',
                'double': 'Двойная',
                'sliding': 'Раздвижная'
            }[doorConfig]} дверь (${width}x${height} см)`;
        } else if (orderType === 'balcony') {
            typeText = `${{
                'standard': 'Стандартный',
                'extended': 'Расширенный',
                'french': 'Французский'
            }[balconyConfig]} балкон (${width}x${height} см)`;
        }

        const districtText = {
            'center': 'Центр',
            'near': 'До 10 км от центра',
            'far': 'Более 10 км от центра'
        }[district];

        const newOrder = {
            id: orderId,
            type: `${typeText}, профиль: ${profileSystem}, подоконник: ${sill}, отлив: ${ledge}${installation ? ', монтаж' : ''}${delivery ? ', доставка' : ''}, район: ${districtText}`,
            status: 'В производстве'
        };

        user.orders.push(newOrder);
        localStorage.setItem('user', JSON.stringify(user));
        alert('Заказ успешно создан!');
        window.location.reload();
    });
}

// Админ-панель: управление заказами
if (document.getElementById('admin-name')) {
    const admin = JSON.parse(localStorage.getItem('admin')) || { name: 'Администратор' };
    const user = JSON.parse(localStorage.getItem('user')) || { orders: [] };

    document.getElementById('admin-name').textContent = admin.name;

    const orderList = document.getElementById('admin-order-list');
    user.orders.forEach((order, index) => {
        const orderItem = document.createElement('div');
        orderItem.classList.add('order-item');
        orderItem.innerHTML = `
            <p>Заказ ${order.id}</p>
            <p>Тип: ${order.type}</p>
            <p>Статус: <span class="status">${order.status}</span></p>
            <select class="status-select" data-index="${index}">
                <option value="В производстве" ${order.status === 'В производстве' ? 'selected' : ''}>В производстве</option>
                <option value="Готов к доставке" ${order.status === 'Готов к доставке' ? 'selected' : ''}>Готов к доставке</option>
                <option value="Доставлен" ${order.status === 'Доставлен' ? 'selected' : ''}>Доставлен</option>
            </select>
            <button class="save-btn" data-index="${index}">Сохранить</button>
        `;
        orderList.appendChild(orderItem);
    });

    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            const newStatus = document.querySelector(`.status-select[data-index="${index}"]`).value;
            user.orders[index].status = newStatus;
            localStorage.setItem('user', JSON.stringify(user));
            alert('Статус заказа обновлён!');
            window.location.reload();
        });
    });
}

// Вход для администратора (имитация)
if (document.querySelector('.footer-admin-link')) {
    document.querySelector('.footer-admin-link').addEventListener('click', function(e) {
        e.preventDefault();
        const adminPass = prompt('Введите пароль администратора:');
        if (adminPass === 'admin123') {
            localStorage.setItem('isAdmin', 'true');
            localStorage.setItem('admin', JSON.stringify({ name: 'Администратор' }));
            window.location.href = 'admin.html';
        } else {
            alert('Неверный пароль!');
        }
    });
}

// Выход из личного кабинета или админ-панели
if (document.getElementById('logout-link') || document.getElementById('admin-logout-link')) {
    const logoutLinks = document.querySelectorAll('.logout');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            localStorage.setItem('isLoggedIn', 'false');
            localStorage.setItem('isAdmin', 'false');
            alert('Вы вышли из системы');
            window.location.href = 'index.html';
        });
    });
}

// Сброс пароля
if (document.getElementById('reset-password-form')) {
    const form = document.getElementById('reset-password-form');
    const emailInput = document.getElementById('reset-email');
    const passwordInput = document.getElementById('new-password');
    const passwordLabel = document.getElementById('new-password-label');
    const resetBtn = document.getElementById('reset-password-btn');
    let emailVerified = false;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const user = JSON.parse(localStorage.getItem('user'));

        if (!emailVerified) {
            const email = emailInput.value;
            if (user && user.email === email) {
                emailVerified = true;
                emailInput.disabled = true;
                passwordLabel.style.display = 'block';
                passwordInput.style.display = 'block';
                resetBtn.textContent = 'Установить новый пароль';
                alert('Email подтверждён. Введите новый пароль.');
            } else {
                alert('Email не найден!');
            }
        } else {
            const newPassword = passwordInput.value;
            if (newPassword.length < 3) {
                alert('Пароль должен быть длиннее 2 символов!');
                return;
            }
            user.password = newPassword;
            localStorage.setItem('user', JSON.stringify(user));
            alert('Пароль успешно изменён! Теперь вы можете войти.');
            window.location.href = 'login.html';
        }
    });
}

// Проверяем авторизацию при загрузке страницы
document.addEventListener('DOMContentLoaded', checkAuth);

document.addEventListener('DOMContentLoaded', function() {
    // Калькулятор на главной странице
    if (document.getElementById('calculate-btn')) {
        document.getElementById('calculate-btn').addEventListener('click', function() {
            const width = parseFloat(document.getElementById('width').value) / 100;
            const height = parseFloat(document.getElementById('height').value) / 100;
            const type = document.getElementById('type').value;

            let pricePerSquareMeter;
            switch (type) {
                case 'single': pricePerSquareMeter = 5000; break;
                case 'double': pricePerSquareMeter = 5500; break;
                case 'triple': pricePerSquareMeter = 6000; break;
                default: pricePerSquareMeter = 0;
            }

            if (isNaN(width) || isNaN(height) || width <= 0 || height <= 0) {
                document.getElementById('result').innerHTML = 'Пожалуйста, введите корректные размеры';
                return;
            }

            const area = width * height;
            const totalCost = Math.round(area * pricePerSquareMeter);
            document.getElementById('result').innerHTML = `Примерная стоимость: <span>${totalCost} ₽</span>`;
        });
    }

    // Калькулятор в личном кабинете
    const orderTypeSelect = document.getElementById('order-type');
    const configWindow = document.getElementById('config-window');
    const configDoor = document.getElementById('config-door');
    const configBalcony = document.getElementById('config-balcony');

    if (orderTypeSelect) {
        orderTypeSelect.addEventListener('change', function() {
            configWindow.style.display = this.value === 'window' ? 'block' : 'none';
            configDoor.style.display = this.value === 'door' ? 'block' : 'none';
            configBalcony.style.display = this.value === 'balcony' ? 'block' : 'none';
        });
        configWindow.style.display = orderTypeSelect.value === 'window' ? 'block' : 'none';
        configDoor.style.display = orderTypeSelect.value === 'door' ? 'block' : 'none';
        configBalcony.style.display = orderTypeSelect.value === 'balcony' ? 'block' : 'none';
    }

    if (document.getElementById('calculate-order-btn')) {
        document.getElementById('calculate-order-btn').addEventListener('click', function() {
            const orderType = document.getElementById('order-type').value;
            const widthInput = document.getElementById('width').value;
            const heightInput = document.getElementById('height').value;
            const windowConfig = document.getElementById('window-config').value;
            const doorConfig = document.getElementById('door-config').value;
            const balconyConfig = document.getElementById('balcony-config').value;
            const profileSystem = document.getElementById('profile-system').value;
            const sill = document.getElementById('sill').value;
            const ledge = document.getElementById('ledge').value;
            const installation = document.getElementById('installation').checked;
            const delivery = document.getElementById('delivery').checked;
            const district = document.getElementById('district').value;

            const width = parseInt(widthInput);
            const height = parseInt(heightInput);
            if (isNaN(width) || isNaN(height) || width < 50 || width > 1000 || height < 50 || height > 1000) {
                document.getElementById('order-result').innerHTML = 'Ширина и высота должны быть целыми числами от 50 до 1000 см';
                return;
            }

            let totalCost = 0;
            const area = (width / 100) * (height / 100);

            if (orderType === 'window') {
                switch (windowConfig) {
                    case 'single-fixed': totalCost += area * 4500; break;
                    case 'single-open': totalCost += area * 5000; break;
                    case 'single-tilt-turn': totalCost += area * 5500; break;
                    case 'double-fixed': totalCost += area * 5500; break;
                    case 'double-open': totalCost += area * 6000; break;
                    case 'double-tilt-turn': totalCost += area * 6500; break;
                    case 'triple': totalCost += area * 6500; break;
                }
            } else if (orderType === 'door') {
                switch (doorConfig) {
                    case 'single-in': case 'single-out': totalCost += area * 6500; break;
                    case 'double': totalCost += area * 7000; break;
                    case 'sliding': totalCost += area * 7500; break;
                }
            } else if (orderType === 'balcony') {
                switch (balconyConfig) {
                    case 'standard': totalCost += area * 8000; break;
                    case 'extended': totalCost += area * 9000; break;
                    case 'french': totalCost += area * 9500; break;
                }
            }

            switch (profileSystem) {
                case 'rula6': totalCost += 2000; break;
                case 'rula7': totalCost += 3000; break;
                case 'rula5': totalCost += 1000; break;
            }

            switch (sill) {
                case 'standard': totalCost += 1500; break;
                case 'premium': totalCost += 3000; break;
            }

            switch (ledge) {
                case 'standard': totalCost += 1000; break;
                case 'premium': totalCost += 2000; break;
            }

            if (installation) {
                if (orderType === 'window') {
                    switch (district) {
                        case 'center': totalCost += 3000; break;
                        case 'near': totalCost += 4000; break;
                        case 'far': totalCost += 5000; break;
                    }
                } else if (orderType === 'door') {
                    switch (district) {
                        case 'center': totalCost += 4000; break;
                        case 'near': totalCost += 5000; break;
                        case 'far': totalCost += 6000; break;
                    }
                } else if (orderType === 'balcony') {
                    switch (district) {
                        case 'center': totalCost += 6000; break;
                        case 'near': totalCost += 7000; break;
                        case 'far': totalCost += 8000; break;
                    }
                }
            }

            if (delivery) {
                if (orderType === 'window') {
                    switch (district) {
                        case 'center': totalCost += 1000; break;
                        case 'near': totalCost += 1500; break;
                        case 'far': totalCost += 2000; break;
                    }
                } else if (orderType === 'door') {
                    switch (district) {
                        case 'center': totalCost += 1500; break;
                        case 'near': totalCost += 2000; break;
                        case 'far': totalCost += 2500; break;
                    }
                } else if (orderType === 'balcony') {
                    switch (district) {
                        case 'center': totalCost += 2000; break;
                        case 'near': totalCost += 2500; break;
                        case 'far': totalCost += 3000; break;
                    }
                }
            }

            totalCost = Math.round(totalCost);
            document.getElementById('order-result').innerHTML = `Итоговая сумма: <span>${totalCost} ₽</span>`;
            document.getElementById('submit-order-btn').disabled = false;
        });
    }
});