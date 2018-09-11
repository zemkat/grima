<div class='card mt-2 bib'>
  <div class='card-header'>
    <h1 class='card-title'>Bib #<?=$e($bib['mms_id'])?>
      <a class='d-print-none viewlink' href="../PrintBib/PrintBib.php?mms_id=<?=$e($bib['mms_id'])?>">(view)</a>
    </h1>
  </div>
  <div class='card-body'>
    <dl class="row">
      <dt class="col-md-2 text-right title">Title:</dt>
      <dd class="col-md-10 title"><?=$e($bib['title'])?></dd>
      <dt class="col-md-2 text-right author">Author:</dt>
      <dd class="col-md-10 author"><?=$e($bib['author'])?></dd>
      <dt class="col-md-2 text-right pop">Place of Publication:</dt>
      <dd class="col-md-10 pop"><?=$e($bib['place_of_publication'])?></dd>
      <dt class="col-md-2 text-right pub">Publisher:</dt>
      <dd class="col-md-10 pub"><?=$e($bib['publisher'])?></dd>
    </dl>
    <?php foreach ($bib->holdings as $holding): ?>
      <?=$t('holding', array('holding' => $holding))?>
    <?php endforeach ?>
  </div>
</div>
