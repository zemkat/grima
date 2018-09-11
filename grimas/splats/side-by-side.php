    <h1 class="page-header"><?=$e($title)?></h1>
    <div class="container">
      <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-heading">
              <h2 class="card-title"><?=$e($leftTitle)?></h2>
            </div>
            <div class="card-body">
<?= $t('leftBody') ?>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card">
            <div class="card-heading">
              <h2 class="card-title"><?=$e($rightTitle)?></h2>
            </div>
            <div class="card-body">
<?= $t('rightBody') ?>
            </div>
          </div>
        </div>
      </div>
<?= $t('messages') ?>
   </div>
