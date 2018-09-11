<?php

require_once("../grima-lib.php");

class MoreItems extends GrimaTask {

	function do_task() {

		$this->item = new Item();
		$this->item->loadFromAlmaBarcode($this['barcode_model']);

		if (isset($this['adding'])) {
			$newItem = clone $this->item;
			unset($newItem['item_pid']);
			$itemdata_elements = array('barcode',
				'enumeration_a',
				'chronology_i',
				'description');
			foreach ($itemdata_elements as $element) {
				$newItem[$element] = $this[$element];
			}
			$ret = $newItem->addToAlmaHolding($this->item['mms_id'],$this->item['holding_id']);
			$this->item = new Item();
			$this->item->xml = $ret;
		}
	}

	function print_success() {
		$item = $this->item;
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
    <link rel="stylesheet" href="Grima.css"/>
    <link rel="stylesheet" href="MoreItems.css"/>
    <script src="MoreItems.js"></script>
  </head>
  <body>
    <div class="jumbotron">
      <div class="container">
        <h1 class="page-header">Add items in a series</h1>
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h2 class="panel-title">New Record</h2>
            </div>
            <div class="panel-body">
              <form method="post" action="MoreItems.php">
				<input type="hidden" name="barcode_model" value="{$this['barcode_model']}">
                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="enumeration_a">Enum A:</label>
                    <input class="form-control" name="enumeration_a" id="enumeration_a" size="20" value="{$item['enumeration_a']}" autocomplete="off"/>
                  </div>
                </div>
                <div class="row">
                <div class="form-group col-md-6">
                  <label for="chronology_i">Chron I:</label>
                  <input class="form-control" type="text" name="chronology_i" id="chronology_i" size="20" value="{$item['chronology_i']}" autocomplete="off">
                </div>
	            </div>
                <div class="row">
                  <div class="form-group col-md-12">
                    <label for="description">Description:</label>
                    <input class="form-control" type="text" name="description" id="description" size="20" value="{$item['description']}" autocomplete="off"/>
                  </div>
                </div>
                <div class="row">
                  <div class="form-group col-md-12">
                    <label class=for="barcode">Barcode</label>
                    <input class="form-control znew" type="text" name="barcode" id="barcode" size="20" placeholder="SCAN NEW BARCODE" autocomplete="off"/>
                  </div>
                </div>
                <input type="hidden" name="adding" value="true"> 
                <input class="btn btn-primary btn-lg active" type="submit" value="Add Item">
	
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h2 class="panel-title">Item based on:</h2>
            </div>
            <div class="panel-body">
              <dl class="dl-horizontal text-lg">
                <dt>Barcode:</dt><dd>{$item['barcode']}</dd>
                <dt>Enumeration A:</dt><dd>{$item['enumeration_a']}</dd>
                <dt>Chronology I:</dt><dd>{$item['chronology_i']}</dd>
                <dt>Material Type:</dt><dd>{$item['physical_material_type']}</dd>
                <dt>Item Policy:</dt><dd>{$item['policy']}</dd>
                <dt>Description:</dt><dd>{$item['description']}</dd>
                <dt>Title:</dt><dd>{$item['title']}</dd>
                <dt>Location:</dt><dd>{$item['location']}</dd>
                <dt>Call Number:</dt><dd>{$item['call_number']}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
TEMPLATE;
	}
}

MoreItems::RunIt();
