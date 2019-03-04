<?php
if(isset($form)): 
	// set defaults
	$method = "post";
	$action = "../$basename/$basename.php";
	$submit = "Submit";
	// then read everything from the form object
	$submitted = ($_SERVER['REQUEST_METHOD'] == 'POST');
	# $form_class = $submitted ? "was-validated" : "";
	$form_class = "";
	extract((array) $form);
?>
<form method="<?=$e($method)?>" action="<?=$e($action)?>" class="<?=$e($form_class)?>">
<?php foreach((array) $fields as $field): ?>
<?php   if ($field->visible): ?>
<?=$t('form-field',['field'=>$field,'submitted'=>$submitted])?>
<?php   endif ?>
<?php endforeach ?>
  <input class="btn btn-primary active" type="submit" value="<?=$e($submit)?>" />
</form>
<?php else: ?>
<!-- no form -->
<?php endif ?>
