                <div class="messages mt-3">
<?php foreach($messages as $message): ?>
                  <?= $t('message',['message'=>$message]) ?>
<?php endforeach ?>
                </div>
