<?php
namespace App\Controllers;
use App\Core\Controller;

/**
 * Controller responsible for user authentication and registration.
 */
class AuthController extends Controller
{
    /**
     * Display the login form.
     *
     * @return void
     *   Renders the 'auth/login' view.
     */
    public function showLoginForm()
    {
        $this->view('auth/login');
    }

    /**
     * Handle login form submission.
     *
     * Reads 'email' and 'password' from $_POST, validates presence,
     * looks up the user in the database, verifies the password,
     * and establishes the session on success.
     * On failure, stores error messages in session and redirects back to login.
     *
     * @return void
     *   Redirects to '/login' on validation or authentication failure,
     *   or to '/' on successful login.
     */
    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        if (!$email || !$pass) {
            $_SESSION['errors'] = ['Заполните все поля.'];
            header('Location: /login'); exit;
        }


        global $db;
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($pass, $user['password_hash'])) {
            $_SESSION['errors'] = ['Неверный логин или пароль.'];
            header('Location: /login'); exit;
        }


        $_SESSION['user']     = ['id'=>$user['id'],'email'=>$user['email'],'is_admin'=>$user['is_admin']];
        $_SESSION['success']  = 'Вы вошли как '.$user['email'];
        header('Location: /'); exit;
    }

    /**
     * Display the registration form.
     *
     * @return void
     *   Renders the 'auth/register' view.
     */
    public function showRegisterForm()
    {
        $this->view('auth/register');
    }

    /**
     * Handle registration form submission.
     *
     * Reads 'email', 'password', and 'password2' from $_POST,
     * validates required fields, password match, and length,
     * hashes the password, and inserts a new user record.
     * On validation or insertion failure, stores errors in session and redirects back.
     *
     * @return void
     *   Redirects to '/register' on validation failure,
     *   or to '/login' on successful registration.
     */
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

    /**
     * Log out the current user.
     *
     * Clears all session data and redirects to homepage.
     *
     * @return void
     */
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /'); exit;
    }
}

