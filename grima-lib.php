<?php
#
#       grima-lib.php - a library for running API calls in Alma
#
#       (c) 2017 Kathryn Lybarger. CC-BY-SA
#

class Grima { # keep api keys etc here ?
	public $hostname;
	public $apikey;
	public $system;
	public $userid;

	function __construct($hostname = null, $apikey = null) {
		if ((!$hostname) || (!$apikey)) {
			require_once("grima-config.php");
		} else {
			$this->hostname = $hostname; $this->apikey = $apikey;
		}
	}

	function dump() {
		print "APIKEY:" . $this->apikey . "\n";
		print "HOSTNAME:" . $this->hostname . "\n";
	}

	function get($url,$params) {
		foreach ($params as $k => $v) {
			$url = str_replace('{'.$k.'}',urlencode($v),$url);
		}
		$url = $this->hostname . $url . '?view=full&expand=None&apikey=' . urlencode($this->apikey);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		$response = curl_exec($ch);
		curl_close($ch);
		$xml = new DOMDocument();
		$xml->loadXML($response);
		return $xml;
	}

	function put($url,$params,$body) {
		foreach ($params as $k => $v) {
			$url = str_replace('{'.$k.'}',urlencode($v),$url);
		}
		$url = $this->hostname . $url . '?apikey=' . urlencode($this->apikey);
		$bodyxml = $body->saveXML($body->documentElement);
		$ch = curl_init();
 		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyxml);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		$response = curl_exec($ch);
		curl_close($ch);
	}

	function post($url,$params,$body) {
		foreach ($params as $k => $v) {
			$url = str_replace('{'.$k.'}',urlencode($v),$url);
		}
		$url = $this->hostname . $url . '?apikey=' . urlencode($this->apikey);
		$bodyxml = $body->saveXML($body->documentElement);
		$ch = curl_init();
 		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyxml);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		$response = curl_exec($ch);
		curl_close($ch);
	}

	function putBib($mms_id,$bib) { 
		return $this->put('/almaws/v1/bibs/{mms_id}',
			array('mms_id' => $mms_id),
			$bib
			);
	}

	function postHolding($mms_id,$holding) {
		return $this->post('/almaws/v1/bibs/{mms_id}/holdings',
			array('mms_id' => $mms_id),
			$holding
			);
	}

	function getbib($mms_id) {
		return $this->get('/almaws/v1/bibs/{mms_id}', array('mms_id' => $mms_id));
	}

	function getHoldingsList($mms_id) {
		return $this->get('/almaws/v1/bibs/{mms_id}/holdings', array('mms_id' => $mms_id));
	}

	function getHolding($mms_id,$holding_id) {
		return $this->get('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}', array(
			'mms_id' => $mms_id,
			'holding_id' => $holding_id
			));
	}

	function getItemList($mms_id,$holding_id) {
		return $this->get('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items', array('mms_id' => $mms_id, 'holding_id' => $holding_id));
	}

	function getItem($mms_id,$holding_id,$item_pid) {
		return $this->get('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items/{item_pid}', array(
			'mms_id' => $mms_id,
			'holding_id' => $holding_id,
			'item_pid' => $item_pid
			));
	}
}


$grima = new Grima();

class Bib { 
	public $xml;
	public $hxml;
	public $networknums = array();
	public $holdings = array();

	public $mms_id;

	# create from oclc record?

	function __construct($mms_id = null) {
		global $grima;
		if ($mms_id) {
			$this->mms_id = $mms_id;
			if (!$this->validMMS()) {
				# ERROR?
			}
			$this->xml = $grima->getbib($mms_id);
		} else {
			# create new one
		} 
	}

	function dump() {
		print_r($this->xml->saveXML());
	}

	function get_title() {
		$xpath = new DomXpath($this->xml);
		$title = $xpath->query("//bib/title");
		return $title[0]->nodeValue;
	}

	function get_author() {
		$xpath = new DomXpath($this->xml);
		$author = $xpath->query("//bib/author");
		return $author[0]->nodeValue;
	}

	function get_placeOfPublication() {
		$xpath = new DomXpath($this->xml);
		$placeOfPub = $xpath->query("//bib/place_of_publication");
		return $placeOfPub[0]->nodeValue;
	}

	function get_publisher() {
		$xpath = new DomXpath($this->xml);
		$publisher = $xpath->query("//bib/publisher_const");
		return $publisher[0]->nodeValue;
	}

	function get_networkNumbers() {
		$xpath = new DomXpath($this->xml);
		$netNums = $xpath->query("//bib/network_numbers/network_number");
		$ret = array();
		for ($j=0;$j<$netNums->length;$j++) {
			$ret[] = $netNums[$j]->nodeValue;
		}
		return $ret;
	}

	function appendField($tag,$ind1,$ind2,$subfields) {
		$xpath = new DomXpath($this->xml);
		$field = $this->xml->createElement("datafield");
		$field->setAttribute("tag",$tag);
		$field->setAttribute("ind1",$ind1);
		$field->setAttribute("ind2",$ind2);
		foreach ($subfields as $k => $v) {
			$subfield = $this->xml->createElement("subfield");
			$subfield->setAttribute("code",$k);
			$subfield->appendChild( $this->xml->createTextNode($v) );
			$field->appendChild($subfield);
		}
		$record = $xpath->query("//record");
		$record[0]->appendChild($field);
	}

	function deleteField($tag) {
		$xpath = new DomXpath($this->xml);
		$fields = $xpath->query("//record/datafield[@tag='$tag']");
		foreach( $fields as $field ) {
    		$field->parentNode->removeChild( $field );
		}
	}

	function load_holdingsList() {
		global $grima;
		$this->hxml = $grima->getHoldingsList($this->mms_id);
		$xpath = new DomXpath($this->hxml);
		$holdings = $xpath->query("//holding");
		$this->holdings = array();
		for ($j=0;$j<$holdings->length;$j++) {
			$mfhd = new Holding();
			$mfhd->mms_id = $this->mms_id;
			for ($k=0;$k<$holdings[$j]->childNodes->length;$k++) {
				$child = $holdings[$j]->childNodes[$k];
				switch($child->tagName) {
					case 'holding_id':
						$mfhd->holding_id = $child->nodeValue;
						break;
					case 'call_number':
						$mfhd->call_number = $child->nodeValue;
						break;
					case 'library':
						$mfhd->library_code = $child->nodeValue;
						$mfhd->library = $child->attributes[0]->nodeValue;
						break;
					case 'location':
						$mfhd->location_code = $child->nodeValue;
						$mfhd->location = $child->attributes[0]->nodeValue;
						break;
					default:
				}
			}
			# print_r($mfhd);
			$this->holdings[] = $mfhd;
		}
	}

	function updateAlma() {
		global $grima;
		$grima->putBib($this->mms_id,$this->xml);
	}

	function addToAlma() {
	}

	function validMMS() {
		return ((strlen($this->mms_id) == 16) and preg_match("/^\d+$/",$this->mms_id));
	}

	public static function valid_MMSID($str) {
		return (
			(strlen($str) == 16) && preg_match("/^99\d+$/",$str)
		);
	}

}

class Holding {

	public $mms_id;
	public $holding_id;
	public $call_number;
	public $library, $library_code;
	public $location, $location_code;

	public $items = array();

	public $xml;
	public $ixml;

	function __construct($mms_id = null,$holding_id = null) {
		global $grima;
		if (($mms_id) && ($holding_id)) {
			$this->mms_id = $mms_id;
			$this->holding_id = $holding_id;
			$this->xml = $grima->getHolding($mms_id,$holding_id);
		} else {
			$this->xml = new DOMDocument();
			$file = file_get_contents("templates/mfhd-blank.xml");
        	$this->xml->loadXML($file);
		}
	}

	function load($mms_id = null,$holding_id = null) {
		global $grima;
		if (($mms_id) && ($holding_id)) {
			$this->mms_id = $mms_id;
			$this->holding_id = $holding_id;
		}
		$this->xml = $grima->getHolding($this->mms_id,$this->holding_id);
	}

	function fluff() {
		global $grima;
		# make sure they're set
		$this->xml = $grima->getHolding($this->mms_id,$this->holding_id);
	}

	function dump() {
	}

	function updateAlma() {
	}

/*
	function addToAlmaBib($mms_id) {
	}
*/

	function load_itemList() {
		#print "LOADING ITEM LIST\n";
		global $grima;
		$this->ixml = $grima->getItemList($this->mms_id,$this->holding_id);
		$xpath = new DomXpath($this->ixml);
		$items = $xpath->query("//item/item_data");
		$this->items = array();
		for ($j=0;$j<$items->length;$j++) {
			$item = new Item();
			$item->mms_id = $this->mms_id;
			$item->holding_id = $this->holding_id;
			for ($k=0;$k<$items[$j]->childNodes->length;$k++) {
				$child = $items[$j]->childNodes[$k];
				switch($child->tagName) {
					case 'pid':
						#print "PID\n";
						$item->item_pid = $child->nodeValue;
						break;
					case 'barcode':
						#print "BARCODE\n";
						$item->barcode = $child->nodeValue;
						break;
					case 'description':
						$item->description = $child->nodeValue;
						break;
					case 'physical_material_type':
						$item->physical_material_type = $child->nodeValue;
						break;
					case 'enumeration_a':
						$item->enumeration_a = $child->nodeValue;
						break;
					case 'enumeration_b':
						$item->enumeration_b = $child->nodeValue;
						break;
					default:
						# print "OOPS: " . $child->tagName . "\n";
				}
			}
			#print "DUMPING!\n";
			#$item->dump();
			#print "DUMPING!\n";
			$this->items[] = $item;
		}
	}

}

class Item {

	public $item_pid;
	public $mms_id;
	public $holding_id;
	public $barcode;
	public $description;
	public $physical_material_type;
	public $enumeration_a;
	public $enumeration_b;
	public $xml;

	function __construct($mms_id = null,$holding_id = null,$item_pid = null) {
		global $grima;
		if (($mms_id) && ($holding_id) && ($item_pid)) {
			$this->mms_id = $mms_id;
			$this->holding_id = $holding_id;
			$this->item_pid = $item_pid;
			$this->xml = $grima->getItem($mms_id,$holding_id,$item_pid);
		}
	}

	function fluff() {
		# make sure they're set
		$this->xml = $grima->getItem($mms_id,$holding_id,$item_pid);
	}

	function dump() {
		print_r($this);
	}

	function updateAlma() {
	}

	function addToAlmaHolding($mms_id) {
	}

}

class Portfolio {
	function __construct() {
	}
}

