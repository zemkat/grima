<div class="card">
<div class="card-body">
<pre>
<?php $item->xml->formatOutput = true; ?>
<?= $e($item->xml->saveXml()); ?>
</pre>
</div>
</div>
