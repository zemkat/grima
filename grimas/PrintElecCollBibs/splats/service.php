<?php foreach ($service->portfolios as $portfolio): ?>
<div class="portfolio">
<!-- <h3>Portfolio #<?=$portfolio['portfolio_id']?> -->
<?= $t('marc',array('marc'=>$portfolio->bib)); ?>
</div>
<?php endforeach ?>
