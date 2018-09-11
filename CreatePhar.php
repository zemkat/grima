<?php
$pharFile = 'grima.phar';
$pharDir = '.';
$pharPort = 23115;

@unlink($pharFile);
ini_set("phar.readonly", 0); 
$p = new Phar($pharFile);
$p->buildFromDirectory($pharDir,",(?!(?:/config(?:|/.*)|.*\.sql)),");
$p->setSignatureAlgorithm(Phar::SHA512);
$p->compressFiles(Phar::GZ);
$p->setStub('<?php 
$pharFile = "' . $pharFile . '";
$pharPort = ' . $pharPort . ';
try {
    if(PHP_SAPI === "cli") {
        if(function_exists("pcntl_exec")) {
            pcntl_exec(PHP_BINARY, array("-S","127.0.0.1:$pharPort",$argv[0]));
        } else {
            $cmd = PHP_BINARY . " -S 127.0.0.1:$pharPort " . escapeshellarg($argv[0]);
            print("Run $cmd instead\n");
            system("open http://127.0.0.1:$pharPort/index.html;exec $cmd");
        }
    } else {
        Phar::mapPhar($pharFile);
        $script = "phar://$pharFile{$_SERVER["SCRIPT_NAME"]}";
        error_log($script);
        if( $_SERVER["SCRIPT_NAME"] == "/" ) {
            $script = "{$script}index.html";
        }
        if( file_exists($script) ) {
            $pathinfo = pathinfo($script);
            switch( $pathinfo["extension"] ) {
                case "mjs": 
                case "js": header("Content-type: text/javascript"); break;
                case "css": header("Content-type: text/css"); break;
                case "html": header("Content-type: text/html"); break;
                case "svg": header("Content-type: image/svg+xml"); break;
                case "png": header("Content-type: image/png"); break;
                case "jpeg":
                case "jpg": header("Content-type: image/jpeg"); break;
                case "gif": header("Content-type: image/gif"); break;
                case "ttf": header("Content-type: application/font-sfnt"); break;
                case "woff": header("Content-type: application/font-woff"); break;
                default: error_log("Hrm, new extension: $script");
            }
            require( $script );
        } else {
            error_log("no such $script");
            http_response_code(404);
        }
    }
} catch( Error $x ) {
    error_log($x);
}
__HALT_COMPILER();');
$p = new Phar($pharFile);
$s = $p->getSignature();
echo "$pharFile successfully created\n";
