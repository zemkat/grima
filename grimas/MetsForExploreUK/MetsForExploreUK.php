<?php

require_once("../grima-lib.php");

function strip_end($str) {
	return rtrim($str,' /,');
}

function stabbrev($state) {
	$starray = array(
		'Alabama' => 'Ala.',
		'Alberta' => 'Alta.',
		'Arizona' => 'Ariz.',
		'Arkansas' => 'Ark.',
		'Australian Capital Territory' => 'A.C.T.',
		'British Columbia' => 'B.C.',
		'California' => 'Calif.',
		'Colorado' => 'Colo.',
		'Connecticut' => 'Conn.',
		'Delaware' => 'Del.',
		'District of Columbia' => 'D.C.',
		'Distrito Federal' => 'D.F.',
		'Florida' => 'Fla.',
		'Georgia' => 'Ga.',
		'Illinois' => 'Ill.',
		'Indiana' => 'Ind.',
		'Kansas' => 'Kan.',
		'Kentucky' => 'Ky.',
		'Louisiana' => 'La.',
		'Maine' => 'Me.',
		'Manitoba' => 'Man.',
		'Maryland' => 'Md.',
		'Massachusetts' => 'Mass.',
		'Michigan' => 'Mich.',
		'Minnesota' => 'Minn.',
		'Mississippi' => 'Miss.',
		'Missouri' => 'Mo.',
		'Montana' => 'Mont.',
		'Nebraska' => 'Neb.',
		'Nevada' => 'Nev.',
		'New Brunswick' => 'N.B.',
		'New Hampshire' => 'N.H.',
		'New Jersey' => 'N.J.',
		'New Mexico' => 'N.M.',
		'New South Wales' => 'N.S.W.',
		'New York' => 'N.Y.',
		'New Zealand' => 'N.Z.',
		'Newfoundland' => 'Nfld.',
		'North Carolina' => 'N.C.',
		'North Dakota' => 'N.D.',
		'Northern Territory' => 'N.T.',
		'Northwest Territories' => 'N.W.T.',
		'Nova Scotia' => 'N.S.',
		'Oklahoma' => 'Okla.',
		'Ontario' => 'Ont.',
		'Oregon' => 'Or.',
		'Pennsylvania' => 'Pa.',
		'Prince Edward Island' => 'P.E.I.',
		'Puerto Rico' => 'P.R.',
		'Queensland' => 'Qld.',
		'Rhode Island' => 'R.I.',
		'Russian Soviet Federated Socialist Republic' => 'R.S.F.S.R.',
		'Saskatchewan' => 'Sask.',
		'South Australia' => 'S. Aust.',
		'South Carolina' => 'S.C.',
		'South Dakota' => 'S.D.',
		'Tasmania' => 'Tas.',
		'Tennessee' => 'Tenn.',
		'Territory of Hawaii' => 'T.H.',
		'Texas' => 'Tex.',
		'Union of Soviet Socialist Republics' => 'U.S.S.R.',
		'United Kingdom' => 'U.K.',
		'United States' => 'U.S.',
		'Vermont' => 'VT.',
		'Victoria' => 'Vic.',
		'Virgin Islands' => 'V.I.',
		'Virginia' => 'Va.',
		'Washington' => 'Wash.',
		'West Virginia' => 'W. Va.',
		'Western Australia' => 'W.A.',
		'Wisconsin' => 'Wis.',
		'Wyoming' => 'Wyo.',
		'Yukon Territory' => 'Yukon'
	);

	if (isset($starray[$state])) {
		return $starray[$state];
	} else {
		return $state;
	}
}

class MetsForExploreUK extends GrimaTask {

	function do_task() {
		$this->bib = new Bib();
		$this->bib->loadFromAlma($this['mms_id']);

		$this->output = '<?xml version="1.0" encoding="UTF-8"?>
<mets:mets xmlns:rights="http://www.loc.gov/rights/" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:lc="http://www.loc.gov/mets/profiles" xmlns:bib="http://www.loc.gov/mets/profiles/bibRecord" xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" xmlns:mets="http://www.loc.gov/METS/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" OBJID="Abbay_Septimus_FR_1833" xsi:schemaLocation="http://www.loc.gov/METS/ http://www.loc.gov/standards/mets/mets.xsd http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd" PROFILE="lc:bibRecord">
<metsHdr CREATEDATE="{{current_timestamp}}" LASTMODDATE="{{current_timestamp}}" RECORDSTATUS="Production">
<mets:agent ROLE="CREATOR" TYPE="REPOSITORY">
<mets:name>University of Kentucky</mets:name>
<mets:note>SIP METS submitted to KDL.</mets:note>
</mets:agent>
<mets:agent ROLE="PRESERVATION" TYPE="ORGANIZATION">
<mets:name>University of Kentucky</mets:name>
<mets:note>SIP METS and associated file(s) submitted in University of Kentucky Preservation Repository.</mets:note>
</mets:agent>
<mets:altRecordID TYPE="DLXS"></mets:altRecordID>
</metsHdr>
<mets:dmdSec ID="DMD1">
<mets:mdWrap MIMETYPE="text/xml" MDTYPE="OAI_DC">
<mets:xmlData>
<oai_dc:dc 
    xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/" 
    xmlns:dc="http://purl.org/dc/elements/1.1/" 
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/
    http://www.openarchives.org/OAI/2.0/oai_dc.xsd">';

	## TITLE
	$fields = $this->bib->getFields("245");
	$field = $fields[0];
	$title = "";
	foreach ($field as $subfield) {
		if ($subfield[0] == "a") {
			$title .= $subfield[1];
		} else {
			if ($subfield[0] == "b") {
				$title .= " " . $subfield[1];
			}
		}
	}

	$this->output .= "\n";
	$this->output .= '  <dc:title xml:lang="en">' . strip_end($title) . '</dc:title>';

	## CREATOR
	$fields = $this->bib->getFields("1XX");
	$field = $fields[0];
	$creator = "";
	foreach ($field as $subfield) {
		switch($subfield[0]) {
			case 'a':
				$creator .= $subfield[1];
				break;
			case 'b':
			case 'c':
			case 'd':
			$creator .= " " . $subfield[1];
		}
	}

	$this->output .= "\n";
	$this->output .= '  <dc:creator>' . strip_end($creator) . '</dc:creator>';

	## PUBLISHER
	$fields = $this->bib->getFields("26X"); # more than one? 
	$field = $fields[0];
	foreach ($field as $subfield) {
		if ($subfield[0] == "b") {
			$publisher = $subfield[1];
		}
		if ($subfield[0] == "c") {
			$pubdate = rtrim(ltrim($subfield[1],'c'),'.');
		}
	}

	$this->output .= "\n";
	$this->output .= '  <dc:publisher>' . strip_end($publisher) . '</dc:publisher>';
	$this->output .= "\n";
	$this->output .= '  <dc:date>' . $pubdate . '</dc:date>';


	$covarray = array();
	## SUBJECTS
	$fields = $this->bib->getFields("6XX"); 
	foreach ($fields as $field) {
		$cova = "";
		$subj = $field[0][1];
		for($j=1;$j<sizeof($field);$j++) {
			$subj .= "--" . $field[1][1];
			if ($field[1][0] == "z") {
				$cova .= "--" . $field[1][1];
			}
		}
		if ($cova != "") {
			$covarray[] = $cova;
		}
		$this->output .= "\n";
		$this->output .= '  <dc:subject>' . $subj . '</dc:subject>';
	}

	$fields = $this->bib->getFields("651"); 
	foreach ($fields as $field) {
		$covarray[] = $field[0][1];
	}

	## COVERAGE
	for ($j=0;$j<sizeof($covarray);$j++) {
		$coco = rtrim($covarray[$j],".");
		$coco = ltrim($covarray[$j],"-");
		$covarray[$j] = $coco;
		/*
		$co = preg_split("/--/",$coco);
		if (sizeof($co) == 2) {
			$covarray[$j] = $co[1] . " (" . stabbrev($co[0]) . ")";
		} else {
			$covarray[$j] = $co[0];
		}
		*/
	}

	$covarray = array_unique($covarray);
	foreach ($covarray as $cov) {
		$this->output .= "\n";
		$this->output .= '  <dc:coverage>' . $cov . '</dc:coverage>';
	}

	## LANGUAGE ## ASK
	$this->output .= "\n";
	$this->output .= '  <dc:language>en</dc:language>';

	## DESCRIPTION
	$fields = $this->bib->getFields("300"); 
	$field = $fields[0];
	$str = "";
	foreach ($field as $subfield) {
		$str .= " " . $subfield[1];
	}
	$str = ltrim($str);
	$this->output .= "\n";
	$this->output .= '  <dc:description>' . $str . '</dc:description>';

$this->output .= '
  <dc:format></dc:format>
  <dc:type></dc:type>
  <dc:source></dc:source>
  <dc:identifier></dc:identifier>
  <dc:rights xml:lang="en">Contact the Special Collections Research Center for information regarding rights and use of this collection.</dc:rights>
</oai_dc:dc>
</mets:xmlData>
</mets:mdWrap>
</mets:dmdSec>
<mets:amdSec>
<!-- uncomment for newspapers
  <mets:digiProvMD ID="DPMD1">
    <mets:process>
      <mets:process_reformat FIELDTYPE="reformatInfo">300</mets:process_reformat>
    </mets:process>
  </mets:digiProvMD>
  -->
  <mets:rightsMD ID="RMD1">
    <mets:mdWrap MDTYPE="OTHER" MIMETYPE="text/xml" OTHERMDTYPE="RIGHTSMD">
      <mets:xmlData>
        <mets:versionStatement>{{current_year}}</mets:versionStatement>
      </mets:xmlData>
    </mets:mdWrap>
  </mets:rightsMD>
<mets:rightsMD ID="ADMRTS1">
<mets:mdWrap MDTYPE="OTHER" OTHERMDTYPE="METSRights">
<mets:xmlData>
<mets:RightsDeclarationMD RIGHTSCATEGORY="">
<mets:Context CONTEXTCLASS="GENERAL PUBLIC">
<mets:Constraints CONSTRAINTTYPE="RE-USE">
<mets:ConstraintDescription>Contact repository to obtain rights information.</mets:ConstraintDescription>
</mets:Constraints>
</mets:Context>
</mets:RightsDeclarationMD>
</mets:xmlData>
</mets:mdWrap>
</mets:rightsMD>
</mets:amdSec>
{{{pagination}}}
</mets:mets>
';

	}

    function print_success() {
		header('Content-Type: text/plain');
		#$dom = new DOMDocument();
        #$dom->loadXML($this->output);
		#XMLtoWeb($dom);
		print $this->output;
    }

}

MetsForExploreUK::RunIt();
