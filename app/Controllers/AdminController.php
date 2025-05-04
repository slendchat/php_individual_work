<?php
namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller
{
    private function ensureAdmin()
    {
        if (empty($_SESSION['user']['is_admin'])) {
            header('Location: /'); exit;
        }
    }

    // Показываем форму
    public function showCreateForm()
    {
        $this->ensureAdmin();

        // вытащим из сессии старые данные/ошибки, если были
        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old']    ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        $this->view('admin/create_user', compact('errors','old'));
    }

    // Обрабатываем сабмит
    public function create()
    {
        $this->ensureAdmin();

        // читаем из POST
        $email = trim($_POST['email'] ?? '');
        $p1    = $_POST['password'] ?? '';
        $p2    = $_POST['password2'] ?? '';

        $errors = [];
        if (!$email || !$p1 || !$p2) {
            $errors[] = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } elseif ($p1 !== $p2) {
            $errors[] = 'Passwords don’t match.';
        } elseif (strlen($p1) < 6) {
            $errors[] = 'Password must be at least 6 chars.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = ['email'=>$email];
            header('Location: /admin/users/create'); exit;
        }

        global $db;
        $hash = password_hash($p1, PASSWORD_BCRYPT);
        try {
            $stmt = $db->prepare("
                INSERT INTO users (email, password_hash, is_admin)
                VALUES (?, ?, 1)
            ");
            $stmt->execute([$email, $hash]);
        } catch (\PDOException $e) {
            // например, нарушение UNIQUE email
            $_SESSION['errors'] = ['User already exists.'];
            $_SESSION['old']    = ['email'=>$email];
            header('Location: /admin/users/create'); exit;
        }

        $_SESSION['success'] = 'Admin account created: ' . htmlspecialchars($email);
        header('Location: /admin/users/create'); exit;
    }
}
