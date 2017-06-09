<?php

function save_config($req) {
	$apikey = clean($req,'apikey');
	$system = clean($req,'system');
	$userid = clean($req,'userid');
	$hostname = clean($req,'hostname','https://api-eu.hosted.exlibrisgroup.com');
	file_put_contents("grima-config.php",
		"<?php\n" .
		"\$this->hostname='$hostname';\n" .
		"\$this->apikey='$apikey';\n" .
		"\$this->system='$system';\n" .
		"\$this->userid='$userid';\n"
	);
}
	
function check_config() {
	if( file_exists("grima-config.php") ) return;
	if(isset($_REQUEST['apikey']) && isset($_REQUEST['system']) && isset($_REQUEST['userid']))  {
		save_config($_REQUEST);
		return;
	}
	readfile("top.html");
	panel("Setup grima");
?>
          <form action="" method="post">
            <div class="form-group">
              <label for="apikey">API Key (get one here: <a href="https://developers.exlibrisgroup.com">developers.exlibrisgroup.com</a>)</label>
              <input class="form-control" id="apikey" name="apikey" placeholder="API Key" />
            </div>
            <div class="form-group">
              <label for="system">System Identifier</label>
              <input class="form-control" id="system" name="system" placeholder="System" />
            </div>
            <div class="form-group">
              <label for="apikey">User ID for new records</label>
              <input class="form-control" id="userid" name="userid" placeholder="User ID" />
            </div>
            <div class="form-group">
              <label for="hostname">ALMA Server</label>
              <input class="form-control" id="hostname" name="hostname" placeholder="https://api-eu.hosted.exlibrisgroup.com" />
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
          </form>
<?php
	panel();
	readfile("bot.html");
}

function clean($arr,$key,$default) {
	$val = isset($arr[$key])?$arr[$key]:$default;
	$val2 = preg_replace('/[^a-zA-Z0-9_-]/','',$val);
	return $val2;
}


function panel($title=false,$body=false) {
	if($body||$title) {
?>
      <div class="panel panel-default">
<?php } if($title) { ?>
        <div class='panel-heading'>
          <h1 class='panel-title'><?php echo $title ?>
        </div>
        <div class='panel-body'>
<?php } if($body || ($body==$title)) { ?>
        </div>
      </div>
<?php }
}

check_config();
