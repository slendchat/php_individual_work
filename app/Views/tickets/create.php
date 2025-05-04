<!-- app/Views/tickets/create.php -->
<h2><?= htmlspecialchars($title) ?></h2>

<!-- сюда будем выводить клиентские ошибки -->
<div id="formErrors" class="errors"></div>

<form id="ticketForm" action="/ticket/create" method="post" novalidate>
  <label>Title:<br>
    <input 
      type="text" 
      name="title"
      id="title"
      value="<?= htmlspecialchars($old['title'] ?? '') ?>"
      required 
      maxlength="255"
      placeholder="Up to 255 chars">
  </label><br>

  <label>Description:<br>
    <textarea 
      name="description" 
      id="description"
      required
      placeholder="Describe the issue…"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
  </label><br>

  <label>Category:<br>
    <select name="category" id="category" required>
      <option value="">-- choose --</option>
      <?php foreach(['Server','Administration','Network','Other'] as $cat): ?>
        <option value="<?= $cat?>"
          <?= (isset($old['category']) && $old['category']===$cat)?'selected':''?>>
          <?= $cat?>
        </option>
      <?php endforeach; ?>
    </select>
  </label><br>

  <label>Priority:<br>
    <?php foreach(['Low','Medium','High'] as $p): ?>
      <label>
        <input 
          type="radio" 
          name="priority" 
          id="priority_<?= strtolower($p) ?>" 
          value="<?= $p?>"
          <?= (isset($old['priority']) && $old['priority']===$p)?'checked':''?> 
          required>
        <?= $p?>
      </label>
    <?php endforeach; ?>
  </label><br>

  <label>Due Date:<br>
    <input 
      type="date" 
      name="due_date" 
      id="due_date"
      value="<?= htmlspecialchars($old['due_date'] ?? '') ?>" 
      required>
  </label><br>

  <label>
    <input 
      type="checkbox" 
      name="is_urgent" 
      id="is_urgent" 
      value="1"
      <?= !empty($old['is_urgent']) ? 'checked' : '' ?>>
    Mark as urgent
  </label><br>

  <button type="submit">Create Ticket</button>
</form>

<script>
(function(){
  const form = document.getElementById('ticketForm');
  const errorsDiv = document.getElementById('formErrors');

  form.addEventListener('submit', function(e) {
    errorsDiv.innerHTML = '';
    const errs = [];

    const title = document.getElementById('title').value.trim();
    if (!title) {
      errs.push('Title is required.');
    } else if (title.length > 255) {
      errs.push('Title must be 255 characters or fewer.');
    }

    const description = document.getElementById('description').value.trim();
    if (!description) {
      errs.push('Description is required.');
    }

    const category = document.getElementById('category').value;
    if (!category) {
      errs.push('Category must be selected.');
    }

    // at least one priority radio checked?
    if (!form.priority.value) {
      errs.push('Please choose a priority.');
    }

    const dueDateValue = document.getElementById('due_date').value;
    if (!dueDateValue) {
      errs.push('Due date is required.');
    } else {
      const dueDate = new Date(dueDateValue);
      const today = new Date();
      today.setHours(0,0,0,0);
      if (dueDate < today) {
        errs.push('Due date cannot be in the past.');
      }
    }

    if (errs.length) {
      e.preventDefault();
      errorsDiv.innerHTML = errs.map(msg => '<p>'+msg+'</p>').join('');
      // скроллим к ошибкам
      errorsDiv.scrollIntoView({ behavior: 'smooth' });
    }
  });
})();
</script>
