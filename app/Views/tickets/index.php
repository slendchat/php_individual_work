<h2><?=htmlspecialchars($title)?></h2>
<?php if (empty($tickets)): ?>
  <p>No tickets to display.</p>
<?php else: ?>
<ul>
<?php foreach($tickets as $t): ?>
  <li>
    <a href="/ticket?id=<?=$t['id']?>">
      [#<?=$t['id']?>] <?=htmlspecialchars($t['title'])?>
      (<?=htmlspecialchars($t['category'])?>)
      <?php if($isAdmin):?>
        — <em><?=htmlspecialchars($t['status'])?></em>
      <?php endif;?>
      — <?=$t['created_at']?>
    </a>

    <?php if($isAdmin): ?>
      | <a href="/ticket/edit?id=<?=$t['id']?>">Edit</a>
      | <a href="/ticket/delete?id=<?=$t['id']?>" 
           onclick="return confirm('Delete?')">Delete</a>
    <?php endif; ?>
  </li>
<?php endforeach;?>
</ul>
<?php endif; ?>