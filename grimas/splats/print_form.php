<!DOCTYPE html>
<html>
  <head>
<?=$t('head')?>
  </head>
  <body>
    <div class="jumbotron">
      <div class="container task-<?=$e($basename)?>">
        <div class="container mt-4 position-relative">
          <div class="position-absolute mx-auto help-button">
            <a class="btn btn-info" href="<?=$e($basename)?>.html">?</a>
          </div>
        </div>
        <!-- form -->
        <?= $t('card',array('body'=>array('form','messages'))) ?>
      </div>
    </div>
  </body>
</html>
