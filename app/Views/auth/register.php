<?php if(!empty($_SESSION['errors'])): ?>
  <div class="errors">
    <?php foreach($_SESSION['errors'] as $err): ?>
      <p><?= htmlspecialchars($err) ?></p>
    <?php endforeach; ?>
    <?php unset($_SESSION['errors']); ?>
  </div>
<?php endif; ?>

<form action="/register" method="post">
  <label>Email:<br><input type="text" name="email" required></label><br>
  <label>Пароль:<br><input type="password" name="password" required></label><br>
  <label>Повторить пароль:<br><input type="password" name="password2" required></label><br>
  <button type="submit">Регистрация</button>
</form>
