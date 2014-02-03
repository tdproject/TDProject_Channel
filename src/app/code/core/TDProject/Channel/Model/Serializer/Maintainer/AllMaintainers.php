<?php

/**
 * TDProject_Channel_Model_Serializer_Maintainer_AllMaintainers
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
class TDProject_Channel_Model_Serializer_Maintainer_AllMaintainers
    extends TDProject_Channel_Model_Serializer_Maintainer_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/m\/allmaintainers\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_ALLMAINTAINERS);
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
     * Returns a collection with all channel maintainers.
     *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
     * @return TechDivision_Collections_ArrayList
     * 		The Collection with the maintainers
     */
    public function getCollection(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// return the Collection with the channel maintainers
		return $assembler->getChannel()->getMaintainers();
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
        $m = $doc->createElementNS($this->getNamespace(), 'm');
        // add the schema to the root element
        $m->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.allmaintainers http://pear.php.net/dtd/rest.allmaintainers.xsd'
        );
        // iterate over the channel's maintainers
        foreach ($this->getCollection($assembler) as $maintainer) {
    		// load the user related with the maintainer
    		$user = $assembler->getUserByUserId($maintainer->getUserIdFk());
            // load the maintainer itself
            $handle = $user->getUsername();
            // check if a handle is set
            if (!empty($handle)) {
                // create the element with the link to the maintainer
                $h = $doc->createElement('h');
                $h->setAttributeNS(
                	'http://www.w3.org/1999/xlink',
                	'xlink:href',
                	'/channel/channelId/' . $this->getChannel()->getChannelId() .'/m/' . $handle
                );
                // set the maintainer handle
                $h->nodeValue = $handle;
                // append the XLink to the root element
                $m->appendChild($h);
            }
        }
        // append the root element to the DOM tree
        $doc->appendChild($m);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}