<!-- app/Views/layout/header.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title ?? 'Ticket System') ?></title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <nav>
    <?php if(!empty($_SESSION['user'])): ?>
      <a href="/">Главная</a> |
      <a href="/ticket/create">Создать тикет</a> |
      <a href="/tickets">Список тикетов</a> |
      Привет, <?=htmlspecialchars($_SESSION['user']['email'])?> |
      <a href="/logout">Выход</a> |
      <?php else: ?>
        <a href="/">Главная</a> |
        <a href="/tickets">Список тикетов</a> |
        <a href="/login">Вход</a> |
        <a href="/register">Регистрация</a> |
      <?php endif; ?>
      <?php if(!empty($_SESSION['user']['is_admin'])): ?>
        | <a href="/admin/users/create">New Admin</a>
      <?php endif; ?>
      
  </nav>
  <main>
