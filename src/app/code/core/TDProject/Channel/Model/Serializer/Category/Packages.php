<?php

/**
 * TDProject_Channel_Model_Serializer_Category_Packages
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
class TDProject_Channel_Model_Serializer_Category_Packages
    extends TDProject_Channel_Model_Serializer_Category_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/c\/(?P<categoryName>\w+)\/packages\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_CATEGORYPACKAGES);
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
     * Returns the collection with the category's packages.
     *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
     * @return TechDivision_Collections_ArrayList
     * 		The Collection with the packages
     */
    public function getCollection(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
        // load channel + category ID
        $channelId = $this->getChannel()->getChannelId();
        $categoryId = $this->getCategory()->getCategoryId();
        // return the packages for channel + category ID
        return $assembler->getChannelPackageByChannelIdAndCategoryId($channelId, $categoryId);
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
        $l = $doc->createElementNS($this->getNamespace(), 'l');
        // add the schema to the root element
        $l->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.categorypackages http://pear.php.net/dtd/rest.categorypackages.xsd'
        );
        // attach the packages information
        foreach ($this->getCollection($assembler) as $package) {
            // create the element with the link to the package
            $p = $doc->createElement('p');
            $p->setAttributeNS(
            	'http://www.w3.org/1999/xlink',
            	'xlink:href',
            	'/p/' . $packageName = $package->getName()
            );
            // set the package name
            $p->nodeValue = $packageName;
            // append the XLink to the root element
            $l->appendChild($p);
        }
        // append the root element to the DOM tree
        $doc->appendChild($l);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}