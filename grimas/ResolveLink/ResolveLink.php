<?php

require_once("../grima-lib.php");

class ResolveLink extends GrimaTask {

	function do_task() {
		$this->splatVars['old_url'] = $this['url'];
		$this->splatVars['body'] = array( 'content', 'messages' );
		if (preg_match("/^https/",$this['url'],$m)) {
			$input_url = $this['url'];
		} else {
			throw new Exception("Invalid URL: " . $this['url']);
		}
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $input_url); 
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch); 
		curl_close($ch);

		if (preg_match("/Location: ([^\\r]*)/",$output,$m)) {
			$url = $m[1];
			$this->splatVars['url'] = $url;
		} else {
			throw new Exception("Could not find Location");
		}
	}

}

ResolveLink::RunIt();
