<?php
namespace App\Controllers;
use App\Core\Controller;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $this->view('auth/login');
    }

    public function login()
    {
        // простая валидация
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        if (!$email || !$pass) {
            $_SESSION['errors'] = ['Заполните все поля.'];
            header('Location: /login'); exit;
        }

        // ищем юзера
        global $db;
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($pass, $user['password_hash'])) {
            $_SESSION['errors'] = ['Неверный логин или пароль.'];
            header('Location: /login'); exit;
        }

        // ставим сессию
        $_SESSION['user']     = ['id'=>$user['id'],'email'=>$user['email'],'is_admin'=>$user['is_admin']];
        $_SESSION['success']  = 'Вы вошли как '.$user['email'];
        header('Location: /'); exit;
    }

    public function showRegisterForm()
    {
        $this->view('auth/register');
    }

    public function register()
    {
        $email = trim($_POST['email'] ?? '');
        $pass1 = $_POST['password'] ?? '';
        $pass2 = $_POST['password2'] ?? '';

        $errors = [];
        if (!$email || !$pass1 || !$pass2) {
            $errors[] = 'Все поля обязательны.';
        } elseif ($pass1 !== $pass2) {
            $errors[] = 'Пароли не совпадают.';
        } elseif (strlen($pass1)<6) {
            $errors[] = 'Пароль минимум 6 символов.';
        }

        if ($errors) {
            $_SESSION['errors'] = $errors;
            header('Location: /register'); exit;
        }

        global $db;
        $hash = password_hash($pass1, PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (email,password_hash) VALUES (?,?)");
        try {
            $stmt->execute([$email,$hash]);
        } catch(\PDOException $e) {
            $_SESSION['errors'] = ['Пользователь уже существует.'];
            header('Location: /register'); exit;
        }

        $_SESSION['success'] = 'Регистрация прошла успешно, войдите.';
        header('Location: /login'); exit;
        exit;
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /'); exit;
    }
}

