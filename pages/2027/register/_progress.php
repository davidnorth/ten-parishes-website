<?php
$steps = ['Artist details', 'Venue', 'Dates', 'Image'];
$current = $current ?? 1;
?>
<ul class="wizard-progress">
<?php foreach ($steps as $i => $label): $n = $i + 1; ?>
  <li class="<?= $n < $current ? 'complete' : ($n === $current ? 'current' : '') ?>">
      <span><span> 
      <?php if($n < $current): ?>
        <iconify-icon icon="ph:check" class="icon-small" aria-hidden="true"></iconify-icon> 
      <?php endif ?>
      </span></span>
    <span><?= $label ?></span>
  </li>
<?php endforeach ?>
</ul>
