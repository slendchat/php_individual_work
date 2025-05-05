<?php
/** @var string $title */
/** @var array  $tickets */
/** @var bool   $isAdmin */
/** @var array  $filters */
?>
<form action="/tickets" method="get" class="search-form">
  <input
    type="text"
    name="q"
    placeholder="Search by title"
    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
  
  <select name="category">
    <option value="">All categories</option>
    <?php foreach(['Server','Administration','Network','Other'] as $cat): ?>
      <option value="<?= $cat?>"
        <?= (isset($_GET['category']) && $_GET['category']===$cat)?'selected':''?>>
        <?= $cat?>
      </option>
    <?php endforeach; ?>
  </select>
  
  <select name="priority">
    <option value="">Any priority</option>
    <?php foreach(['Low','Medium','High'] as $p): ?>
      <option value="<?= $p?>"
        <?= (isset($_GET['priority']) && $_GET['priority']===$p)?'selected':''?>>
        <?= $p?>
      </option>
    <?php endforeach; ?>
  </select>
  
  <button type="submit">Search</button>
</form>


<div class="ticket-list">
  <div class="tui-window">
    <fieldset class="tui-fieldset">
      <legend>
        <?= htmlspecialchars($title) ?>
      </legend>
      <?php if (empty($tickets)): ?>
        <p>No tickets to display.</p>
      <?php else: ?>
        <ul>
          <?php usort($tickets, function ($a, $b) {
            return strtotime($a['created_at']) <=> strtotime($b['created_at']);
          });
          ?>
          <?php foreach ($tickets as $t): ?>
            <li>
              <fieldset class="tui-fieldset tui-border-solid">
                <legend><a href="/ticket?id=<?= $t['id'] ?>">
                    [#<?= $t['id'] ?>] <?= htmlspecialchars($t['title']) ?>
                    (<?= htmlspecialchars($t['category']) ?>)</legend>

                <?php if ($isAdmin): ?>
                  <?php if ($t['status'] === "Open"): ?>
                    <span class="green-255-text">• <?= htmlspecialchars($t['status']) ?></span>
                  <?php elseif ($t['status'] === "Pending"): ?>
                    <span class="orange-255-text">• <?= htmlspecialchars($t['status']) ?></span>
                  <?php else: ?>
                    <span class="red-255-text">• <?= htmlspecialchars($t['status']) ?></span>
                  <?php endif; ?>
                <?php endif; ?>
                <span class="white-168-text">— <?= $t['created_at'] ?></span>
                </a>
                <br>
                <?php if ($isAdmin): ?>
                  <div class="button-row">
                    <a href="/ticket/edit?id=<?= $t['id'] ?>"><button class="tui-button">Edit</button></a>
                    <a href="/ticket/delete?id=<?= $t['id'] ?>" onclick="return confirm('Delete?')"><button
                        class="tui-button red-168">Delete</button></a>
                  </div>
                <?php endif; ?>
              </fieldset>
            </li>

          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
  </div>
</div>