<?php

require_once "ISO2709.php";

class MARC21RecordSetIterator extends ISO2709RecordSetIterator {
	public function __construct( $input ) {
		parent::__construct( $input );
		$this->prepend = fread( $this->input, 5 );
		if( ctype_digit( $this->prepend ) ) $this->RecordTerminator = ISO2709::RecordTerminator;
		else if( $this->prepend === "=LDR " ) {
			$this->RecordTerminator = "\r\n\r\n";
		} else if( $this->prepend === "=000 " ) {
			$this->RecordTerminator = "\r\n\r\n";
		}
		$this->recordClass = "MARC21Record";
	}
}

class MARC21RecordSetArray extends ISO2709RecordSetArray {
	public function __construct( $input ) {
		parent::__construct( $input );
		$this->recordClass = "MARC21Record";
	}
}

class MARC21Record extends ISO2709Record {
	protected $mnemonicType;
	function __construct( ) {
		parent::__construct();
		$this->set_default( "identifierLength", 2 );
		$this->set_default( "indicatorLength", 2 );
		$this->set_default( "lengthOfLengthOfField", 4 );
		$this->set_default( "lengthOfStartingCharacterPosition", 5 );
		$this->set_default( "lengthOfImplementationDefined", 0 );
		$this->mnemonicType = "LC";
	}

	function isTagValid( $tag ) {
		return is_string( $tag ) and ctype_digit( $tag ) and strlen( $tag ) == 3 and $tag !== "000";
	}

	function assertTagValid( $tag ) {
		if( !self::isTagValid( $tag ) ) {
			$this->exception("MARC21: Tags must be numeric and must be 3 bytes long. Thus '$tag' is not allowed.");
			return false;
		}
		return parent::assertTagValid( $tag );
	}

	function isTagRepeatable( $tag ) {
		return $tag != "001" and $tag != "005";
	}

	// 00* ordered ASCII-wise, otherwise order by century
	function tagOrder( $a, $b ) {
		if(!parent::isTagValid($a) || !parent::isTagValid($b)) return 0;
		if( $a === $b ) return 0;
		$a0 = substr( $a, 0, 2 );
		$b0 = substr( $b, 0, 2 );
		if( $a0 === $b0 and $a0 === "00" ) return strcmp( $a, $b );
		if( $a0 === "00" ) { return -1; }
		if( $b0 === "00" ) { return 1; }
		$a0 = substr( $a, 0, 1 );
		$b0 = substr( $b, 0, 1 );
		if( $a0 === "0" and $b0 === "0" ) {
			if( $a === "049" ) return 1;
			if( $b === "049" ) return -1;
		}
		return strcmp( $a0, $b0 );
	}

	function assertTagOrder( $a, $b ) {
		if( self::tagOrder( $a, $b ) > 0 ) {
			$this->exception("MARC21: 00* is ordered by ASCII, and the rest by century. Thus '$a' should not precede '$b'.");
			return false;
		}
		return parent::assertTagOrder( $a, $b );
	}

	function loadFromBinaryString( $string, $fuzzy = true ) {
		$ret = parent::loadFromBinaryString( $string );
		if( $ret === false ) return $ret;
		if( strlen($this->leader) != 24 ) return false;
		if(!ctype_print($this->leader))
			$this->exception("MARC21: Leader can only contain 0x20 through 0x7E, not 0x" . bin2hex( $this->leader ) );
		if($this->leader[23] != "0")
			$this->exception("MARC21: Last character of leader is supposed to be '0' not '{$this->leader[23]}'");
		return true;
		// Check data order
		# MARC21
		# 00*-00* are in numeric-alpha order in both directory and data (with no intervening in either directory or data)
	}

	function CheckField( $field ) {
		parent::CheckField( $field );
		if( substr( $field->tag, 0, 2 ) === "00" ) {
			// No extra requirements on control fields
		} else {
			// Indicators must be length 2, numeric, lower case, or space
			if( !preg_match("/^[0-9a-z ]{2}$/", $field->indicators) )
				$this->exception("MARC21 8.3.5: indicators should be length 2, and each character should be lower case, numeric, or a space. " .
					"So field '{$field->tag}' with indicators '{$field->indicators}' is not allowed." );
			// Subfield identifiers must be length 1, numeric, or lower case
			foreach( $field->subfields as $subfield ) {
				if( !preg_match("/^[0-9a-z]$/", $subfield->identifier) )
					$this->exception("MARC21 8.3.5: subfield identifiers should be length 1, and each character should be lower case or numeric. " .
						"So field '{$field->tag}' with subfield '{$subfield->identifier}' is not allowed." );
			}
		}
	}

	function loadFromMnemonicString( $string ) {
		$this->leader = array();
		$this->fields = array();
		$this->exceptions = array();
		$this->raw = $string;
		foreach( explode( "\r\n", $string ) as $line ) {
			$field = $this->ParseMnemonicLine( $line );
			if( $field !== false ) $this->AppendField( $field );
		}
	}

	function loadFromXMLString( $string ) {
		$this->leader = array(); # XXX ??
		$this->fields = array();
		$this->exceptions = array();
		$this->raw = $string;

		$doc = new DOMDocument();
		$doc->loadXML($string);
		$xpath = new DOMXpath($doc);

		$leaderfield = $xpath->query('//record/leader');
		if ($leaderfield) { # <leader>00838mas a2200301 a 4500</leader>
			$leader = $leaderfield[0]->nodeValue;
			if( strlen($leader) != 24 ) return false;
			$this->leader = $leader;
		}

		# $field = new ISO2709Field( $tag, $data, $indicators, $subfields, $directoryEntry, $this );

		$controlfield = $xpath->query('//record/controlfield');
		if ($controlfield) { # <controlfield tag="001"> 92005291 </controlfield>
			foreach ($controlfield as $cfd) {
				$tag = $cfd->getAttribute('tag');
				$data = $cfd->nodeValue;
				$field = new ISO2709Field( $tag, $data ); 
				$this->AppendField( $field );
			}
		}

		$datafield = $xpath->query('//record/datafield');
		if ($datafield) { # <datafield ind1=" " ind2=" " tag="035"><subfield code="9">AHE8121LM</subfield></datafield>
			foreach ($datafield as $dfd) {
				$tag = $dfd->getAttribute('tag');
				$indicators = $dfd->getAttribute('ind1') . $dfd->getAttribute('ind2');
				$subfields = array();
				foreach ($dfd->childNodes as $subf) {
					$code = $subf->getAttribute('code');
					$value = $subf->nodeValue;
					$subfields[] = new ISO2709Subfield( $code, $value );
				}
				$field = new ISO2709Field( $tag, null, $indicators, $subfields);
				$this->AppendField( $field );
			}
		}
	}

	function asXMLString( ) {
		$str = '<record>';
		$str .= '<leader>' . $this->leader . '</leader>';
        foreach( $this->fields as $field ) {
			if ( isset($field->data ) ) {
				$str .= "<controlfield tag=\"{$field->tag}\">{$field->data}</controlfield>";
			} else {
				$str .= "<datafield tag=\"{$field->tag}\">";
				foreach( $field->subfields as $subfield ) {
					$str .= "<subfield code=\"{$subfield->identifier}\">{$subfield->data}</subfield>";
        		}
				$str .= '</datafield>';
			}
		}
		$str .= '</record>';
		return $str;
	}

	function loadFromString( $string ) {
		if (preg_match("/^ *([^ ])/",$string,$m)) {
			$starts_with = $m[1];
			if ($starts_with == '<') {
				return $this->loadFromXMLString( $string );
			}
			if ($starts_with == '=') {
				return $this->loadFromMnemonicString( $string );
			}
			if (ctype_digit($starts_with)) {
				return $this->loadFromBinaryString( $string );
			}
			return false;
		}
		return false;
	}

	function ParseMnemonicLine( $line ) {
		$equ = substr( $line, 0, 1 );
		$tag = substr( $line, 1, 3 );
		$spa = substr( $line, 4, 2 );
		$dat = substr( $line, 6 );
		if( $equ !== "=" or $spa !== "  " ) {
			$this->exception("Mnemonic: unreadable line '$line'");
			return false;
		}
		if( $tag == "LDR" or $tag == "000" ) {
			$this->leader = $dat;
			$this->indicatorLength = 2;
			$this->identifierLength = 2;
			$this->mnemonicType = ($tag == "LDR") ? "TRME" : "LCMB";
			return false;
		}
		if( $this->mnemonicType === "TRME" ) {
			$dat = str_replace( '$', ISO2709::SubfieldInitiator, $dat );
			$dat = str_replace( '\\', ' ', $dat );
		} else if( $this->mnemonicType === "LCMB" ) {
			$dat = str_replace( '$', ISO2709::SubfieldInitiator, $dat );
			$dat = str_replace( '#', ' ', $dat );
		/* // These might not really be applicable
		} else if( $this->mnemonicType === "OCLC" ) {
			$dat = str_replace( 'ǂ', ISO2709::SubfieldInitiator, $dat );
			// no space translation
		} else if( $this->mnemonicType === "VGER" ) {
			$dat = str_replace( '‡', ISO2709::SubfieldInitiator, $dat );
			// no space translation
		} else if( $this->mnemonicType === "ALMA" ) {
			$dat = str_replace( '$$', ISO2709::SubfieldInitiator, $dat );
		*/
		} else {
			if( substr($tag,0,2) === "00" ) {
				$dat = str_replace( '#', ' ', $dat );
				$dat = str_replace( '\\', ' ', $dat );
			} else {
				if( $dat[0] == '\\' ) $dat[0] = ' ';
				if( $dat[1] == '\\' ) $dat[1] = ' ';
				if( $dat[0] == '#' ) $dat[0] = ' ';
				if( $dat[1] == '#' ) $dat[1] = ' ';
				if( strpos( $dat, ISO2709::SubfieldInitiator ) === false ) {
					$dat = str_replace( '$', ISO2709::SubfieldInitiator, $dat );
				}
			}
		}
		return $this->ParseBinaryField( $tag, $dat, array() );
	}

	function AppendField( $field, $reorder = false ) {
		if( is_string( $field ) ) {
			$field = $this->ParseMnemonicLine( $field );
		}
		if( is_a( $field, "ISO2709Field" ) ) return parent::AppendField( $field, $reorder );
	}

	
	function AsMnemonicString( $leader_tag = 'LDR', $field_initiator = '=', $tag_terminator = '  ', $identifier_initiator = '$', $field_terminator = "\r\n", $record_terminator = "\r\n", $space_replacer = ' ') {
		if( $this->mnemonicType === "TRME" ) return parent::AsMnemonicString( "LDR", "=", "  ", "\$", "\r\n", "\r\n", "\\" );
		if( $this->mnemonicType === "LCMB" ) return parent::AsMnemonicString( "000", "=", "  ", "\$", "\r\n", "\r\n", "#" );
		if( $this->mnemonicType === "VGER" ) return parent::AsMnemonicString( "LEADER", "=", "  ", "|", "\r\n", "\r\n", "#" );
		#return parent::AsMnemonicString( "LDR", "=", "  ", "\$", "\r\n", "\r\n", " " );
		return parent::AsMnemonicString( $leader_tag, $field_initiator, $tag_terminator, $identifier_initiator, $field_terminator, $record_terminator, $space_replacer );
	}

	function delFields( $pattern ) {
		foreach( $this->fields as $k => $v ) {
			$vmne = $v->AsMnemonicString( "=", "  ", "\$", "\r\n", '\\' );
			if( preg_match( "@^" . $pattern . "@", $vmne ) ) unset( $this->fields[$k] );
		}
		$this->fields = array_values( $this->fields );
	}
		
}
