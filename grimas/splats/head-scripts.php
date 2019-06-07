<?php
// include Task.js if it exists
$src = "$basename.js";
$local_scripts[] = $src;
if ($basename!="Login") {
	foreach ($form->fields as $field) {
		if($field->name == "password") {
			$local_scripts[] = "../Login/Login.js";
			break;
		}
	}
}
$basedirs = array( "../" => $webroot, "" => join_paths( $webroot, $basename ) );
foreach ($local_scripts as $src):
    foreach($basedirs as $href => $basedir):
        $abspath = "$basedir/$src";
        if (file_exists($abspath)):
            $hash = base64_encode(hash("sha384",file_get_contents($abspath),true));
            $integrity = "sha384-$hash";
?>
    <script src="<?=$e($href.$src)?>" integrity="<?=$e($integrity)?>"></script>
<?php break ?>
<?php endif ?>
<?php endforeach ?>
<?php endforeach ?>
