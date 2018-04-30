<?php
/**
 *       grima-lib.php - a library for running API calls in Alma
 *
 *       (c) 2018 Kathryn Lybarger. CC-BY-SA
 */

require_once("grima-util.php");
require_once("grima-xmlbag.php");

// {{{ class Grima 
/** class Grima */
class Grima { 
	public $server;
	public $apikey;

// {{{ config
	function __construct() {
		$this->get_config();
	}

	function get_config() {
	# Precedence:
    # $_REQUEST, $_SESSION, $_SERVER, $_ENV, grima-config.php

    	if (isset($_REQUEST['apikey']) and isset($_REQUEST['server']) and
            	($_REQUEST['apikey']) and ($_REQUEST['server'])) {
        	if ( !isset($_SESSION) ) session_start();
        	$_SESSION['apikey'] = $_REQUEST['apikey'];
        	$_SESSION['server'] = $_REQUEST['server'];
        	session_write_close();
			$this->apikey = $_SESSION['apikey'];
			$this->server = $_SESSION['server'];
			return true;
		}

		if ( isset($_COOKIE['PHPSESSID']) ) {
			if ( !isset($_SESSION) ) session_start();
			if (isset($_SESSION['apikey']) and isset($_SESSION['server']) and
					($_SESSION['apikey']) and ($_SESSION['server'])) {
				session_write_close();
				$this->apikey = $_SESSION['apikey'];
				$this->server = $_SESSION['server'];
				return true;
			}
		}

		if ( isset($_SERVER['apikey']) and isset($_SERVER['server']) and
				($_SERVER['apikey']) and ($_SERVER['server'])) {
			$this->apikey = $_SERVER['apikey'];
			$this->server = $_SERVER['server'];
			return true;
		}

		if ( isset($_ENV['apikey']) and isset($_ENV['server']) and
				($_ENV['apikey']) and ($_ENV['server'])) {
			$this->apikey = $_ENV['apikey'];
			$this->server = $_ENV['server'];
			return true;
		}

    	if( file_exists("grima-config.php") ) {
        	require('grima-config.php'); # this should set those
			return true;
    	}

		return false;
	}

// }}}

// {{{ get - general function for GET (retrieve) API calls
/**
 * @brief general function for GET (retrieve) API calls
 * 
 * @param string $url - URL pattern string with parameters in {}
 * @param array $URLparams - URL parameters
 * @param array $QSparams - query string parameters
 * @return DomDocument of requested record
 */
	function get($url,$URLparams,$QSparams) {
		# returns a DOM document
		foreach ($URLparams as $k => $v) {
			$url = str_replace('{'.$k.'}',urlencode($v),$url);
		}
		$url = $this->server . $url . '?apikey=' . urlencode($this->apikey);
		foreach ($QSparams as $k => $v) {
			$url .= "&$k=$v";
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		$response = curl_exec($ch);
		$code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		if (curl_errno($ch)) {
			throw new Exception("Network error: " . curl_error($ch));
		}
		curl_close($ch); 
		$xml = new DOMDocument();
		try {
			$xml->loadXML($response);
		} catch (Exception $e) {
			throw new Exception("Malformed XML from Alma: $e");
		}
		return $xml;
	}
// }}}

// {{{ post - general function for POST (create) API calls
/**
 * @brief general function for POST (create) API calls
 * 
 * @param string $url - URL pattern string with parameters in {}
 * @param array $URLparams - URL parameters
 * @param array $QSparams - query string parameters
 * @param DomDocument $body - object to add to Alma 
 * @return DomDocument $body - object as it now appears in Alma
 */
	function post($url,$URLparams,$QSparams,$body) { 
		foreach ($URLparams as $k => $v) {
			$url = str_replace('{'.$k.'}',urlencode($v),$url);
		}
		$url = $this->server . $url . '?apikey=' . urlencode($this->apikey);
		foreach ($QSparams as $k => $v) {
			$url .= "&$k=$v";
		}

		$bodyxml = $body->saveXML();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyxml);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		$response = curl_exec($ch);
		$code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		if (curl_errno($ch)) {
			throw new Exception("Network error: " . curl_error($ch));
		}
		curl_close($ch); 
		$xml = new DOMDocument();
		try {
			$xml->loadXML($response);
		} catch (Exception $e) {
			throw new Exception("Malformed XML from Alma: $e");
		}
		return $xml;
	}
// }}}

// {{{ put - general function for PUT (update) API calls
/**
 * @brief general function for PUT (update) API calls
 * 
 * @param string $url - URL pattern string with parameters in {}
 * @param array $URLparams - URL parameters
 * @param array $QSparams - query string parameters
 * @param DomDocument $body - record to update Alma record with
 * @return DomDocument - record as it now appears in Alma
 */
	function put($url,$URLparams,$QSparams,$body) {
		foreach ($URLparams as $k => $v) {
			$url = str_replace('{'.$k.'}',urlencode($v),$url);
		}
		$url = $this->server . $url . '?apikey=' . urlencode($this->apikey);
		foreach ($QSparams as $k => $v) {
			$url .= "&$k=$v";
		}

		$bodyxml = $body->saveXML();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyxml);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		$response = curl_exec($ch);
		$code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		if (curl_errno($ch)) {
			throw new Exception("Network error: " . curl_error($ch));
		}
		curl_close($ch); 
		$xml = new DOMDocument();
		try {
			$xml->loadXML($response);
		} catch (Exception $e) {
			throw new Exception("Malformed XML from Alma: $e");
		}
		return $xml;
	}
// }}}

// {{{ delete - general function for DELETE API calls
/**
 * @brief general function for DELETE API calls
 * 
 * @param string $url - URL pattern string with parameters in {}
 * @param array $URLparams - URL parameters
 * @param array $QSparams - query string parameters
 */
	function delete($url,$URLparams,$QSparams) {
		foreach ($URLparams as $k => $v) {
			$url = str_replace('{'.$k.'}',urlencode($v),$url);
		}
		$url = $this->server . $url . '?apikey=' . urlencode($this->apikey);
		foreach ($QSparams as $k => $v) {
			$url .= "&$k=$v";
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER,
			array ("Accept: application/xml"));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		$response = curl_exec($ch);
		$code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		if (curl_errno($ch)) {
			throw new Exception("Network error: " . curl_error($ch));
		}
		curl_close($ch); 
		if ($code != 204) {
			$xml = new DOMDocument();
			try {
				$xml->loadXML($response);
			} catch (Exception $e) {
				throw new Exception("Malformed XML from Alma: $e");
			}
			return $xml;
		}
	}
// }}}

// {{{ checkForErrorMessage - checks for errorMessage tag, throws exceptions
/**
 * @brief checks for errorMessage tag, throws exceptions
 * @param DomDocument $xml
 */
	function checkForErrorMessage($xml) {
		if ($xml instanceOf DomDocument) {
			$xpath = new DomXpath($xml);
			$xpath->registerNamespace("err","http://com/exlibris/urm/general/xmlbeans");
			$error = $xpath->query('//err:errorMessage');
			if ($error->length > 0) {
				throw new Exception("Alma says: " . $error[0]->nodeValue);
			}
		}
	}
// }}}

//{{{Bib APIs
/**@name Bib APIs */
/**@{*/

// {{{ getBib (Retrieve Bib)
/**
 * @brief Retrieve Bib - retrieve a bib record from Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      GET /almaws/v1/bibs/{mms_id}
 *
 * @param string $mms_id The Bib Record ID.
 * @param string $view Optional. Default=full
 * @param string $expand Optional. Default=None
 * @return DomDocument Bib object https://developers.exlibrisgroup.com/alma/apis/xsd/rest_bib.xsd?tags=GET
 */
	function getBib($mms_id, $view = 'full', $expand = 'None') {
		$ret = $this->get('/almaws/v1/bibs/{mms_id}',
			array('mms_id' => $mms_id),
			array('view' => $view, 'expand' => $expand) 
			);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

// {{{ postBib (Create Record)
/**
 * @brief Create Record - adds a new bib record to Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      POST /almaws/v1/bibs
 *		https://developers.exlibrisgroup.com/alma/apis/bibs#Resources
 * @param DomDocument $bib Bib object to add to Alma as new record
 * @return DomDocument Bib object as it now appears in Alma https://developers.exlibrisgroup.com/alma/apis/xsd/rest_bib.xsd?tags=GET
 */
	function postBib($bib) {
		$ret = $this->post('/almaws/v1/bibs',
			array(),
			array(),
			$bib
			);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

// {{{ putBib (Update Bib Record)
/**
 * @brief Update Bib Record - updates the copy of the bib in Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 * PUT /almaws/v1/bibs/{mms_id}
 *
 * @param string $mms_id Alma Bib record to update
 * @param DomDocument $bib Bib to replace old record
 * @return DomDocument Bib object as it now appears in Alma https://developers.exlibrisgroup.com/alma/apis/xsd/rest_bib.xsd?tags=GET
 */
	function putBib($mms_id,$bib) {
		$ret = $this->put('/almaws/v1/bibs/{mms_id}',
			array('mms_id' => $mms_id),
			array(),
			$bib
			);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

// {{{ deleteBib (Delete Bib Record)
/**
 * @brief Delete Bib Record - deletes the bib record from Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      DELETE /almaws/v1/bibs/{mms_id}
 *	
 * @param string $mms_id MMS ID of Alma Bib record to delete
 * @param string $override Optional. Default=false
 */
	function deleteBib($mms_id,$override='false') {
		$ret = $this->delete('/almaws/v1/bibs/{mms_id}', 
			array('mms_id' => $mms_id),
			array('override' => $override)
		);
		$this->checkForErrorMessage($ret);
	}
// }}}

/**@}*/
//}}}

//{{{Holdings List APIs
/**@name Holdings List APIs */
/**@{*/

// {{{ getHoldingsList (Retrieve Holdings list)
/**
 * @brief Retrieve Holdings list - download brief descriptions of holdings 
 * for the bib
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      GET /almaws/v1/bibs/{mms_id}/holdings
 *	
 * @param string $mms_id MMS ID of Alma Bib to gather holdings from
 * @return DomDocument Holdings List object
 */
	function getHoldingsList($mms_id) {
		$ret = $this->get('/almaws/v1/bibs/{mms_id}/holdings', 
			array('mms_id' => $mms_id),
			array()
			);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

/**@}*/
//}}}

//{{{Holding APIs
/**@name Holding APIs */
/**@{*/

// {{{ getHolding (Retrieve Holdings Record)
/**
 * @brief Retrieve Holdings Record - retrieve holdings record from Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      GET /almaws/v1/bibs/{mms_id}/holdings/{holding_id}
 *
 * @param string $mms_id MMS ID of Alma Bib 
 * @param string $holding_id Holdings ID of Alma Holding
 * @return DomDocument Holding object
 */
	function getHolding($mms_id,$holding_id) {
		$ret = $this->get('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}', 
			array(
			'mms_id' => $mms_id,
			'holding_id' => $holding_id
			),
			array()
			);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

// {{{ postHolding (Create holding record)
/**
 * @brief Create holding record - add a new holdings record to a bib
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      POST /almaws/v1/bibs/{mms_id}/holdings
 *
 * @param string $mms_id MMS ID of bib record
 * @param DomDocument $holding Holding object to add to Alma as new record
 * @return DomDocument Bib object as it now appears in Alma https://developers.exlibrisgroup.com/alma/apis/xsd/rest_bib.xsd?tags=GET
 */
	function postHolding($mms_id,$holding) {
		$ret = $this->post('/almaws/v1/bibs/{mms_id}/holdings',
			array('mms_id' => $mms_id),
			array(),
			$holding
			);
		return $ret;
	}
// }}}

// {{{ putHolding (Update Holdings Record)
/**
 * @brief Update Holdings Record - replace the holdings record in Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      PUT /almaws/v1/bibs/{mms_id}/holdings/{holding_id}
 *
 * @param string $mms_id MMS ID of Bib
 * @param string $holding_id Holding ID of holding to replace
 * @param DomDocument $holding Holding object to add to Alma as new record
 * @return DomDocument Bib object as it now appears in Alma https://developers.exlibrisgroup.com/alma/apis/xsd/rest_bib.xsd?tags=GET
 */
	function putHolding($mms_id,$holding_id,$holding) {
		$ret = $this->put('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}',
			array('mms_id' => $mms_id, 'holding_id' => $holding_id),
			array(),
			$holding
			);
	}
// }}}

// {{{ deleteHolding (Delete Holdings Record)
/**
 * @brief Delete Holdings Record - delete the holdings record from Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      DELETE /almaws/v1/bibs/{mms_id}/holdings/{holding_id}
 *
 * @param string $mms_id MMS ID of Alma Bib record 
 * @param string $holding_id Holding ID of Holding record to delete from Alma
 * @param string $override Optional. Default=false
 */
	function deleteHolding($mms_id,$holding_id,$override='false') {
		$ret = $this->delete('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}', 
			array('mms_id' => $mms_id),
			array('holding_id' => $holding_id),
			array('override' => $override)
		);
		$this->checkForErrorMessage($ret);
	}
// }}}

/**@}*/
//}}}

//{{{Item List APIs
/**@name Item List APIs */
/**@{*/

// {{{ getItemList (Retrieve Items list)
/**
 * @brief Retrieve Items list - retrieve the items list from a holding or bib from Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      GET /almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items
 *
 * @param string $mms_id MMS ID of Alma bib
 * @param string $holding_id MMS ID of Alma holding
 * @param string $limit Max number of items to retrieve
 * @param string $offset Offset of the results returned
 * @return DomDocument Holdings List object
 */
	function getItemList($mms_id,$holding_id,$limit,$offset) {
		$ret = $this->get('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items', array('mms_id' => $mms_id, 'holding_id' => $holding_id),
			array('limit' => $limit, 'offset' => $offset)
		);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

/**@}*/
//}}}

//{{{Item APIs
/**@name Item APIs */
/**@{*/

// {{{ getItem (Retrieve Item and print label information)
/**
 * @brief Retrieve Item and print label information
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      GET /almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items/{item_pid}
 *
 * @param string $mms_id MMS ID of Alma Bib
 * @param string $holding_id Holding ID of Alma Holding
 * @param string $item_pid Item ID of Alma Holding
 */
	function getItem($mms_id,$holding_id,$item_pid) {
		$ret = $this->get('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items/{item_pid}', array(
			'mms_id' => $mms_id,
			'holding_id' => $holding_id,
			'item_pid' => $item_pid
			),
			array()
			);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

// {{{ getItemBC (Retrieve Item and print label information (by barcode))
/**
 * @brief Retrieve Item and print label information (by barcode))
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      GET /almaws/v1/items?item_barcode={item_barcode}
 *
 * @param string $barcode Barcode of Alma item
 */
	function getItemBC($barcode) {
		$ret = $this->get('/almaws/v1/items',
			array(),
			array(
				'item_barcode' => $barcode,
			)
		);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

// {{{ postItem (Create Item)
/**
 * @brief Create Item - add a new item to a holding in Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      POST /almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items
 *
 * @param string $mms_id MMS ID of Bib record
 * @param string $holding_id Holding ID of Holding record
 * @param DomDocument object $item Item object to add to Alma as new record
 * @return DomDocument Bib object as it now appears in Alma https://developers.exlibrisgroup.com/alma/apis/xsd/rest_bib.xsd?tags=GET
 */
	function postItem($mms_id,$holding_id,$item) {
		$ret = $this->post('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items',
			array('mms_id' => $mms_id, 'holding_id' => $holding_id),
			array(),
			$item
			);
		return $ret;
	}
// }}}

// {{{ putItem (Update Item information)
/**
 * @brief Update Item information - replace item record in Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      PUT /almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items/{item_pid}
 *
 * @param string $mms_id MMS ID of Bib record
 * @param string $holding_id Holding ID of Holding record
 * @param string $item_pid Item ID of Item record
 * @param DomDocument $item Item object to update record with in Alma
 * @return DomDocument Bib object as it now appears in Alma https://developers.exlibrisgroup.com/alma/apis/xsd/rest_bib.xsd?tags=GET
 */
	function putItem($mms_id,$holding_id,$item_pid,$item) {
		$ret = $this->put('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items/{item_pid}',
			array('mms_id' => $mms_id, 'holding_id' => $holding_id, 'item_pid' => $item_pid),
			array(),
			$item
			);
		return $ret;
	}
// }}}

// {{{ deleteItem (Withdraw Item)
/**
 * @brief Withdraw Item - delete an item record from Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/bibs#Resources)
 *
 *      DELETE /almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items/{item_pid}
 *
 * @param string $mms_id MMS ID of Bib record
 * @param string $holding_id Holding ID of holding record
 * @param string $item_pid Item ID of item record
 * @param string $override Override warnings? (false, true)
 * @param string $holding How to handle holdings with inventory? (retain, delete or suppress)
*/
	function deleteItem($mms_id,$holding_id,$item_pid,$override = "false",
		$holdings = "retain") {
		$ret = $this->delete('/almaws/v1/bibs/{mms_id}/holdings/{holding_id}/items/{item_pid}', array(
				'mms_id' => $mms_id,
				'holding_id' => $holding_id,
				'item_pid' => $item_pid
			), array(
				'override' => $override,
				'holdings' => $holdings
			)
		);
		$this->checkForErrorMessage($ret);
	}
// }}}

/**@}*/
//}}}

//{{{Electronic APIs
/**@name Electronic APIs */
/**@{*/

// {{{ getElectronicPortfolio (Retrieve Portfolio)
/**
 * @brief Retrieve Portfolio - retrieve a portfolio record from Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/electronic#Resources)
 *
 *      GET /almaws/v1/electronic/e-collections/{collection_id}/e-services/{service_id}/portfolios/{portfolio_id}
 * @param string $collection_id ID of collection
 * @param string $service_id ID of service
 * @param string $portfolio_id ID of portfolio
 * @return DomDocument Electronic Portfolio object
*/
	function getElectronicPortfolio($collection_id,$service_id,$portfolio_id) {
		$ret = $this->get('/almaws/v1/electronic/e-collections/{collection_id}/e-services/{service_id}/portfolios/{portfolio_id}',
			array('collection_id' => $collection_id, 'service_id' => $service_id, 'portfolio_id' => $portfolio_id),
			array()
		);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

// {{{ postElectronicPortfolio (Create Electronic Portfolio)
/**
 * @brief Create Electronic Portfolio - add a new portfolio to Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/electronic#Resources)
 *
 *      POST /almaws/v1/electronic/e-collections/{collection_id}/e-services/{service_id}/portfolios/
 *
 * @param string $collection_id ID of collection
 * @param string $collection_id ID of service
 * @param string $portfolio_id ID of portfolio
 * @return DomDocument Electronic Portfolio object as it appears in Alma
*/
	function postElectronicPortfolio($collection_id,$service_id,$portfolio_id) {
		$ret = $this->post('/almaws/v1/electronic/e-collections/{collection_id}/e-services/{service_id}/portfolios',
			array('collection_id' => $collection_id, 'service_id' => $service_id),
			array(),
			$portfolio
			);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

// {{{ deleteElectronicPortfolio (Delete Electronic Portfolio)
/**
 * @brief Delete Electronic Portfolio - delete portfolio from Alma
 *
* Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/electronic#Resources)
 *		
 *      DELETE /almaws/v1/electronic/e-collections/{collection_id}/e-services/{service_id}/portfolios/{portfolio_id}
 *
 * @param string $collection_id ID of collection
 * @param string $service_id ID of service
 * @param string $portfolio_id ID of portfolio
 */
	function deleteElectronicPortfolio($collection_id,$service_id,$portfolio_id) {
		$ret = $this->delete('/almaws/v1/electronic/e-collections/{collection_id}/e-services/{service_id}/portfolios/{portfolio_id}',
		array('collection_id' => $collection_id, 'service_id' => $service_id, 'portfolio_id' => $portfolio_id),
		array()
		);
		$this->checkForErrorMessage($ret);
	}
// }}}

// {{{ getElectronicPortfolios (Retrieve Portfolios)
/**
 * @brief Retrieve Portfolios - retrieve a list of portfolios from Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/electronic#Resources)
 *
 *      GET /almaws/v1/electronic/e-collections/{collection_id}/e-services/{service_id}/portfolios
 *
 * @param string $collection_id ID of collection
 * @param string $service_id ID of service
 * @param string $limit Max number of portfolios to retrieve
 * @param string $offset Offset of the results
 * @return DomDocument Retrieve Portfolios object
*/
	function getElectronicPortfolios($collection_id, $service_id, $limit, $offset) {
		$ret = $this->get('/almaws/v1/electronic/e-collections/{collection_id}/e-services/{service_id}/portfolios',
			array('collection_id' => $collection_id, 'service_id' => $service_id),
			array('limit' => $limit, $offset = $offset)
		);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

// {{{ getElectronicServices (Retrieve Electronic Services)
/**
 * @brief Retrieve Electronic Services - retrieve a list of services from
 * a collection in Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/electronic#Resources)
 *
 *      GET /almaws/v1/electronic/e-collections/{collection_id}/e-services
 *
 * @param string $collection_id ID of collection
 * @param string $limit Max number of portfolios to retrieve
 * @param string $offset Offset of the results
 * @return DomDocument Services object
*/
	function getElectronicServices($collection_id) {
		$ret = $this->get('/almaws/v1/electronic/e-collections/{collection_id}/e-services',
			array('collection_id' => $collection_id),
			array()
		);
		$this->checkForErrorMessage($ret);
		return $ret;
	}
// }}}

/**@}*/
//}}}

//{{{Set APIs
/**@name Set APIs */
/**@{*/

// {{{ getSet (Retrieve a Set)
/**
 * @brief Retrieve a Set - retrieve a Set from Alma
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/conf#Resources)
 *
 *      GET /almaws/v1/conf/sets/{set_id}
 *
 * @param string $set_id ID of the set to retrieve
 * @return DomDocument Set object
*/
	function getSet($set_id) {
		$ret = $this->get('/almaws/v1/conf/sets/{set_id}',
			array('set_id' => $set_id),
			array()
		);
		$this->checkForErrorMessage($ret);
		return $ret;	
	}
// }}}}

/*
	function postSetManageMembers($set_id,$id_type,$op) {
	}
*/

// {{{ createSetFromImport (Create a Set)
/**
 * @brief Create a Set from an import job
 *
 * Makes a call to the API:
 * [(API docs)](https://developers.exlibrisgroup.com/alma/apis/conf#Resources)
 *
 *      POST /almaws/v1/conf/sets
 *
 * @param string $job_instance_id ID of the import job
 * @param string $population ...
 * @return DomDocument Set object
*/
	function createSetFromImport($job_instance_id, $population) {
		# create blank set

		$body = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<set>
  <name>Grima set from ' . $job_instance_id . '</name>
  <description>members of ' . $job_instance_id . '</description>
  <type desc="Itemized">ITEMIZED</type>
  <content desc="All Titles">BIB_MMS</content>
  <private desc="No">false</private>
</set>';

		$ret = $this->post('/almaws/v1/conf/sets', array(), array('job_instance_id' => $job_instance_id, 'population' => $population),$body);

	}
// }}}

	function getSetMembers($set_id,$limit = 10,$offset = 0) {
		return $this->get('/almaws/v1/conf/sets/{set_id}/members',
			array('set_id' => $set_id),
			array('limit' => $limit, 'offset' => $offset)
		);
	}

/**@}*/
//}}}

//{{{Analytics APIs
/**@name Analytics APIs */
/**@{*/

	function getAnalytics($path,$filter,$limit=25,$token=null) {
		return $this->get('/almaws/v1/analytics/reports',
			array(),
			array('path' => urlencode($path), 'filter' => urlencode($filter), 
				'limit' => $limit, 'token' => $token)
		);
	}

/**@}*/
//}}}

}

/* }}} */

// {{{ class GrimaTask
/** class GrimaTask */
abstract class GrimaTask implements ArrayAccess {

	public $error = false;
	public $args = array();
	public $el_override = array();

	public $auto_args = array();

	protected $messages = array();

	function offsetExists($offset) {
		return isset($this->args[$offset]);
	}

	function offsetGet($offset) {
		return $this->args[$offset];
	}

	function offsetSet($offset,$value) {
		$this->args[$offset] = $value;
	}

	function offsetUnset($offset) {
		unset($this->args[$offset]);
	}
	
	function __construct() {
		$base = basename($_SERVER['PHP_SELF'],'.php');
		if (file_exists("$base.xml") and (!isset($this->formxml))) {
			$this->formxml = file_get_contents("$base.xml");
		}
		if (isset($this->formxml)) {
			$this->form = new GrimaForm();
			$this->form->fromXML($this->formxml);
		}
	}

    function print_form() {
		if (isset($this->form)) { # should everything have this
        	print <<<TOP
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
      .has-error input { background-color : #ffe4e1; }
}
    </style>
  </head>
  <body>
    <div class="jumbotron">
      <div class="container">
        <div class="col-md-4 col-md-offset-4">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h2 class="panel-title">{$this->form->title}</h2>
            </div>
            <div class="panel-body">
TOP;
	print $this->form->toHTML();
	print "<div>";
	foreach ($this->messages as $message) {
		print $message->toHTML();
	}
	print "<div>";
print <<<BOT

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
BOT;

		}
		exit;
    }


	function print_success() {
		if (php_sapi_name() != "cli") {
			// @todo: simplified cli print out
		}
		if ($this->output) {
			print $this->output;
		} else {
			$this->print_form();
		}
	}

	function print_failure() {
		foreach ($this->messages as $message) {
			#print $message->toHTML();
			$this->print_form();
		}
	}

	function check_login() {
		global $grima;
		if (isset($grima->apikey) and isset($grima->server) and
			($grima->apikey) and ($grima->server)) {
			return true;
		} else {
			do_redirect('login.php?redirect_url=' . urlencode($_SERVER['PHP_SELF']));
		}
	}

	public function addMessage($type,$message) {
		$this->messages[] = new GrimaTaskMessage($type,$message);
	}

	public function run() {
		$this->check_login(); # if not logged in, print login form
		$this->error = false;
		$this->get_input();
		if ($this->check_input()) {
			try {
        		$this->do_task();
			} catch (Exception $e) {
				$this->addMessage('error',$e->getMessage());
				$this->error = true;
			}
		} else {
			$this->form->loadValues($this);
			$this->print_form(); 
			exit;
		}
		if ($this->error) { # maybe better
			$this->print_failure(); 
		} else {
			$this->print_success(); 
		}
	}

	public static function RunIt() {
		$task = new static();
		$task->run();
	}

	function get_input() {
		if (isset($this->form)) {
        	if (php_sapi_name() == "cli") { # command line
				/*
				if ($options = getopt(implode(array_keys($param)))) {
					foreach ($param as $k => $v) {
						$this->args[$v] = $options[$k[0]];
					}
					if (!$this->check_input()) {
						$this->usage(); exit;
					}
				} else {
					$this->usage(); exit;
				}
				*/ 
			} else { # web
				foreach ($this->form->fields as $field) {
					#print "NAME: " . $field->name . "<br />";
					if (isset($_REQUEST[$field->name])) {
						$this[$field->name] = $_REQUEST[$field->name];
						/* sanitize */
					}
				}	
			}
				
		} else {
			$this->get_input_param($this->auto_args);
		}
	}

	function check_input() {
		if (isset($this->form)) {
			$input_good = true; 
			foreach ($this->form->fields as $field) {
				if ($field->required) {
	                if (!isset($this->args[$field->name]) or 
							!($this->args[$field->name])) {
						$field->error_condition = "error";
						$field->error_message = "Field is required\n";
						$input_good = false;
					}
				}
			}
			return $input_good;
		} else {

			foreach ($this->auto_args as $k => $v) {
				if (preg_match('/[^:]:$/',$k)) {
					if (!isset($this->args[$v]) or !($this->args[$v])) {
						return false;
					}
				}
			}
			return true;
		}
	}

	function get_input_param($param) {
        if (php_sapi_name() == "cli") { # command line
			if ($options = getopt(implode(array_keys($param)))) {
				foreach ($param as $k => $v) {
					$this->args[$v] = $options[$k[0]];
				}
				if (!$this->check_input()) {
					$this->usage(); exit;
				}
			} else {
				$this->usage(); exit;
			}
		} else { # web
			foreach ($param as $k => $v) {
				if (isset($_REQUEST[$v])) {
                	$this->args[$v] = $_REQUEST[$v];
				}
			}
            if (!$this->check_input()) {
                $this->print_form(); exit;
            }
		}
	}

	abstract function do_task();

	function usage() { # XXX rewrite for grima form
		global $argv;
		print "Usage: php ${argv[0]} ";
		foreach ($this->auto_args as $k => $v) {
			if (preg_match('/^(.):$/',$k,$m)) {
				print "-${m[1]} <$v> ";
			} else {
				if (preg_match('/^(.)::$/',$k,$m)) {
					print "[ -${m[1]} <$v> ] ";
				} else {
					if (preg_match('/^.$/',$k)) {
						print "[ -$k ] ";
					}
				}
			}
		}
		print "\n";
		exit;
	}

}

$grima = new Grima();

/** }}} */

// {{{ class GrimaTaskMessage
/** class GrimaTaskMessage */
class GrimaTaskMessage {
	public $type; 
		/* bootstrap type: debug, info, success, warning, error */
	public $message;

	function __construct($type,$message) {
		$this->type = $type;
		$this->message = $message;
	}

	function toHTML() {
		$translation = array(
			'success' => 'alert alert-success',
			'info' => 'alert alert-info',
			'warning' => 'alert alert-warning',
			'error' => 'alert alert-danger'
		);
		$class = $translation[$this->type];
		return "<div class=\"$class\">{$this->message}</div>";
	}
}

/* }}} */

// class {{{ GrimaForm
/** class GrimaForm */
class GrimaForm {
	public $fields = array();
	public $title;
	protected $action;

// {{{ loadValues
/**
 * @brief load values into the form
 *
 * @param Object @obj array-accessible
 */
	function loadValues($obj) {
		foreach ($this->fields as $field) {
			if (isset($obj[$field->name])) {
				$field->value = $obj[$field->name];
			}
		}
	}
// }}}

// {{{ fromXML
/**
 * @brief interpret XML to determine form fields and behavior
 * @param string $xml XML document
 */
	function fromXML($xml) {
		$doc = new DomDocument();
		$doc->loadXML($xml);
		$xpath = new DomXpath($doc);
		$this->title = $xpath->query('//Title')[0]->nodeValue;
		$this->action = basename($_SERVER['PHP_SELF']); # allow set?

		$nodes = $xpath->query('//Field');
		foreach ($nodes as $node) {
			$this->fields[$node->getAttribute('name')] = new GrimaFormField($node);
		}
	}
// }}}

// {{{ toHTML - convert to HTML for display as a form
/**
 * @brief convert to HTML for display as a form
 */
	function toHTML() {
		$html = "<form method=\"post\" action=\"{$this->action}\">";
		foreach ($this->fields as $field) {
			if ($field->visible) {
				$html .= $field->toHTML();
			}
		}
$html .= "             <input class=\"btn btn-primary active\" type=\"submit\" value=\"submit\" />
              </form>";

		return $html;
	}
// }}}

}

/** }}} */

// {{{ class GrimaFormField
/** class GrimaFormField */
class GrimaFormField {

	public $value;

	public $name;
	public $label;
	public $placeholder;
	public $required;
	public $visible;
	public $rows;
	protected $autocomplete;
	protected $highlight;
	public $error_condition = ""; /* can be warning or error */
	public $error_message = "";

// {{{ booly - is it true or false
/**
 * return boolean meaning of common terms ("true","on","1","yes")
 *
 * @param string $str term to interpret
 * @param $default if it is not found
 * @return boolean true or false
 */
	function booly($str, $default = 'undefined') {
		switch(strtolower($str)) {
			case 'true':
			case 't':
			case 'on':
			case 'yes':
			case '1':
				return true;
			case 'false':
			case 'f':
			case 'off':
			case 'no':
			case '0':
				return false;
			default:
				return $default;
		}
	}
// }}}

// {{{ __construct 
/**
 * @brief create a new GrimaFormField 
 *
 * @param DomNode $field with attributes for all properties
 */
	function __construct($field) {
		$this->name = $field->getAttribute('name');
		$this->label = $field->getAttribute('label');
		$this->placeholder = $field->getAttribute('placeholder');
		$this->rows = $field->getAttribute('rows');
		$this->required = $this->booly($field->getAttribute('required'),true);
		$this->autocomplete = $this->booly($field->getAttribute('autocomplete'),false);
		$this->visible = $this->booly($field->getAttribute('visible'),true);
	}
// }}}

// {{{ toHTML - convert to HTML for display as a form
/**
 * @brief convert to HTML for display in a form
 */
	function toHTML() {
		global $_SERVER;
		$submitted = ($_SERVER['REQUEST_METHOD'] == 'POST');
		
		if ($this->rows > 0) {
			return "<textarea rows=\"{$this->rows}\" cols=\"20\" class=\"form-control\" name=\"{$this->name}\" id=\"{$this->name}\" placeholder=\"{$this->placeholder}\" /></textarea>";
		} 
		$auto = ($this->autocomplete)?"on":"off";
		if ($submitted and $this->error_condition) {
			$error_class = " has-{$this->error_condition}";
			$help_block = "<span class=\"help-block\">{$this->error_message}</span>";
		} else {
			$error_class = "";
			$help_block = "";
		}
		return "
                <div class=\"form-group$error_class\">
                  <label for=\"{$this->name}\">{$this->label}</label>
                  <input class=\"form-control$error_class\" name=\"{$this->name}\" id=\"{$this->name}\" size=\"20\" placeholder=\"{$this->placeholder}\" autocomplete=\"$auto\" value=\"{$this->value}\" />
					$help_block
				</div>
";
	}
// }}}

}

/** }}} */

// {{{ class AlmaObject
/** class AlmaObject 
*/
class AlmaObject implements ArrayAccess {
	public $el_access = array();
	public $xml;

	function offsetExists($offset) {
		if (isset($this->el_override)) {
			return array_key_exists($offset, $this->el_override);
		}
		return array_key_exists($offset, $this->el_access);
	}

	function offsetGet($offset) {
		if ((isset($this->el_override)) and 
				(isset($this->el_override[$offset]))) {
			return $this->el_override[$offset];
		}
		$xpath = new DomXpath($this->xml);
		$node = $xpath->query($this->el_address[$offset]);
		if (count($node) >= 1) {
			return $node[0]->nodeValue;
		} 
		return null;
	}

	function offsetSet($offset, $value) {
		$xpath = new DomXpath($this->xml);
		$node = $xpath->query($this->el_address[$offset]);
		$node[0]->nodeValue = $value;
	}

	function offsetUnset($offset) {
		$xpath = new DomXpath($this->xml);
		$node = $xpath->query($this->el_address[$offset]);
		$node[0]->nodeValue = null;
	}

}

/* }}} */

// {{{ class Bib 
/** 
 class Bib
*/
class Bib extends AlmaObject {
	public $holdingsList; # HoldingsList object

	/* public $networknums = array(); */

	protected $el_address = array(
		'mms_id' => '//mms_id',
		'record_format' => '//record_format',
		'title' => '//title',
		'author' => '//author',
		'place_of_publication' => '//place_of_publication',
		'publisher_const' => '//publisher_const',
		'publisher' => '//publisher_const'
	);

	# override because these go multiple places
	function offsetSet($offset,$value) {
		parent::offsetSet($offset,$value);
		if ($offset == 'author') {
			$this->replaceOrAddSubfield('100','a',$value);
		}
		if ($offset == 'title') {
			$this->replaceOrAddSubfield('245','a',$value);
		}
		if ($offset == 'publisher_const') {
			$this->replaceOrAddSubfield('264','b',$value);
		}
		if ($offset == 'place_of_publication') {
			$this->replaceOrAddSubfield('264','a',$value);
		}
	}

// {{{ __construct 
/**
 * @brief create new blank bib from template
 */
	function __construct() {
		$this->xml = new DomDocument();
		$this->xml->loadXML(file_get_contents("templates/bib.xml"));
	}
// }}}

// {{{ loadFromAlma (get) - gets Bib from Alma
/**
 * @brief populates the bib with a record from Alma
 *
 * @param string $mms_id MMS ID of record to load from Alma
 */
	function loadFromAlma($mms_id) {
		global $grima;
		$this->xml = $grima->getBib($mms_id);
	}
// }}}

// {{{ addToAlma (post) - adds the Bib to Alma
/**
 * @brief adds record as a new record to Alma, updates Bib with
 *     current Alma version
 */
	function addToAlma() {
		global $grima;
		$this->xml = $grima->postBib($this->xml);
	}
// }}}

// {{{ updateAlma (put) - replaces the Bib in Alma
/**
 * @brief replaces the Bib in Alma
 */
	function updateAlma() {
		global $grima;
		$this->xml = $grima->putBib($this['mms_id'],$this->xml);
	}
// }}}

// {{{ deleteFromAlma (delete) - deletes the Bib from Alma
/**
 * @brief deletes the Bib from Alma
 */
	function deleteFromAlma() {
		global $grima;
		$grima->deleteBib($this['mms_id']);
	}
// }}}

// {{{ getHoldingsList
/**
 * @brief populate holdingsList property with info from Alma
 */
	function getHoldingsList() {
		$this->holdingsList = new HoldingsList($this['mms_id']);
	}
// }}}

/*
	function recycle() { # XXX 
		global $grima;
		$recycle_bin = new Set();
		$recycle_bin->loadFromAlma("9543638640002636");
		$recycle_bin->addMember($this['mms_id']);
		# add to recycle bin
	}
*/

// {{{ get_title_proper 
/** @brief a tidy title proper
 @return string 245$a with ISBD punctuation removed
 */
	function get_title_proper() {
		$xpath = new DomXpath($this->xml);
		$title = $xpath->query("//record/datafield[@tag='245']/subfield[@code='a']");
		return preg_replace("/[ \/=:;\.]*$/","",$title[0]->nodeValue);
	}
// }}}

	/*
	function get_networkNumbers() {
		$xpath = new DomXpath($this->xml);
		$netNums = $xpath->query("//bib/network_numbers/network_number");
		$ret = array();
		for ($j=0;$j<$netNums->length;$j++) {
			$ret[] = $netNums[$j]->nodeValue;
		}
		return $ret;
	}
	*/

	function appendField($tag,$ind1,$ind2,$subfields) {
		$frag = "<datafield ind1=\"$ind1\" ind2=\"$ind2\" tag=\"$tag\">";
		foreach ($subfields as $k => $v) {
			$frag .= "<subfield code=\"$k\">$v</subfield>";
		}
		$frag .= "</datafield>";
		$xpath = new DomXpath($this->xml);
		$record = $xpath->query("//record");
		appendInnerXML($record[0],$frag);
	}

	function replaceOrAddSubfield($tag,$code,$value) {
		# very shady but sometimes needed
		$xpath = new DomXpath($this->xml);
		$fields = $xpath->query("//record/datafield[@tag='$tag']");
		if (sizeof($fields) == 0) {
			$this->appendField($tag,' ',' ',array($code => $value));
		} else {
			$done = false;
			foreach	 ($fields[0]->childNodes as $subfield) {
				if($subfield->nodeType !== 1) { 
        			continue;
    			}
				if ($subfield->getAttribute("code") == $code) {
					$subfield->nodeValue = $value;
					$done = true;
					break;
				}
			}
			if (!$done) {
				$subfield = $this->xml->createElement("subfield");
				$subfield->setAttribute("code",$code);
				$subfield->appendChild($this->xml->createTextNode($value));
				$fields[0]->appendChild($subfield);
			}
		}
	}

	function deleteField($tag) {
		$xpath = new DomXpath($this->xml);
		$fields = $xpath->query("//record/datafield[@tag='$tag']");
		foreach( $fields as $field ) {
			$field->parentNode->removeChild( $field );
		}
	}

}

/** }}} */

// {{{ class HoldingsList
/** class HoldingsList */
class HoldingsList extends AlmaObject {
	public $el_address = array(
		'mms_id' => '//mms_id',
		'title' => '//title',
		'author' => '//author',
	);
	public $xml;
	public $holdings = array(); 

	function __construct($mms_id = null) {
		if (!is_null($mms_id)) {
			$this->loadFromAlma($mms_id);
		}
	}

	function loadFromAlma($mms_id) {
		global $grima;
		$this->xml = $grima->getHoldingsList($mms_id);
		$xpath = new DomXpath($this->xml);
		$hs = $xpath->query('//holding');
		$this->holdings = array(); # clear
		foreach ($hs as $h) {
			$this->holdings[] = new HoldingsListEntry($h,$mms_id);
		}
	}
}

/** }}} */

// {{{ class HoldingsListEntry
/** class HoldingsListEntry */
class HoldingsListEntry extends AlmaObject {
	protected $el_address = array(
		'holding_id' => '//holding_id',
		'call_number' => '//holding/call_number',
		'library_code' => '//holding/library',
		'library' => '//holding/library/@desc',
		'location_code' => '//holding/location',
		'location' => '//holding/location/@desc'
	);
	public $xml;

	function __construct($node,$mms_id) { 
		$this->xml = new DomDocument();
		$this->xml->appendChild($this->xml->importNode($node,true));
		$this->el_override['mms_id'] = $mms_id;
	}

	function getItemList($limit = -1) {
		global $grima;
		$this->itemList = new ItemList($this['mms_id'], $this['holding_id'], $limit);
	}
}

/** }}} */

// {{{ class ItemList
/** class ItemList */
class ItemList extends AlmaObject {
	public $items = array(); 

	function __construct($mms_id,$holding_id,$limit =-1) {

		global $grima;
		$curr_offset = 0;
		$req_limit = ($limit == -1)?100:$limit;

		do {
			if ($curr_offset > 0) {
				if (($curr_offset+1)*100 > $limit) {
					$req_limit = $limit - $curr_offset*100;
				} else {
					$req_limit = 100;
				}
		 	}
			$xml = $grima->getItemList($mms_id,$holding_id,$req_limit,$curr_offset*100);
			$xpath = new DomXpath($xml);
			$is = $xpath->query('//item');
			foreach ($is as $i) {
				$new_item = new Item();
				$new_item->loadFromItemListNode($i);
				$this->items[] = $new_item;
			}
			$xpath = new DomXPath($xml);
			if (!$curr_offset) {
				$length = $xpath->query('//items/@total_record_count')[0]->nodeValue;
				if ($limit == -1) { $limit = $length; }
			}
			$curr_offset++;
			
		} while (($curr_offset*100 < $length) and ($curr_offset*100 < $limit));

	}

}

/** }}} */

// {{{ class Holding
/** class Holding */
class Holding extends AlmaObject {
	public $itemList; # object
	public $xml;

	function offsetSet($offset,$value) {
		if ($offset == "mms_id") {
			$this->el_override['mms_id'] = $value;
		} else {
			parent::offsetSet($offset,$value);
		}
	}

// {{{ $el_address
	public $el_address = array(
		'holding_id' => '//holding_id',
		'inst_code' => '',
		'library_code' => '//holding/record/datafield[@tag=852]/subfield[@code=b]',
		'location_code' => '//holding/record/datafield[@tag=852]/subfield[@code=c'
	);
// }}}

// {{{ __construct - creates a blank holding
/**
 * @brief creates a blank holding record
 */
	function __construct() {
		$this->xml = new DomDocument();
		$this->xml->loadXML(file_get_contents("templates/bib.xml"));
	}
// }}}
	
// {{{ loadFromAlma (get) - populates record from Alma
/**
 * @brief populates the record from Alma
 *
 * @param $mms_id MMS ID of bib record
 * @param $holding_id Holding ID of holding
*/
	function loadFromAlma($mms_id,$holding_id) {
		global $grima;
		$this->xml = $grima->getHolding($mms_id,$holding_id);
		$this['mms_id'] = $mms_id;
	}
// }}}

// {{{ loadFromAlmaX (get) - populates record from Alma using holding_id
/**
 * @brief populates the record from Alma - only requires holding_id
 * XXX: Maybe don't use this if you eventually need the MMS ID also?
 *
 * @param $holding_id Holding ID of holding
*/
	function loadFromAlmaX($holding_id) { 
		global $grima;
		$this->xml = $grima->getHolding('X',$holding_id);
	}
// }}}

// {{{ addToAlmaBib (post) - adds new holding record to specified bib
/**
 * @brief adds a new holding record to the specified bib
 * 
 * @param string $mms_id bib record to add the holdings record 
 */
	function addToAlmaBib($mms_id) {
		global $grima;
		$this->xml = $grima->postHolding($mms_id,$this->xml);
		return $ret;
	}
// }}}

// {{{ updateAlma (put) - update record in Alma
/**
 * @brief update holding record in Alma
 */
	function updateAlma() {
		global $grima;
		$grima->putHolding($this['mms_id'],$this['holding_id'],$this->xml);
	}
// }}}

// {{{ deleteFromAlma (delete) - delete record in Alma
/**
 * @brief delete the holding record from Alma
 */
	function deleteFromAlma() {
		global $grima;
		$grima->deleteHolding();
	}
// }}}

	function appendField($tag,$ind1,$ind2,$subfields) {
		$frag = "<datafield ind1=\"$ind1\" ind2=\"$ind2\" tag=\"$tag\">";
		foreach ($subfields as $k => $v) {
			$frag .= "<subfield code=\"$k\">$v</subfield>";
		}
		$frag .= "</datafield>";
		$xpath = new DomXpath($this->xml);
		$record = $xpath->query("//record");
		appendInnerXML($record[0],$frag);
	}

	function setCallNumber($h,$i,$ind1) {
		$xpath = new DomXpath($this->xml);
		$xpath->query("//record/datafield[@tag='852']")->item(0)->setAttribute("ind1",$ind1);

		$field852 = $xpath->query("//record/datafield[@tag='852']")->item(0);
		$subfieldHs = $xpath->query("subfield[@code='h']",$field852);
		foreach ($subfieldHs as $subfieldH) {
			$subfieldH->delete();
		}
		$subfieldIs = $xpath->query("subfield[@code='i']",$field852);
		foreach ($subfieldIs as $subfieldI) {
			$subfieldI->delete();
		}

		$frag = "<subfield code=\"h\">$h</subfield><subfield code=\"i\">$i</i>";
		appendInnerXML($field852,$frag);
	}

// {{{ moveToBib - moves a holding from one bib to another
/**
 * @brief moves the holding from one bib to another -- only for empty holdings!
 */
	function moveToBib($mms_id) {
		$this->deleteFromAlma();
		$this->addToAlmaBib($mms_id);
	}
// }}}

// {{{ getItemList - populates itemList property from Alma
/** 
 * @brief populates itemList property from Alma
 */
	function getItemList() {
		global $grima;
		$this->itemList = new ItemList($this['holding_id']);
	}
// }}}

}

/** }}} */

// {{{ class Item
/** class Item */
class Item extends AlmaObject {

	public $el_address = array(
		'item_pid' => '//pid',
		'barcode' => '//barcode',
		'creation_date' => '//creation_date',
		'modification_date' => '//modification_date',
		'base_status' => '//base_status',
		'physical_material_type_code' => '//physical_material_type',
		'physical_material_type' => '//physical_material_type/@desc',
		'policy' => '//policy',
		'item_policy' => '//policy',
		'provenance' => '//provenance',
		'po_line' => '//po_line',
		'is_magnetic' => '//is_magnetic',
		'arrival_date' => '//arrival_date',
		'year_of_issue' => '//year_of_issue',
		'enumeration_a' => '//enumeration_a',
		'enumeration_b' => '//enumeration_b',
		'chronology_i' => '//chronology_i',
		'chronology_j' => '//chronology_j',
		'description' => '//description',
		'in_temp_location' => '//in_temp_location',
 		'mms_id' => '//mms_id',
		'holding_id' => '//holding_id',
		'title' => '//title',
		'location' => '//location/@desc',
		'call_number' => '//call_number',
	);

// {{{ __construct
/**
 * @brief creates a new blank item record
 */
	function __construct() {
		$this->xml = new DomDocument();
		$this->xml->loadXML(file_get_contents("templates/item.xml"));
	}
// }}}

// {{{ loadFromAlma (get)
/**
 * @brief populates item record from Alma
 *
 * @param string $mms_id MMS ID of bib record
 * @param string $holding_id Holding ID of holding record
 * @param string $item_pid Item ID of item record
 */
	function loadFromAlma($mms_id,$holding_id,$item_pid) {
		global $grima;
		$this->xml = $grima->getItem($mms_id,$holding_id,$item_pid);
	}
// }}}

// {{{ loadFromAlmaX (get)
/**
 * @brief populates item record from Alma, only needs item_pid
 * @param string $item_pid item ID of record to load from Alma
 */
	function loadFromAlmaX($item_pid) {
		global $grima;
		$this->xml = $grima->getItem('X','X',$item_pid);
	}
// }}}

// {{{ loadFromAlmaBarcode (get)
/**
 * @brief populates item record from Alma, using barcode
 * @param string $barcode barcode of record to load from Alma
 */
	function loadFromAlmaBarcode($barcode) {
		global $grima;
		$this->xml = $grima->getItemBC($barcode);
	}
// }}}

// {{{ loadFromAlmaBCorX (get)
/**
 * @brief populates item record from Alma using either identifier
 *
 * @param string $id identifer of record to load from Alma (can be barcode 
 *   or item ID)
 */
	function loadFromAlmaBCorX($id) {
		global $grima;
		$suffix = $grima->suffix;
		if (preg_match("/^23.*$suffix$/",$id)) { # item_pid
			$this->loadFromAlmaX($id);
		} else {
			$this->loadFromAlmaBarcode($id);
		}
	}
// }}}

// {{{ loadFromItemListNode
/**
 * @brief populate item record from the information in an ItemList node
 *
 * @param DomNode $node node from an ItemList
 */
	function loadFromItemListNode($node) {
		$this->xml = new DomDocument();
		$this->xml->appendChild($this->xml->importNode($node,true));
	}
// }}}

// {{{ addToAlmaHolding (post)
/**
 * @brief add new item record to holding in Alma
 * @param string $mms_id MMS ID of bib record
 * @param string $holding_id Holding ID of holding record to add item to
 * @return DomDocument item object as it now appears in Alma
 */
	function addToAlmaHolding($mms_id,$holding_id) {
		global $grima;
		$this->mms_id = $mms_id;
		$this->holding_id = $holding_id;
		$this->xml = $grima->postItem($mms_id,$holding_id,$this->xml);
		return $this->xml;
	}
// }}}

// {{{ updateAlma (put)
/**
 * @brief replace item record in Alma
 * @return DomDocument item object as it now appears in Alma
 */
	function updateAlma() {
		global $grima;
		return $grima->putItem(
			$this['mms_id'],
			$this['holding_id'],
			$this['item_pid'],
			$this->xml
		);
	}
// }}}

// {{{ deleteFromAlma (delete)
/**
 * @brief delete record from Alma
 *
 * @param string $override should the item be deleted even if warnings exist? (default false)
 * @param string $holdings method for handling holdings record left with no items (retain, delete, suppress)
 */
	function deleteFromAlma($override = "false", $holdings = "retain") {
		global $grima;
		$grima->deleteItem($this['mms_id'],$this['holding_id'],$this['item_pid'],$override,$holdings);
	}
// }}}

}

/** }}} */

// {{{ class Electronic_Collection
/** class Electronic_Collection */
class Electronic_Collection {
	public $collection_id;
	function __construct($collection_id = null) {
		if ($collection_id) {
			$this->collection_id = $collection_id;
		}
	}

	public $services = array();

	function getServices() {
		print "Getting services\n";
		global $grima;
		$ret = $grima->getElectronicServices($this->collection_id);
		$xpath = new DomXpath($ret);
		$eservices = $xpath->query('//electronic_services/electronic_service');
		foreach ($eservices as $service) {
			$id = $service->firstChild->nodeValue;
			$ser = new Electronic_Service($collection_id,$id);
			$services[] = $ser;
		}
	}
}

/** }}} */

// {{{ class Electronic_Service
/** class Electronic_Service */
class Electronic_Service {
	public $collection_id;
	public $service_id;

	public $is_local;
	public $type;
	public $public_description;
	public $activation_status;
	public $number_of_portfolios;

	public $portfolios = array();

	function __construct($collection_id = null, $service_id = null) {
		if ($collection_id) {
			$this->collection_id = $collection_id;
		}
		if ($service_id) {
			$this->service_id = $service_id;
		}
	}

	function retrieveAllPortfolios() {
	 	# XXX
	}

	function retrievePortfolios($limit = 10, $offset = 0) {
		global $grima;
		$ret = $grima->getElectronicPortfolios();
		return $ret;
		# XXX
	}

	function deleteAllPortfolios() { #XXX
		# get portfolio list? or just ten at a time?
		$this->retrievePortfolios();
		while (sizeof($this->portfolios) > 0) {
			foreach ($this->portfolios as $portfolio) {
			print $portfolio['mms_id']->nodeValue; exit;
				#$portfolio->delete();
			}
		}
	}
}

/** }}} */

//  {{{ class Electronic_Portfolio
/** class Electronic_Portfolio */

class Electronic_Portfolio extends AlmaObject {
	public $xml;

	public $el_address = array(
		'portfolio_id' => '//portfolio/id',
		'is_local' => '//is_local',
		'is_standalone' => '//is_standalone',
		'mms_id' => '//mms_id',
		'title' => '//title',
		'service' => '//service',
		'availability' => '//availability'
	);


	function __construct() {
		$this->xml = new DOMDocument();
		$this->xml->loadXML(file_get_contents('templates/portfolio-blank.xml'));
	}

	function existsInAlma() {
		global $grima;
		$ret = getElectronicPortfolio($this->collection_id,
			$this->service_id, $this->portfolio_id);
		if ($ret->query('//portfolio')) {
			return true;
		} else {
			return false;
		}
	}

	function addToAlma() {
		global $grima;
		$ret = $grima->postElectronicPortfolio($this->collection_id, $this->service_id, $this);
		return $ret;
	}

	function loadFromAlma($portfolio_id) {
		global $grima;
		$this->xml = $grima->getElectronicPortfolio('X','X',$portfolio_id);
	}

	function loadFromAlmaX($portfolio_id) {
		global $grima;
		$this->xml = $grima->getElectronicPortfolio('X','X',$portfolio_id);
	}

	function deleteFromAlma() {   # accept a variable?
		global $grima;
		$grima->deleteElectronicPortfolio('X','X',
			$this['portfolio_id']);

		#XXX do something about deleting bibs with no inventory?

	}

}

/** }}} */

// {{{ class Set
/** class Set IN PROGRESS */
class Set {
	public $id;
	public $type; # itemized or logical
	public $name;
	public $xml;
	public $private;
	public $active;
	public $members = array();
	public $size; # number of members


	function __construct() {
		## from blank template?
	}

	function loadFromAlma($set_id) {
	}

	function getMembers($limit = -1) { # put in $members
		# limit -1 means all
		global $grima;
		if ($limit == -1) { # get them all
			$xml = $grima->getSetMembers($this->id,0);
			$xpath = new DomXpath($xml);
			$this->size = $xpath->query("//members")->item(0)->getAttribute("total_record_count");
			$limit = $this->size;
		}

		for ($j = 0; $j < ceil($limit/100); $j++) { # how many queries
			$xml = $grima->getSetMembers($this->id,100,$j*100);
			$xpath = new DomXpath($xml);
			foreach ($xpath->query("//member") as $member) {
				print "member\n";
				$this->members[] = new SetMember(
					$member->childNodes[0]->nodeValue,
					$member->childNodes[1]->nodeValue
				);
			}
		}
	}

	function addMember($mms_id) {
		global $grima;
	}

	function deleteAllMembers() {
		global $grima;
	}

}

/** }}} */

// {{{ class SetMember
/** class SetMember */
class SetMember {
	public $id;
	public $description;

	function __construct($id,$description) {
		$this->id = $id;
		$this->description = $description;
	}
}

/** }}} */

/* vim: set foldmethod=marker noexpandtab shiftwidth=4 tabstop=4: */
