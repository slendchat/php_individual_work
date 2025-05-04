<h2>Create New Admin</h2>

<?php if (!empty($_SESSION['success'])): ?>
  <div class="success">
    <?= htmlspecialchars($_SESSION['success']) ?>
  </div>
  <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div class="errors">
    <?php foreach ($errors as $e): ?>
      <p><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<form action="/admin/users/create" method="post">
  <label>Email:<br>
    <input type="email" name="email"
           value="<?= htmlspecialchars($old['email'] ?? '') ?>"
           required>
  </label><br>

  <label>Password:<br>
    <input type="password" name="password" required minlength="6">
  </label><br>

  <label>Repeat Password:<br>
    <input type="password" name="password2" required minlength="6">
  </label><br>

  <button type="submit">Create Admin</button>
</form>
