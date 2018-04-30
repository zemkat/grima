<?php

require_once("grima-lib.php");

class Boundwith extends GrimaTask {
	public $biblist = array();
	
	function do_task() {
		$this->bibs = preg_split('/\r\n|\r|\n/',$this['mms']);

		# BIBS
		foreach ($this->bibs as $mmsid) {
			$bib = new Bib();
			$bib->loadFromAlma($mmsid);
			$this->biblist[] = $bib;
		}

		# build array of unique titles
		$arrfor501 = array();
		foreach ($this->biblist as $k => $bib) {
			$title = $bib->get_title_proper();
			if ($k > 0) {
				$this->biblist[0]->appendField("774","1"," ",array(
					't' => $title,
					'w' => $bib['mms_id']
					)
				);
			} 
			if (!in_array($title,$arrfor501)) {
				$arrfor501[] = $title;
			}
		}

		foreach ($this->biblist as $bib) {
			$my501text = "Bound with: ";
			$skip = $bib->get_title_proper();
			foreach ($arrfor501 as $title) {
				if ($title != $skip) {
					$my501text .= $title . "; ";
				}
			}
			$my501text = preg_replace("/; $/",".",$my501text);
			$bib->appendField("501"," "," ",array('a' => $my501text));
			$bib->updateAlma();
		}

		## holdings list
		$this->biblist[0]->getHoldingsList();

		## HOLDING
		$mfhd = new Holding();
		$mfhd->loadFromAlma($this->biblist[0]['mms_id'], $this->biblist[0]->holdingsList->holdings[0]['holding_id']);

		foreach ($this->biblist as $k => $bib) {
			if ($k > 0) {
				$mfhd->appendField("014","1"," ",array('a' => $bib['mms_id']));
			}
		}
		$mfhd->updateAlma();
	}

	function print_success() {
        print <<<TEMPLATE
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Grima by Zemkat</title>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <style type="text/css">
      body { min-height: 100vh;  padding: 0; margin: 0; }
      .jumbotron{ min-height: 100vh; margin: 0; }
    </style>
  </head>
  <body>
    <div class="jumbotron">
      <div class="container">
        <div class="col-md-4 col-md-offset-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h2 class="panel-title">Created boundwith of:</h2>
            </div>
            <div class="panel-body">
              <ul>
TEMPLATE;

foreach ($this->biblist as $bib) {
	print "<li>" . $bib->get_title_proper() . "
(<a href=\"Hierarchy.php?mms_id={$bib['mms_id']}\">hierarchy</a>)
(<a href=\"PrintBib.php?mms_id={$bib['mms_id']}\">view record</a>)
</li>\n";
}

print '
              </ul>
            </div>
		  </div>
        </div>
      </div>
    </div>
  </body>
</html>
';

	}
}

Boundwith::RunIt();
