<?php

/**
 * TDProject_Channel_Model_Serializer_ChannelPackage_Info
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   	TDProject
 * @package    	TDProject_Channel
 * @subpackage	Model
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Model_Serializer_ChannelPackage_Info
    extends TDProject_Channel_Model_Serializer_ChannelPackage_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/p\/(?P<packageName>\w+)\/info\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_PACKAGE);
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::serialize()
     */
    public function serialize(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
		// set the package and channel instance
    	$this->getChannelPackageByName($assembler);
    	$this->setChannel($assembler->getChannel());
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new simple root element
        $p = $doc->createElement('p');
        // attach the body
        return $this->_content($doc, $p);
    }

    /**
	 * Simple representation without namespaces, for
	 * importing into other ressources.
	 *
	 * @param TDProject_Channel_Model_Assembler_Channel_Api
	 * 		The assembler instance
	 * @param TechDivision_Lang_Integer $channelPackageId The package ID
	 * @return DOMElement The element with the package information
     */
    public function simple(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler,
        TechDivision_Lang_Integer $channelPackageId)
    {
		// set the package and channel instance
    	$this->setChannelPackage(
    		$assembler->getChannelPackageByChannelPackageId($channelPackageId)
    	);
    	$this->setChannel($assembler->getChannel());
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new simple root element
        $p = $doc->createElement('p');
        // attach the body
        $this->_content($doc, $p);
        // return the node
        return $p;
    }

    /**
     * Attaches the content to the passed root node, appends it to the
     * also passed DOMDocument and returns the DOMDocument itself.
     *
     * @param DOMDocument $doc The DOM document to append the content
     * @param DOMElement $p The DOM element to append the content
     * @return DOMDocument The DOM document itself
     */
    protected function _content(DOMDocument $doc, DOMElement $p)
    {
        // create an element for the channel package's name
        $n = $doc->createElement('n');
        $n->nodeValue = $this->getChannelPackage()->getName();
        $p->appendChild($n);
        // create an element for the channel packages's channel name
        $c = $doc->createElement('c');
        $c->nodeValue = $this->getChannel()->getName();
        $p->appendChild($c);
        // create an element for the link to the channel package's category directory
        $packageCategory = $this->getChannelPackage()->getCategory()->getName();
        $ca = $doc->createElement('ca');
        $ca->setAttributeNS(
        	'http://www.w3.org/1999/xlink',
        	'xlink:href',
        	'/c/' . $packageCategory
        );
        $ca->nodeValue = $packageCategory;
        $p->appendChild($ca);
        // create an element for the channel package's licence
        $l = $doc->createElement('l');
        $l->nodeValue = $this->getChannelPackage()->getLicence();
        $p->appendChild($l);
        // create an element for the channel package's licence url
        $lu = $doc->createElement('lu');
        $lu->nodeValue = $this->getChannelPackage()->getLicenceUri();
        $p->appendChild($lu);
        // create an element for the channel package's summary
        $s = $doc->createElement('s');
        $s->nodeValue = $this->getChannelPackage()->getShortDescription();
        $p->appendChild($s);
        // create an element for the channel package's description
        $d = $doc->createElement('d');
        $d->nodeValue = $this->getChannelPackage()->getDescription();
        $p->appendChild($d);
        // create an element for the link to the channel package's release directory
        $r = $doc->createElement('r');
        $r->setAttributeNS(
        	'http://www.w3.org/1999/xlink',
        	'xlink:href',
        	'/r/'.$this->getChannelPackage()->getName()
        );
        $p->appendChild($r);
        // check if the channel package is deprecated
        if ($this->getChannelPackage()->getDeprecated()->booleanValue()) {
            // create an element for the new channel package's channel
            $dc = $doc->createElement('dc');
            $dc->nodeValue = $this->getChannelPackage()->getDeprecatedChannel();
            $p->appendChild($dc);
            // create an element for the new channel package's name
            $dp = $doc->createElement('dp');
            $dp->nodeValue = $this->getChannelPackage()->getDeprecatedPackage();
            $p->appendChild($dp);
        }
        // append the root element to the DOM tree
        $doc->appendChild($p);
        // return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
            new TechDivision_Lang_String($doc->saveXML())
        );
    }
}