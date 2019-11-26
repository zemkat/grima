<?php


/***********************************************************************
**
**  Methods for dealing with ISO2709 (MARC) records and files
*/

# TODO: should fields be in directory order or data order?

# TODO: Missing exceptions:
# ISO2709
# 001 is non-repeatable
# 001 is first in data
# 002-009 are next in data, but in any order

class ISO2709RecordSetIterator implements Iterator {
	protected $input, $currentPosition, $currentRecord, $valid, $RecordTerminator, $prepend;
	public function __construct( $input ) {
		if( is_string( $input ) ) $input = fopen( $input, "rb" );
		if( !is_resource( $input ) ) throw new Exception("ISO2709RecordSetIterator requires a file resource, filename, or URL to be created");
		$this->input = $input;
		$this->recordClass = "ISO2709Record";
		$this->RecordTerminator = ISO2709::RecordTerminator;
		$this->prepend = "";
	}
	public function valid() { return $this->valid; }
	public function current() {
		return $this->currentRecord;
	}
	public function key() {
		return $this->currentPosition;
	}
	public function next() {
		if( $this->currentRecord === null ) $this->currentRecord = new $this->recordClass();
		$this->currentPosition++;
		$string = stream_get_line( $this->input, 99999, $this->RecordTerminator );
		$this->valid = true;
		if( $string === false ) $this->valid = false;
		else $this->currentRecord->loadFromString( $this->prepend . $string );
		$this->prepend = "";
	}
	public function rewind() {
		if($this->currentPosition !== null) rewind( $this->input );
		$this->currentPosition = -1;
		$this->currentRecord = new $this->recordClass();
		$this->next();
	}
}

class ISO2709RecordSetArray implements ArrayAccess {
	protected $input, $records, $numRecords, $cache, $recordOffsets, $numCache, $recordClass;

	function __construct( $input, $numCache = 10 ) {
		if( is_string( $input ) ) $input = fopen( $input, "rb" );
		if( !is_resource( $input ) ) throw new Exception("ISO2709RecordSetArray requires a file resource, filename, or URL to be created");
		$this->input = $input;
		$this->records = array();
		$this->cache = array();
		$this->recordOffsets = array();
		$this->recordOffsets[0] = 0;
		$this->numCache = $numCache;
		$this->recordClass = "ISO2709Record";
	}

	public function offsetExists( $offset ) {
		if(!is_int($offset) or $offset < 0 or ($this->numRecords !== null and $this->numRecords <= $offset))
			return false;
		return $this->offsetGet( $offset ) === false;
	}

	public function offsetGet ( $offset ) {
		# Update cache
		foreach( array_keys( $this->cache, $offset ) as $k ) { unset($this->cache[$k]); }
		$this->cache = array_values( $this->cache );
		array_push( $this->cache, $offset );
		while( count( $this->cache ) > $this->numCache ) {
			$k = array_shift( $this->cache );
			unset( $this->records[$k] );
		}
		# Now get the record
		if( !isset( $this->records[ $offset ] ) ) {
			if( !isset( $this->recordOffsets[$offset] ) ) {
				for( $i = $offset - 1 ; $i > 0 ; $i-- ) {
					if( isset( $this->recordOffsets[$i] ) ) break;
				}
				fseek( $this->input, $this->recordOffsets[$i], SEEK_SET );
				while( $i < $offset ) {
					stream_get_line( $this->input, 99999, ISO2709::RecordTerminator );
					$i++;
					$this->recordOffsets[ $i ] = ftell( $this->input );
				}
			} else {
				fseek( $this->input, $this->recordOffsets[$offset], SEEK_SET );
			}
			$this->records[ $offset ] = new $this->recordClass();
			$this->records[ $offset ]->loadFromBinaryStream( $this->input );
			if(feof($this->input)) {
				$this->numRecords = $offset+1;
			} else {
				$this->recordOffsets[$offset+1] = ftell( $this->input );
			}
		}
		return $this->records[ $offset ];
	}

	public function offsetSet ( $offset, $value ) {
		throw new Exception( "This type of record set is read-only, sorry" );
	}

	public function offsetUnset ( $offset ) {
		throw new Exception( "This type of record set is read-only, sorry" );
	}
}

class ISO2709 {
	/* A record must be terminated with 0x1D, 
	the directory and each field must be terminated with 0x1E, 
	and each subfield begins with an identifier which must in turn begin
	with 0x1F */
	const RecordTerminator = "\035";
	const FieldTerminator = "\036";
	const SubfieldInitiator = "\037";

};

class ISO2709Exception extends Exception { };

class ISO2709Record {
	public $leader; // string form of the leader (mnemonic marc format)
	public $fields; // array of ISO2709 fields
	public $exceptions; // an array of standards violations as strings
	public $raw;
	protected $identifierLength, $indicatorLength;

	protected $defaults;

	protected function set_default( $name, $value ) {
		if( !isset( $this->defaults ) ) $this->defaults=array();
		if( !isset( $this->defaults[$name] ) ) $this->defaults[$name] = $value;
	}

	function __construct( ) { 
		$this->set_default( "identifierLength", 0 ); // required, so if not available disable subfields
		$this->set_default( "indicatorLength", 0 ); // advisory only
		$this->set_default( "lengthOfLengthOfField", 4 ); // required, but no real default; use MARC21
		$this->set_default( "lengthOfStartingCharacterPosition", 5 ); // required, but no real default; use MARC21
		$this->set_default( "lengthOfImplementationDefined", 0 ); // required, it seems like 0 is the only sensible value; use MARC21
		$this->leader = "";
		$this->fields = array();
		$this->exceptions = array();
	}

	############################################################	
	##
	##  Standards conformance
	##

	// ISO2709 4.5.1
	function isTagValid( $tag ) {
		return ($tag !== "000") && is_string( $tag ) && ( preg_match('/^[0-9A-Z]{3}$/',$tag) || preg_match('/^[0-9a-z]{3}$/',$tag) );
	}

	function assertTagValid( $tag ) {
		if( !self::isTagValid( $tag ) ) {
			$this->exception("ISO2709 4.5.1: Tags must be alphanumeric, using only one case, and must be 3 bytes long. Thus '$tag' is not allowed.");
			return false;
		}
		if( !$this->isTagValid( $tag ) ) {
			$this->exception("Unknown: Invalid tag '$tag'");
			return false;
		}
		return true;
	}

	// ISO2709 4.1
	function isTagRepeatable( $tag ) {
		return ($tag !== "001");
	}

	function assertTagRepeatable( $tag ) {
		if( !self::isTagRepeatable( $tag ) ) {
			$this->exception("ISO2709 4.5.1: 001 refers to the record identifier field, which is not repeatable (4.1c)");
			return false;
		}
		if( !$this->isTagRepeatable( $tag ) ) {
			$this->exception("Unknown: Tag '$tag' is not repeatable.");
			return false;
		}
		return true;
	}

	// ISO2709 4.1
	// -1 if $a must come before $b, +1 if $a must come after $b, 0 if unspecified
	// ISO2709 ordering is simple 001, then 00*, then ***
	function tagOrder( $a, $b ) {
		if(!$this->isTagValid($a) || !$this->isTagValid($b)) return 0;
		if( $a === $b ) return 0;
		if( $a === "001" ) return -1;
		if( $b === "001" ) return +1;
		$a = substr( $a, 0, 2 );
		$b = substr( $b, 0, 2 );
		if( $a === $b ) return 0;
		if( $a === "00" ) return -1;
		if( $b === "00" ) return +1;
		return 0;
	}

	function assertTagOrder( $a, $b ) {
		if( self::tagOrder( $a, $b ) > 0 )
			$this->exception("ISO2709 4.1: Field 001 is first, followed by 00* in any order, followed by all other tags; hence '$a' cannot precede '$b'");
		else if( $this->tagOrder( $a, $b ) > 0 )
			$this->exception("Unknown: '$a' is not supposed to precede '$b'");
	}

	function exception( $string ) {
		$this->exceptions[] = $string;
	}

	############################################################
	##
	##	Parsing a binary string
	##

	public function loadFromBinaryStream( $filehandle ) {
		$peek = stream_get_line( $filehandle, 99999, ISO2709::RecordTerminator );
		if( $peek ) $peek .= ISO2709::RecordTerminator;
		else return false;
		/* 
		$peek = fread( $filehandle, 5 );
		if( $peek === false ) return false;
		if( ! ctype_digit( $peek ) ) return false;
		$peek .= fread( $filehandle, intval( $peek ) - 5 );
		*/
		return $this->loadFromBinaryString( $peek );
	}

	private function checkString( $name, $trueValue, $string, $start = 0, $length = false, $default = false ) {
		$string = ( $length === false ) ? substr( $string, $start ) : substr( $string, $start, $length );
		if( is_int( $trueValue ) ) {
			if( !ctype_digit( $string ) or intval( $string ) !== $trueValue )
				$this->exception(
				"ISO2709: Value '$string' for \"$name\" does not match computed value '$trueValue'" );
		} else {
			if( !ctype_digit( $string ) )
				$this->exception(
				"ISO2709: Value '$string' for \"$name\" is not numeric" );
		}
		return ctype_digit($string)?intval( $string ):$default;
	}

	public function setLeader( $string ) {
		$this->leader = $string;
		$this->indicatorLength = $this->checkString( "Indicator Length", false, $this->leader, 10, 1, $this->defaults["indicatorLength"] );
		$this->identifierLength = $this->checkString( "Identifier Length", false, $this->leader, 11, 1, $this->defaults["identifierLength"] );
	}

	public function loadFromBinaryString( $string, $fuzzy = true ) {
		$this->leader = substr( $string, 0, 24 );
		$this->fields = array();
		$this->exceptions = array();
		$this->raw = $string;

		// We now ignore the leader as much as possible
		$recordLength = strpos( $string, ISO2709::RecordTerminator ) + 1;
		$baseAddressOfData = strpos( $string, ISO2709::FieldTerminator )+1;

		$this->checkString( "Record Length", $recordLength, $this->leader, 0, 5 );
		$indicatorLength = $this->checkString( "Indicator Length", false, $this->leader, 10, 1, $this->defaults["indicatorLength"] );
		$identifierLength = $this->checkString( "Identifier Length", false, $this->leader, 11, 1, $this->defaults["identifierLength"] );
		$this->checkString( "Base Address of Data", $baseAddressOfData, $this->leader, 12, 5 );
		$lengthOfLengthOfField = $this->checkString( "Length of Length-Of-Field portion of directory entry", false, $this->leader, 20, 1, $this->defaults["lengthOfLengthOfField"] );
		$lengthOfStartingCharacterPosition = $this->checkString( "Length of Starting-Character-Position portion of directory entry", false, $this->leader, 21, 1, $this->defaults["lengthOfStartingCharacterPosition"] );
		$lengthOfImplementationDefined = $this->checkString( "Length of Implementation-Defined portion of directory entry", false, $this->leader, 22, 1, $this->defaults["lengthOfImplementationDefined"] );
		if( "$lengthOfLengthOfField$lengthOfStartingCharacterPosition$lengthOfImplementationDefined" !== "450" ) {
			$this->exception( "MARC21: $lengthOfLengthOfField$lengthOfStartingCharacterPosition$lengthOfImplementationDefined != 450" );
			$lengthOfLengthOfField = 4;
			$lengthOfStartingCharacterPosition = 5;
			$lengthOfImplementationDefined = 0;
		}

		$this->indicatorLength = $indicatorLength; // advisory only
		$this->identifierLength = $identifierLength; // required

		$lengthOfDirectoryEntry = 3 + $lengthOfLengthOfField + $lengthOfStartingCharacterPosition + $lengthOfImplementationDefined;

		if( 0 != ( ( $baseAddressOfData-25) % $lengthOfDirectoryEntry ) )
		$this->exception( "ISO2709 4.4.1: Directory does not end on directory entry boundary ".
			"(Directory is ". ($baseAddressOfData-25)." bytes long, " .
			"each entry is 3 + $lengthOfLengthOfField + $lengthOfStartingCharacterPosition + $lengthOfImplementationDefined = $lengthOfDirectoryEntry bytes long, " .
			"leaving " . (($baseAddressOfData-25)%$lengthOfDirectoryEntry) . " bytes leftover)" );

		// now read the directory
		$fieldTypeMode = 0;
		$lastTag = false;
		for( $i = 24 ; $i+$lengthOfDirectoryEntry+1 <= $baseAddressOfData ; $i+= $lengthOfDirectoryEntry ) {
			$tag = substr( $string, $i, 3 );

			$off = $this->checkString( "Start of field '$tag'", false, $string, $i+3+$lengthOfLengthOfField, $lengthOfStartingCharacterPosition );
			if( $baseAddressOfData + $off > strlen($string) ) {
				$off = substr( $string, $i+3+$lengthOfLengthOfField, $lengthOfStartingCharacterPosition );
				$this->exception( "ISO2709: Offset '$off' of field '$tag' is beyond end of string. Losing this field." );
				continue;
			}
			if( $string[ $baseAddressOfData + $off - 1 ] !== ISO2709::FieldTerminator ) {
				$this->exception( "Jack: Data for field '$tag' does not immediately follow a field terminator" );
				$off = strrpos( substr( $string, 0, $baseAddressOfData + $off ), ISO2709::FieldTerminator ) - $baseAddressOfData + 1;
			}
			$len = strpos( $string, ISO2709::FieldTerminator, $baseAddressOfData + $off ) - ( $baseAddressOfData + $off - 1);
			$imp = substr( $string, $i + 3 + $lengthOfLengthOfField + $lengthOfStartingCharacterPosition, $lengthOfImplementationDefined );

			$maxFieldLen = intval( "1" .str_repeat( $lengthOfLengthOfField, "0" ) ) - 1;
			$calclen = 0;
			while( substr( $string, $i+3, $lengthOfLengthOfField ) === "000" ) {
				$calclen += $maxFieldLen;
				$i += $lengthOfDirectoryEntry;
				$newTag = substr( $string, $i, 3 );
				if( $tag !== $newTag ) {
					$this->exception("ISO2709 4.4.4: When the recorded field length is 0, each following directory entry refers to the same field. However, we went from '$tag' to '$newTag'.");
					$i -= $lengthOfDirectoryEntry;
					break;
				}
			}
			$this->checkString( "Length of field '$tag'", $len-$calclen, $string, $i+3, $lengthOfLengthOfField );


			// read in the field
			$data = substr( $string, $baseAddressOfData + $off, $len - 1 );
			$this->AppendFieldBinary( $tag, $data, array( $tag, $len, $off, $imp ) );
		}
		return true;
	}

	function DataNotInDirectory( ) {
		$string = $this->raw;
		$recordLength = strpos( $string, ISO2709::RecordTerminator ) + 1;
		$baseAddressOfData = strpos( $string, ISO2709::FieldTerminator )+1;
		$lengthOfLengthOfField = $this->checkString( "Length of Length-Of-Field portion of directory entry", false, $this->leader, 20, 1, $this->defaults["lengthOfLengthOfField"] );
		$lengthOfStartingCharacterPosition = $this->checkString( "Length of Starting-Character-Position portion of directory entry", false, $this->leader, 21, 1, $this->defaults["lengthOfStartingCharacterPosition"] );
		$lengthOfImplementationDefined = $this->checkString( "Length of Implementation-Defined portion of directory entry", false, $this->leader, 22, 1, $this->defaults["lengthOfImplementationDefined"] );
		$lengthOfDirectoryEntry = 3 + $lengthOfLengthOfField + $lengthOfStartingCharacterPosition + $lengthOfImplementationDefined;

		$map = str_repeat( "?", $recordLength );
		for( $i = 24 ; $i+$lengthOfDirectoryEntry+1 <= $baseAddressOfData ; $i+= $lengthOfDirectoryEntry ) {
			$tag = substr( $string, $i, 3 );
			$off = intval( substr( $string, $i+3+$lengthOfLengthOfField, $lengthOfStartingCharacterPosition ) );
			$off = strrpos( substr( $string, 0, $baseAddressOfData + $off ), ISO2709::FieldTerminator ) - $baseAddressOfData + 1;

			$calclen = 0;
			$maxFieldLen = intval( "1" .str_repeat( $lengthOfLengthOfField, "0" ) ) - 1;
			while( substr( $string, $i+3, $lengthOfLengthOfField ) === "000" ) {
				$i += $lengthOfDirectoryEntry;
				$calclen += $maxFieldLen;
			}
			$len = $calclen + intval( substr( $string, $i+3, $lengthOfLengthOfField ) );

			for( $j = 0 ; $j < $len ; $j++ ) {
				$map[$baseAddressOfData+$off+$j] = " ";
			}
			$map = substr_replace( $map, $tag, $baseAddressOfData+$off );
			$map[$baseAddressOfData+$off+$len-1] = "t";
		}
		for( $i = 0 ; $i < $recordLength ; $i++ ) {
			if( $i < 24 ) $map[$i] = "l";
			else if( $i+1 < $baseAddressOfData ) $map[$i] = "d";
			else if( $string[$i] == ISO2709::FieldTerminator ) {
				if($map[$i] != "t") $map[$i]="#";
				else $map[$i] = "T";
			}
		}
		print $map."\n";
	}

	function loadFromString( $string ) {
		return $this->loadFromBinaryString( $string );
	}
		

	############################################################
	##
	## 	Append a field to a MARC record
	##

	function ParseBinaryField( $tag, $data, $directoryEntry ) {
		# Check for valid tag
		$this->assertTagValid($tag);

		# Strip out indicators and subfields, handling bad data
		# If there is a subfield identifier initiator, then everything before it is the indicators
		# Otherwise the indicators are precisely the first indicatorLength bytes
		if( false !== ( $subfieldStart = strpos( $data, ISO2709::SubfieldInitiator ) ) ) {
			$indicators = substr( $data, 0, $subfieldStart );
			$subfields = explode( ISO2709::SubfieldInitiator, substr( $data, $subfieldStart+1 ) );
			foreach( $subfields as $k => $v ) {
				$identifier = substr( $v, 0, $this->identifierLength - 1);
				$subdata = substr( $v, $this->identifierLength-1 );
				$subfields[$k] = new ISO2709Subfield( $identifier, $subdata );
			}
			$data = "";
		} else {
			if( substr( $tag, 0, 2 ) !== "00" ) {
				$indicators = substr( $data, 0, $this->indicatorLength );
				$data = substr( $data, $this->indicatorLength );
			} else {
				$indicators = "";
			}
			$subfields = array();
		}
		return new ISO2709Field( $tag, $data, $indicators, $subfields, $directoryEntry, $this );
	}

	function AppendFieldBinary( $tag, $data, $directoryEntry = array(), $reorder = false ) {
		$field = $this->ParseBinaryField( $tag, $data, $directoryEntry );
		$this->AppendField( $field, $reorder );
	}

	function CheckField( $field ) {
		if( substr( $field->tag, 0, 2 ) === "00" ) {
			if( $field->indicators !== "" )
				$this->exception("ISO2709 4.5.4: Control fields have no indicators, but field '{$field->tag}' has indicators '{$field->indicators}'");
			if( count( $field->subfields ) > 0 )
				$this->exception("ISO2709 4.5.4: Control fields have no subfields, but field '{$field->tag}' has " . count( $field->subfields) . " subfields");
		} else {
			if( strlen( $field->indicators ) != $this->indicatorLength )
				$this->exception("ISO2709 4.5.4d: Field '{$field->tag}' has indicators '{$field->indicators}' of the wrong length (specified length is {$this->indicatorLength})");
			foreach( $field->subfields as $subfield ) {
				if( strlen( $subfield->identifier ) + 1 != $this->identifierLength )
					$this->exception("ISO2709: Field '{$field->tag}' has subfield '\${$subfield->identifier}' but that is not the right length of a subfield identifier.");
			}
			if( $field->data !== "" and $this->indicatorLength > 0 )
				$this->exception("ISO2709 4.5.4: Field '{$field->tag}' has data, but should only have subfields");
		}
	}

	function AppendField( $field, $reorder = false ) {
		$this->checkField( $field );
		$field->record = $this;

		# Check for valid ordering
		$numTags = count( $this->fields );
		if( $numTags > 0 ) {
			if( $reorder === false ) {
				$lastTag = $this->fields[ $numTags - 1 ]->tag;
				#$this->assertTagOrder( $lastTag, $field->tag );
				$appendAfter = $numTags;
			} else {
				for( $i = $numTags - 1 ; $i >= 0 ; $i-- ) {
					$lastTag = $this->fields[ $i ]->tag;
					if( $this->tagOrder( $lastTag, $field->tag ) <= 0 ) {
						#print "Append {$field->tag} after $lastTag \n";
						break;
					}
					#print "Do not {$field->tag} after $lastTag \n";
				}
				$appendAfter = $i;
			}
		} else { $appendAfter = -1; }

		array_splice( $this->fields, $appendAfter+1, 0, array( $field ) );
	}

	function removeField( $field ) {
		foreach( $this->fields as $k => $v ) {
			if( $v === $field ) unset( $this->fields[$k] );
		}
		$this->fields = array_values( $this->fields );
	}

	function delFields( $tag ) {
		foreach( $this->fields as $k => $v ) {
			if( $v->tag === $tag ) unset( $this->fields[$k] );
		}
		$this->fields = array_values( $this->fields );
	}

	function getFields( $tag ) {
		$ret = array();
		foreach( $this->fields as $v ) {
			if( $v->tag === $tag ) $ret[] = $v;
		}
		return $ret;
	}

	function getTagPattern( $patt ) {
		$ret = array();
		foreach( $this->fields as $v ) {
			if( preg_match("/$patt/", $v->tag ) ) $ret[] = $v;
		}
		return $ret;
	}

	############################################################
	##
	##	Export as binary
	##

	public function AsBinaryString( ) {
		// We now ignore the leader as much as possible
		$indicatorLength = $this->indicatorLength; // advisory, we will write corrupt files if requested
		$identifierLength = $this->identifierLength; // advisory
		$lengthOfLengthOfField = $this->checkString( "Length of Length-Of-Field portion of directory entry", false, $this->leader, 20, 1, $this->defaults["lengthOfLengthOfField"] );
		$lengthOfStartingCharacterPosition = $this->checkString( "Length of Starting-Character-Position portion of directory entry", false, $this->leader, 21, 1, $this->defaults["lengthOfStartingCharacterPosition"] );
		$lengthOfImplementationDefined = $this->checkString( "Length of Implementation-Defined portion of directory entry", false, $this->leader, 22, 1, $this->defaults["lengthOfImplementationDefined"] );
		$lengthOfDirectoryEntry = 3 + $lengthOfLengthOfField + $lengthOfStartingCharacterPosition + $lengthOfImplementationDefined;

		$directory = "";
		$data = "";
		$baseAddressOfData = 24 + $lengthOfDirectoryEntry * count( $this->fields ) + 1;
		foreach( $this->fields as $v ) {
			$tag = $v->tag;
			$start = strlen( $data );
			$data .= $v->indicators;
			foreach( $v->subfields as $vv ) {
				$data .= ISO2709::SubfieldInitiator . $vv->identifier . $vv->data;
			}
			$data .= $v->data;
			$data .= ISO2709::FieldTerminator;
			$length = strlen($data) - $start;
			$impdef = $v->directoryEntry[3];
			$directory .= sprintf(
				"%3.3s" .
				"%0{$lengthOfLengthOfField}.{$lengthOfLengthOfField}d" .
				"%0{$lengthOfStartingCharacterPosition}.{$lengthOfStartingCharacterPosition}d" .
				"%{$lengthOfImplementationDefined}.{$lengthOfImplementationDefined}s",
				$tag, $length, $start, $impdef );
		}
		$directory .= ISO2709::FieldTerminator;
		$data .= ISO2709::RecordTerminator;
		$recordLength = $baseAddressOfData + strlen($data);
		$leader = sprintf("%05.5d%1.1s%4.4s%1.1d%1.1d%05.5d%3.3s%1.1d%1.1d%1.1d%1.1s",
			$recordLength, substr( $this->leader, 5, 1 ),
                        substr( $this->leader, 6, 4), $indicatorLength,
                        $identifierLength, $baseAddressOfData, substr( $this->leader, 17, 3 ),
                        $lengthOfLengthOfField,
                        $lengthOfStartingCharacterPosition,
                        $lengthOfImplementationDefined, substr( $this->leader, 23, 1 ) );
		$this->leader = $leader;
		$ret = $leader . $directory . $data;
		assert( strlen( $leader ) + strlen( $directory ) === $baseAddressOfData );
		assert( strlen( $ret ) === $recordLength );
		return $ret;
	}

	############################################################
	##
	##	Export as Mnemonic string
	##


	public function AsMnemonicString( 
		$leader_tag = "LDR", 
		$field_initiator = "=",
		$tag_terminator = "  ",
		$identifier_initiator = "\$", 
		$field_terminator = "\r\n", 
		$record_terminator = "\r\n",
		$space_replacer = " "
	) {
		$ret = array();
		$ret[] = sprintf( "%s%3.3s%s%s%s%s%s", $field_initiator, $leader_tag,
				$tag_terminator, "", "", $this->leader, $field_terminator );
		foreach( $this->fields as $v ) {
			$ret[] = $v->AsMnemonicString( $field_initiator,$tag_terminator,$identifier_initiator,$field_terminator, $space_replacer );
		}
		foreach( $this->exceptions as $v ) {
			$ret[] = sprintf( "%s%3.3s%s%s%s%s%s", $field_initiator, "XXX",
                                $tag_terminator, "xx", "\$x$v", "", $field_terminator );
		}
		return implode( $ret ) . $record_terminator;
	}
}

class ISO2709Field {
	public $tag, $data, $indicators, $subfields, $directoryEntry;
	function __construct( $tag, $data, $indicators = "", $subfields = array(), $directoryEntry = array(), $record = null ) {
		$this->tag=$tag;
		$this->data=$data;
		$this->indicators = $indicators;
		$this->directoryEntry = $directoryEntry;
		if( !isset( $this->directoryEntry[0] ) ) $this->directoryEntry[0] = $tag;
		if( !isset( $this->directoryEntry[3] ) ) $this->directoryEntry[3] = "";
		$this->record = $record;
		$this->subfields = array();
		foreach( $subfields as $v ) { $this->appendSubfield( $v ); }
	}
	function remove() {
		return $this->record->removeField( $this );
	}
	function delete() {
		return $this->record->removeField( $this );
	}
	function appendSubfield( $subfield, $arg2 = null ) {
		if( is_a( $subfield, "ISO2709Subfield" ) ) {
			$subfield->field = $this;
			$this->subfields[] = $subfield;
		} else {
			$this->appendSubfield( new ISO2709Subfield( $subfield, $arg2 ) );
		}
	}

	# maybe title more specific
	function insertSubfieldBefore( $identifier, $subfield, $arg3 = null) {
		if( is_a( $subfield, "ISO2709Subfield" ) ) {
			$found = false;
			foreach( $this->subfields as $k => $v ) {
				if( $v->identifier === $identifier ) {
					array_splice($this->subfields,$k,0,array($subfield));
					$this->subfields = array_values($this->subfields);
					$found = true;
					if( $k > 0 ) {
						if (preg_match("/(.*?)( *[\:\/=] *)$/",$this->subfields[$k-1]->data,$m)) {
							$this->subfields[$k-1]->data = $m[1];
							$this->subfields[$k]->data .= $m[2];
						}
					}
					break;
				}
			}
			return $found;
		} else {
			return $this->insertSubfieldBefore( $identifier, new ISO2709Subfield( $subfield, $arg3 ) );
		}
	}
	
	function removeSubfield( \ISO2709Subfield $subfield ) {
		foreach( $this->subfields as $k => $v ) {
			if( $v === $subfield ) unset( $this->subfields[$k] );
		}
		$this->subfields = array_values( $this->subfields );
	}
	function getOneSubfield( $identifier ) {
		foreach( $this->subfields as $v ) {
			if( $v->identifier === $identifier ) return $v;
		}
		return false;
	}
	function getSubfields( $identifier ) {
		$ret = array();
		foreach( $this->subfields as $v ) {
			if( $v->identifier === $identifier ) $ret[] = $v;
		}
		return $ret;
	}

	public function AsMnemonicString( 
		$field_initiator = "=",
		$tag_terminator = "  ",
		$identifier_initiator = "\$", 
		$field_terminator = "\r\n", 
		$space_replacer = " "
	) {
		$sub = "";
		foreach( $this->subfields as $vv ) {
			$sub .= $identifier_initiator . $vv->identifier . $vv->data;
		}
		$indicators = str_replace(" ",$space_replacer, $this->indicators );
		$data = str_replace(" ",$space_replacer, $this->data );
		return sprintf( "%s%3.3s%s%s%s%s%s", $field_initiator, $this->tag, 
			$tag_terminator, $indicators, $sub, $data, $field_terminator );
	}
}

class ISO2709Subfield {
	public $identifier, $data, $field;
	function __construct( $identifier, $data ) {
		$this->identifier = $identifier;
		$this->data = $data;
	}
	function remove() {
		return $this->field->removeSubfield( $this );
	}
	function delete() {
		return $this->field->removeSubfield( $this );
	}

	function prev() {
		$prev = null;
		foreach( $this->field->subfields as $vv ) {
			if ($vv == $this) {
				return $prev;
			} else {
				$prev = $vv;
			}
		}
	}

}
