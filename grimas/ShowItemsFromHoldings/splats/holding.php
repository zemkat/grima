<div class='card mt-2 bib'>
  <div class='card-header'>
    <h1 class='card-title'>Holding #<?=$e($holding['holding_id'])?>
      <a class='d-print-none viewlink' href="../PrintHolding/PrintHolding.php?holding_id=<?=$e($holding['holding_id'])?>">(view)</a>
    </h1>
  </div>
  <div class='card-body'>
    <div class="table">
      <table class="table">
      <tr>
        <th>Item PID</th>
        <th>Barcode</th>
        <th>Material Type</th>
        <th>Item Policy</th>
        <th>PO Line</th>
        <th>Description</th>
        <th>In Temp Location?</th>
        <th>Temp Library</th>
        <th>Temp Location</th>
        <th>Process Type</th>
        <th>Public Note</th>
      </tr>
    <?php foreach ($holding->items as $item): ?>
      <tr>
        <td><?=$e($item['item_pid'])?></td>
        <td><?=$e($item['barcode'])?></td>
        <td><?=$e($item['physical_material_type'])?></td>
        <td><?=$e($item['item_policy'])?></td>
        <td><?=$e($item['po_line'])?></td>
        <td><?=$e($item['description'])?></td>
        <td><?=$e($item['in_temp_location'])?></td>
        <td><?=$e($item['temp_library'])?></td>
        <td><?=$e($item['temp_location'])?></td>
        <td><?=$e($item['process_type'])?></td>
        <td><?=$e($item['public_note'])?></td>
      </tr>
    <?php endforeach ?>
    </div>
  </div>
</div>
