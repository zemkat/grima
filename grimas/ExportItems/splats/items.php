MMS Record ID, HOL Record ID, Item PID, Barcode, Title, Publisher, Bib Material Type, Creator, Call Number, Permanent Call Number, Permanent Physical Location, Local Location, Holding Type, Item Material Type, Policy, Seq. Num, Chronology, Enumeration, Issue year, Description, Public note, Fulfillment Note, Inventory #, Inventory date, Shelf report #, On shelf date, On shelf seq, Last shelf report, Temp library, Temp location, Temp call # type, Temp call #, Temp item policy, Alt call # type, Alt call #, Pieces, Pages, Internal note (1), Internal note (2), Internal note (3), Statistics note (1), Statistics note (2), Statistics note (3), Creation date, Modification date, Status, Process type, Process Id, Number of loans, Last loan, Number of loans in house, Last loan in house, Year-to-date loans, Receiving date, Copy ID, Receive number, Weeding number, Weeding date
<?php foreach ($holding->items as $item): ?>

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
<?php endforeach ?>
