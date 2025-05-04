<?php
namespace App\Controllers;

use App\Core\Controller;

class TicketController extends Controller
{
    public function index()
    {
        global $db;
        $isLoggedIn = !empty($_SESSION['user']);
        $isAdmin    = $isLoggedIn && $_SESSION['user']['is_admin'];

        if (!$isLoggedIn) {
            // только Open
            $where = "WHERE status = 'Open'";
            $title = 'Open Tickets';
        } elseif (!$isAdmin) {
            // Open + Closed
            $where = "WHERE status IN ('Open','Closed')";
            $title = 'Active Tickets';
        } else {
            // админ — всё
            $where = "";
            $title = 'All Tickets';
        }

        $sql = "
            SELECT id, title, category, status, created_at
            FROM tickets
            $where
            ORDER BY created_at DESC
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $tickets = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->view('tickets/index', compact('title','tickets','isAdmin'));
    }

    public function show()
    {
        global $db;
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) return header('Location:/tickets');

        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$ticket) return $this->abort404();

        // если Pending и не админ — редирект
        if ($ticket['status']==='Pending' && empty($_SESSION['user']['is_admin'])) {
            return header('Location:/tickets');
        }

        $this->view('tickets/show', [
            'title'=>'Ticket #'.$ticket['id'],
            'ticket'=>$ticket,
            'isAdmin'=>!empty($_SESSION['user']['is_admin'])
        ]);
    }
    
    
    public function createForm()
    {
        // доступ только для залогиненных
        if (empty($_SESSION['user'])) {
            header('Location: /login'); exit;
        }

        // передаём в вид ошибки и старые значения, если были
        $errors = $_SESSION['errors'] ?? [];
        $old    = $_SESSION['old']    ?? [];
        unset($_SESSION['errors'], $_SESSION['old']);

        $this->view('tickets/create', [
            'title'  => 'Create Ticket',
            'errors' => $errors,
            'old'    => $old,
        ]);
    }

    public function create()
    {
        if (empty($_SESSION['user'])) {
            header('Location: /login'); exit;
        }

        // собираем входные
        $old = [
            'title'       => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'category'    => $_POST['category'] ?? '',
            'priority'    => $_POST['priority'] ?? '',
            'due_date'    => $_POST['due_date'] ?? '',
            'is_urgent'   => isset($_POST['is_urgent']) ? 1 : 0,
        ];

        // серверная валидация
        $errors = [];
        if ($old['title']==='') {
            $errors[] = 'Title is required.';
        } elseif (mb_strlen($old['title']) > 255) {
            $errors[] = 'Title must be ≤ 255 chars.';
        }

        if ($old['description']==='') {
            $errors[] = 'Description is required.';
        }

        $allowedCats = ['Server','Administration','Network','Other'];
        if (!in_array($old['category'], $allowedCats, true)) {
            $errors[] = 'Invalid category.';
        }

        $allowedPrio = ['Low','Medium','High'];
        if (!in_array($old['priority'], $allowedPrio, true)) {
            $errors[] = 'Invalid priority.';
        }

        if (!preg_match('#^\d{4}-\d{2}-\d{2}$#', $old['due_date'])
            || !strtotime($old['due_date'])
        ) {
            $errors[] = 'Due date is invalid.';
        }

        // если есть ошибки — редирект обратно с flash
        if ($errors) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = $old;
            header('Location: /ticket/create');
            exit;
        }

        // вставляем в БД
        global $db;
        $stmt = $db->prepare("
            INSERT INTO tickets
            (user_id, title, description, category, priority, due_date, is_urgent)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user']['id'],
            $old['title'],
            $old['description'],
            $old['category'],
            $old['priority'],
            $old['due_date'],
            $old['is_urgent'],
        ]);

        $_SESSION['success'] = 'Ticket created successfully.';
        header('Location: /tickets');
        exit;
    }

    public function editForm(){
        if (empty($_SESSION['user']['is_admin'])) {
            header('Location:/tickets'); exit;
        }
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) { header('Location:/tickets'); exit; }

        global $db;
        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$ticket) { $this->abort404(); }

        // ошибки с прошлого сабмита
        $errors = $_SESSION['errors'] ?? [];
        // старые значения: либо из сессии после failed-валидации, либо из базы
        $old    = $_SESSION['old']    ?? $ticket;
        unset($_SESSION['errors'], $_SESSION['old']);

        $this->view('tickets/edit', compact('errors','old'));
    }

    public function edit(){
        if (empty($_SESSION['user']['is_admin'])) {
            header('Location:/tickets'); exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if (!$id) { header('Location:/tickets'); exit; }

        // Собираем все поля из POST
        $old = [
          'id'          => $id,
          'title'       => trim($_POST['title'] ?? ''),
          'description' => trim($_POST['description'] ?? ''),
          'category'    => $_POST['category'] ?? '',
          'priority'    => $_POST['priority'] ?? '',
          'due_date'    => $_POST['due_date'] ?? '',
          'is_urgent'   => isset($_POST['is_urgent']) ? 1 : 0,
          'status'      => $_POST['status'] ?? 'Pending',
        ];

        // Валидация (как в create() + проверка status)
        $errors = [];
        if ($old['title']==='') {
          $errors[] = 'Title is required.';
        }
        // … остальные проверки title/description/category/priority/due_date …

        if (!in_array($old['status'], ['Pending','Open','Closed'], true)) {
          $errors[] = 'Invalid status.';
        }

        if ($errors) {
          $_SESSION['errors'] = $errors;
          $_SESSION['old']    = $old;
          header('Location: /ticket/edit?id=' . $id);
          exit;
        }

        // Если всё ок, апдейтим запись
        global $db;
        $stmt = $db->prepare("
          UPDATE tickets SET
            title       = ?,
            description = ?,
            category    = ?,
            priority    = ?,
            due_date    = ?,
            is_urgent   = ?,
            status      = ?
          WHERE id = ?
        ");
        $stmt->execute([
          $old['title'], $old['description'], $old['category'],
          $old['priority'], $old['due_date'], $old['is_urgent'],
          $old['status'], $id
        ]);

        $_SESSION['success'] = 'Ticket updated.';
        header('Location: /ticket?id=' . $id);
        exit;
    }

    // DELETE
    public function delete()
    {
        if (empty($_SESSION['user']['is_admin'])) return header('Location:/tickets');
        $id = (int)($_GET['id']??0);
        if ($id) {
          global $db;
          $db->prepare("DELETE FROM tickets WHERE id=?")->execute([$id]);
        }
        $_SESSION['success']='Ticket deleted.';
        header('Location:/tickets');
    }

    // CHANGE STATUS через POST (админ)
    public function changeStatus()
    {
        if (empty($_SESSION['user']['is_admin'])) return header('Location:/tickets');
        $id = (int)($_POST['id']??0);
        $status = $_POST['status']??'Open';
        if ($id && in_array($status,['Open','Closed','Pending'],true)) {
          global $db;
          $db->prepare("UPDATE tickets SET status=? WHERE id=?")
             ->execute([$status,$id]);
          $_SESSION['success']="Status changed to $status.";
        }
        header('Location:/ticket?id='.$id);
    }

    protected function abort404()
    {
      http_response_code(404);
      echo "404 Not Found"; exit;
    }
    
}
