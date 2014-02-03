<?php

/**
 * TDProject_Channel_Model_Serializer_Category_Categories
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
class TDProject_Channel_Model_Serializer_Category_Categories
    extends TDProject_Channel_Model_Serializer_Category_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/c\/categories\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_ALLCATEGORIES);
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
     * Returns the collection with the channel's categories.
     *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
     * @return TechDivision_Collections_ArrayList
     * 		The Collection with the categories
     */
    public function getCollection(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// return the Collection with the channel's categories
		return $assembler->getChannel()->getCategories();
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
		// set the package instance
    	$this->getCategoryByName($assembler);
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new namespaced root element
        $a = $doc->createElementNS($this->getNamespace(), 'a');
        // add the schema to the root element
        $a->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.allcategories http://pear.php.net/dtd/rest.allcategories.xsd'
        );
        // create an element for the channel's name
        $ch = $doc->createElement('ch');
        $ch->nodeValue = $this->getChannel()->getName();
        // append the element with the channel's name to the root element
        $a->appendChild($ch);
        // iterate over the channel's categories
        foreach ($this->getCollection($assembler) as $category) {
            $c = $doc->createElement('c');
            $c->setAttributeNS(
            	'http://www.w3.org/1999/xlink',
            	'xlink:href',
            	'/c/'. $category->getName() .'/info.xml'
            );
            $c->nodeValue = $category->getName();
            // append the XLink to the root element
            $a->appendChild($c);
        }
        // append the root element to the DOM tree
        $doc->appendChild($a);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}