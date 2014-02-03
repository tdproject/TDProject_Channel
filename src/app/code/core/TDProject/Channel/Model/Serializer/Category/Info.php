<?php

/**
 * TDProject_Channel_Model_Serializer_Category_Info
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
class TDProject_Channel_Model_Serializer_Category_Info
    extends TDProject_Channel_Model_Serializer_Category_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/c\/(?P<categoryName>\w+)\/info\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_CATEGORY);
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
		// set the category and channel instance
    	$this->getCategoryByName($assembler);
    	$this->setChannel($assembler->getChannel());
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new namespaced root element
        $c = $doc->createElementNS($this->getNamespace(), 'c');
        // add the schema to the root element
        $c->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.category http://pear.php.net/dtd/rest.category.xsd'
        );
        // create an element for the category's name
        $n = $doc->createElement('n');
        $n->nodeValue = $this->getCategory()->getName();
        // append the element with the category's name to the root element
        $c->appendChild($n);
        // create an element for the channel's name
        $ca = $doc->createElement('c');
        $ca->nodeValue = $this->getChannel()->getName();
        // append the element with the channel's name to the root element
        $c->appendChild($ca);
        // create an element for the category's alias
        $a = $doc->createElement('a');
        $a->nodeValue = strtolower($this->getCategory()->getAlias());
        // append the element with the category's alias to the root element
        $c->appendChild($a);
        // create an element for the category's description
        $d = $doc->createElement('d');
        $d->nodeValue = $this->getCategory()->getDescription();
        // append the element with the category's description to the root element
        $c->appendChild($d);
        // append the root element to the DOM tree
        $doc->appendChild($c);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}