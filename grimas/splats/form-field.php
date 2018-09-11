<?php 
	$error_class = "";
	$auto="off";
	extract((array) $field);
	if ( $error_condition && $submitted ) {
		$error_class = " is-invalid alert-{$error_condition} has-{$error_condition}"; # " is-invalid"
	} else if( $submitted ) {
		$error_class = " is-valid";
	}
?>
<?php if ($type=="hidden"): ?>
  <input name="<?=$e($name)?>" id="<?=$e($name)?>" type="<?=$e($type)?>" value="<?=$e($value)?>" />
<?php else: ?>
  <div class="form-group<?=$e($error_class)?>">
<?php if ( isset($label) ): ?>
    <label for="<?=$e($name)?>"><?=$e($label)?></label>
<?php endif ?>
<?php if ($field->rows > 0): ?>
    <textarea
      rows="<?=$e($rows)?>"
      cols="20"
      class="form-control"
      name="<?=$e($name)?>"
      id="<?=$e($name)?>"
      placeholder="<?=$e($placeholder)?>"
    /><?=$e($value)?></textarea>
<?php elseif (count($field->options)): ?>
    <select
      name="<?=$e($name)?>"
      id="<?=$e($name)?>"
      class="form-control"
    >
<?php foreach($field->options as $option): ?>
      <?=$option?>
<?php endforeach ?>
    </select>
<?php else: ?>
    <input
      class="form-control<?=$e($error_class)?>"
      name="<?=$e($name)?>"
      id="<?=$e($name)?>"
      size="20"
      placeholder="<?=$e($placeholder)?>"
      autocomplete="<?=$e($auto)?>"
      type="<?=$e($type)?>"
      value="<?=$e($value)?>"
    />
<?php endif ?>
<?php if (isset($error_message)): ?>
    <small class="invalid-feedback"><?=$e($error_message)?></small>
<?php endif ?>
  </div>
<?php endif ?>
