<?php
// public/index.php

// 1) Подключаем конфиг (он инициализирует PDO и сессии)
require __DIR__ . '/../config/config.php';

// 2) Собственный автолоадер PSR-4–like
spl_autoload_register(function($class) {
    // префикс нашего неймспейса
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    // если класс не наш, выходим
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    // убираем префикс, переводим несуществующие \ в /, добавляем .php
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 3) Запускаем маршрутизацию
use App\Core\Router;

$router = new Router();
$router->get('/', 'HomeController@index');
// позже добавим другие роуты...

// Auth
$router->get('/login',       'AuthController@showLoginForm');
$router->post('/login',      'AuthController@login');
$router->get('/register',    'AuthController@showRegisterForm');
$router->post('/register',   'AuthController@register');
$router->get('/logout',      'AuthController@logout');

// Tickets
$router->get('/ticket/create',   'TicketController@createForm');
$router->post('/ticket/create',  'TicketController@create');

$router->get('/ticket/edit',     'TicketController@editForm');
$router->post('/ticket/edit',    'TicketController@edit');

$router->get('/ticket/delete',   'TicketController@delete');

$router->post('/ticket/status',  'TicketController@changeStatus');

$router->get('/tickets',         'TicketController@index');
$router->get('/ticket',          'TicketController@show');

// только для админа
$router->get('/admin/users/create',  'AdminController@showCreateForm' );
$router->post('/admin/users/create',  'AdminController@create' );


// далее тикеты, админ и т.п.
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
