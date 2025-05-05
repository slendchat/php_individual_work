 <h2><?= htmlspecialchars($title) ?></h2> 

<table class="ticket-info-table">
  <tr><th>ID:</th><td><?= htmlspecialchars($ticket['id']) ?></td></tr>
  <tr><th>Title:</th><td><?= htmlspecialchars($ticket['title']) ?></td></tr>
  <tr><th>Description:</th><td><pre><?= htmlspecialchars($ticket['description']) ?></pre></td></tr>
  <tr><th>Category:</th><td><?= htmlspecialchars($ticket['category']) ?></td></tr>
  <tr><th>Priority:</th><td><?= htmlspecialchars($ticket['priority']) ?></td></tr>
  <tr><th>Due Date:</th><td><?= htmlspecialchars($ticket['due_date']) ?></td></tr>
  <tr><th>Urgent:</th><td><?= $ticket['is_urgent'] ? 'Yes' : 'No' ?></td></tr>
  <tr><th>Status:</th><td><?= htmlspecialchars($ticket['status']) ?></td></tr>
  <tr><th>Created At:</th><td><?= htmlspecialchars($ticket['created_at']) ?></td></tr>
</table>

<?php if($isAdmin): ?>
  <p>
    <a href="/ticket/edit?id=<?=$ticket['id']?>">Edit</a> |
    <a href="/ticket/delete?id=<?=$ticket['id']?>"
       onclick="return confirm('Delete?')">Delete</a>
  </p>

  <form action="/ticket/status" method="post">
    <input type="hidden" name="id" value="<?=$ticket['id']?>">
    <select name="status">
      <?php foreach(['Pending','Open','Closed'] as $s): ?>
        <option value="<?=$s?>" <?=$ticket['status']===$s?'selected':''?>>
          <?=$s?>
        </option>
      <?php endforeach;?>
    </select>
    <button type="submit">Change Status</button>
  </form>
<?php endif; ?>

<p><a href="/tickets">&larr; Back to list</a></p>
