                <div class="messages mt-3">
<?php foreach($messages as $message): ?>
                  <?= $t('message',['message'=>$message]) ?>
<?php endforeach ?>
<?php if (isset($redirect_url) && $redirect_url): ?>
				  <div class="alert alert-info">Continue on to <a class="alert-link" href="<?=$e($redirect_url);?>"><?=$e($redirect_url)?></a></div>
<?php endif ?>
                </div>
