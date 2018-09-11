<?php
    $translation = [
        'success'   => 'alert alert-success',
        'info'      => 'alert alert-info',
        'warning'   => 'alert alert-warning',
        'error'     => 'alert alert-danger',
        'debug'     => 'alert alert-primary',
    ];
    extract((array) $message);
    $class = $translation[$type];
?>
<div class="<?=$e($class)?>"><?=$e($message)?></div>
