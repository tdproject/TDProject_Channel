<?php

/**
 * TDProject_Channel_Model_Serializer_Package_Packages
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
class TDProject_Channel_Model_Serializer_ChannelPackage_Packages
    extends TDProject_Channel_Model_Serializer_ChannelPackage_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/p\/packages\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_ALLPACKAGES);
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
     * Returns the collection with the channel's packages.
     *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
     * @return TechDivision_Collections_ArrayList
     * 		The Collection with the channel's packages
     */
    public function getCollection(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// return the Collection with the channel's packages
		return $assembler->getChannel()->getChannelPackages();
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::serialize()
     */
    public function serialize(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// load the channel from the assembler
    	$this->setChannel($assembler->getChannel());
    	// initialize a new DOM document
    	$doc = new DOMDocument('1.0', 'UTF-8');
    	// create new namespaced root element
    	$a = $doc->createElementNS($this->getNamespace(), 'a');
    	// add the schema to the root element
    	$a->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation',
            'http://pear.php.net/dtd/rest.allpackages http://pear.php.net/dtd/rest.allpackages.xsd'
    	);
    	// create an element for the channel's name
    	$c = $doc->createElement('c');
    	$c->nodeValue = $this->getChannel()->getName();
    	// append the element with the channel's name to the root element
    	$a->appendChild($c);
    	// iterate over the channel's packages
    	foreach ($this->getCollection($assembler) as $channelPackage) {
    		// create element, load and set the package
    		$p = $doc->createElement('p');
    		$p->nodeValue = $channelPackage->getName();
    		// append the element to the root element
    		$a->appendChild($p);
    	}
    	// append the root element to the DOM tree
    	$doc->appendChild($a);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}