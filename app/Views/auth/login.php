
<?php if (!empty($_SESSION['success'])): ?>
  <div class="success">
    <?= htmlspecialchars($_SESSION['success']) ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['errors'])): ?>
  <div class="errors">
    <?php foreach ($_SESSION['errors'] as $err): ?>
      <p><?= htmlspecialchars($err) ?></p>
    <?php endforeach; ?>
    <?php unset($_SESSION['errors']); ?>
  </div>
<?php endif; ?>

<form action="/login" method="post">
  <label>Email:<br><input type="text" name="email" required></label><br>
  <label>Пароль:<br><input type="password" name="password" required></label><br>
  <button type="submit">Войти</button>
</form>
<p>Нет аккаунта? <a href="/register">Зарегистрируйтесь</a></p>
