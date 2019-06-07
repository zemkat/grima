    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<?php
// include Task.css if it exists
$src = "$basename.css";
$local_stylesheets[] = $src;
$basedirs = array( "../" => $webroot, "" => join_paths( $webroot, $basename ) );
foreach ($local_stylesheets as $src):
    foreach($basedirs as $href => $basedir):
        $abspath = "$basedir/$src";
        if (file_exists($abspath)):
            $hash = base64_encode(hash("sha384",file_get_contents($abspath),true));
            $integrity = "sha384-$hash";
?>
    <link rel="stylesheet" href="<?=$e($href.$src)?>" integrity="<?=$e($integrity)?>"/>
<?php break ?>
<?php endif ?>
<?php endforeach ?>
<?php endforeach ?>
