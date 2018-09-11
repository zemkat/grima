<?php

/******************************************************************************
 *
 *  XML Bags!
 *
 *  Wish you could treat XML as an array? Now you can!
 *
 *  What does xml['item'] = ?
 * 
 *  Three plausible answers:
 *  (1) A single DOMNode, so you have to use innerXML and such to get its string value
 *  (2) A DOMNodeList, so you can have multiple nodes that match
 *  (3) A string, just the innerXML
 *  (4) An array of strings, just the innerXMLs
 */


function xml_debug( $obj ) {
    $retval = get_class($obj)."\n";
    switch(true) {
        case ($obj instanceof DOMDocument):
            $retval .= "XPath: {$obj->getNodePath()}\n".$obj->saveXML($obj);
            break;
        case ($obj instanceof DOMElement):
            $retval .= "XPath: {$obj->getNodePath()}\n".$obj->ownerDocument->saveXML($obj);
            break;
        case ($obj instanceof DOMAttr):
            $retval .= "XPath: {$obj->getNodePath()}\n".$obj->ownerDocument->saveXML($obj);
            break;
        case ($obj instanceof DOMNodeList):
            $retval .= "NodeList of length {$obj->length}\n";
            print $retval;
            for ($i = 0; $i < $obj->length; $i++) {
                $retval .= "Item #$i, " . xml_debug($obj->item($i));
            }
            break;
        default:
            return "Instance of unknown class";
    }
    return $retval;
}
        
abstract class XMLAsArrayOfWhateversSpecifiedByXPath implements ArrayAccess {
    public $xml;
    public $context_node;
    private $xpath = null;
    function __construct( $xml, $context_node ) {
        if( is_string( $xml ) ) {
            $this->xml = new DOMDocument();
            $this->xml->loadXML( $xml );
        } elseif( $xml instanceof DOMDocument ) {
            $this->xml = $xml;
        } else {
            throw new Exception("<xml> must be of type DOMDocument in new XMLAsArrayOf...(xml,context)");
        }
        $this->xpath = new DOMXPath($this->xml);
        if( is_string( $context_node ) ) {
            $nodeList = $this->xpath->query( $context_node );
            //print( "Context ($context_node) is: " . xml_debug($nodeList) . "\n" );
            if( $nodeList->length === 1 ) {
                $this->context_node = $nodeList->item(0);
            } else {
                $this->context_node = $this->xml->documentElement;
            }
        } elseif( $context_node instanceof DOMElement ) {
            $this->context_node = $context_node;
        } else {
            $this->context_node = $this->xml->documentElement;
        }
    }
    protected function query( string $offset ) {
        if( isset( $this->context_node ) ) {
            return $this->xpath->query( $offset, $this->context_node );
        } else {
            return $this->xpath->query( $offset );
        }
    }
    public function __toString() {
        return $this->xml->saveXML( $this->context_node );
    }
    public function __get( string $name ) {
        $class = get_class($this);
        return new $class( $this->xml, $this->query($name) );
    }
    public function __set( string $name, $value ) {
        $this[$name] = $value;
    }
    public function __isset( string $name ) {
        return isset( $this[$name] );
    }
    public function __unset( string $name ) {
        unset( $this[$name] );
    }
}

class XMLAsArrayOfDOMNodesSpecifiedByXPath extends XMLAsArrayOfWhateversSpecifiedByXPath {
    public function offsetExists ( $offset ) {
        return $this->query( $offset )->length === 1;
    }
    public function offsetGet ( $offset ) {
        $nodeList = $this->query( $offset );
        if( $nodeList->length === 1 )
            return $nodeList->item(0);
    }
    public function offsetSet ( $offset, $value ) {
        $nodeList = $this->query( $offset );
        if( $nodeList->length === 1 ) {
            $node = $nodeList->item(0);
            if($node->parentNode !== NULL) {
                $node->parentNode->replaceChild( $value, $node );
            }
        } elseif( $nodeList->length === 0 ) {
            $nodeList->context_node->appendChild( $value );
        }
    }
    public function offsetUnset ( $offset ) {
        $nodeList = $this->query( $offset );
        if( $nodeList->length === 1 ) {
            $node = $nodeList->item(0);
            if($node->parentNode !== NULL) {
                $node->parentNode->removeChild( $node );
            }
        }
    }
}

class XMLAsArrayOfDOMNodeListsSpecifiedByXPath extends XMLAsArrayOfWhateversSpecifiedByXPath {
    public function offsetExists ( $offset ) {
        return $this->query( $offset )->length > 0; // technically it always exists, but...
    }
    public function offsetGet ( $offset ) {
        return $this->query( $offset );
    }
    public function offsetSet ( $offset, $value ) {
        $nodeList = $this->query( $offset );
        $cur_length = $nodeList->length;
        $new_length = $value->length;
        for( $i = 0 ; $i < min( $cur_length, $new_length ) ; $i++ ) {
            $cur_node = $nodeList->item($i);
            $new_node = $value->item($i);
            $cur_node->parentNode->replaceChild( $new_node, $cur_node );
        }
        for( $i = min( $cur_length, $new_length ) ; $i < $cur_length ; $i++ ) {
            $cur_node = $nodeList->item($i);
            $cur_node->parentNode->removeChild( $cur_node );
        }
        for( $i = min( $cur_length, $new_length ) ; $i < $new_length ; $i++ ) {
            $new_node = $value->item($i);
            $this->context_node->appendChild( $new_node );
        }
    }
    public function offsetUnset ( $offset ) {
        $nodeList = $this->query( $offset );
        $cur_length = $nodeList->length;
        for( $i = 0 ; $i < $cur_length ; $i++ ) {
            $cur_node = $nodeList->item($i);
            $cur_node->parentNode->removeChild( $cur_node );
        }
    }
}

class XMLAsArrayOfStringsSpecifiedByXPath extends XMLAsArrayOfWhateversSpecifiedByXPath {
    public function offsetExists ( $offset ) {
        return $this->query( $offset )->length === 1;
    }
    public function offsetGet ( $offset ) {
        $nodeList = $this->query( $offset );
        if( $nodeList->length === 1 ) {
            return $nodeList->item(0)->nodeValue;
        }
    }
    public function offsetSet ( $offset, $value ) {
        $nodeList = $this->query( $offset );
        if( $nodeList->length === 1 ) {
            print "Setting!\n";
            $nodeList->item(0)->nodeValue = $value;
        } else if( $nodeList->length === 0 ) {
            $node = $this->xml->createElement( basename($offset) );
            $node->nodeValue = $value;
            $this->contextNode->appendChild( $node );
        }
    }
    public function offsetUnset ( $offset ) {
        if( $nodeList->length === 1 ) {
            $node = $nodeList->item(0);
            $node->parentNode->removeChild( $node );
        }
    }
}

function test() {
    $xml = file_get_contents("item-sample.xml");
    $item_item_data = new XMLAsArrayOfStringsSpecifiedByXPath( $xml, "/item/item_data" );
    print "EnumerationA: " . $item_item_data['enumeration_a'] . "\n\n";
    $item_item_data['enumeration_a'] = "NOOOOOOOOO";
    print $item_item_data->xml->saveXML();
}

//test();
