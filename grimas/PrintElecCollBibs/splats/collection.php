<?php foreach ($collection->services as $service): ?>
<div class="service">
<!-- <h3>Portfolio #<?=$service['service_id']?> -->
<?= $t('service',array('service'=>$service)); ?>
</div>
<?php endforeach ?>
