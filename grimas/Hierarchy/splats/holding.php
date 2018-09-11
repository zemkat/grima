<div class='card ml-4 holding'>
  <div class='card-header'>
    <h2 class='card-title'>Holding #<?=$e($holding['holding_id'])?>
      <a class='d-print-none viewlink' href="../PrintHolding/PrintHolding.php?holding_id=<?=$e($holding['holding_id'])?>">(view)</a>
  </h2>
  </div>
  <div class='card-body'>
    <dl class="row">
      <dt class="col-md-2 text-right library">Library:</dt>
      <dd class="col-md-10 library"><?=$e($holding['library_code'] . " : " . 
          $holding['library'])?></dd>
      <dt class="col-md-2 text-right location">Location:</dt>
      <dd class="col-md-10 location"><?=$e($holding['location_code'] . " : " . 
          $holding['location'])?></dd>
      <dt class="col-md-2 text-right title">Call Number:</dt>
      <dd class="col-md-10 title"><?=$e($holding['call_number'])?></dd>
    </dl>
    <?php foreach ($holding->itemList->items as $item): ?>
      <?=$t('item', array('item' => $item))?>
    <?php endforeach ?>
  </div>
</div>
           
