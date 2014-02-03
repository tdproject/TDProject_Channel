<?php

/**
 * TDProject_Channel_Model_Serializer_ChannelPackage_Maintainers
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
class TDProject_Channel_Model_Serializer_ChannelPackage_Maintainers
    extends TDProject_Channel_Model_Serializer_ChannelPackage_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/p\/(?P<packageName>\w+)\/maintainers\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_PACKAGEMAINTAINERS);
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
     * Returns the collection with the channel package's maintainers.
     *
     * @param TDProject_Channel_Model_Entities_ChannelPackage $channelPackage
     * 		The channel package instance
     * @return TechDivision_Collections_ArrayList
     * 		The Collection with the maintainers
     */
    public function getCollection(
    	TDProject_Channel_Model_Entities_ChannelPackage $channelPackage)
    {
    	// return the Collection with the channel package's maintainers
		return $channelPackage->getMaintainers();
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::serialize()
     */
    public function serialize(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
		// load the channel package instance
    	$this->getChannelPackageByName($assembler);
    	$this->setChannel($assembler->getChannel());
    	// initialize a new DOM document
    	$doc = new DOMDocument('1.0', 'UTF-8');
    	// create new namespaced root element
    	$m = $doc->createElementNS($this->getNamespace(), 'm');
    	// add the schema to the root element
    	$m->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.packagemaintainers http://pear.php.net/dtd/rest.packagemaintainers.xsd'
    	);
    	// create an element for the package's name
    	$p = $doc->createElement('p');
    	$p->nodeValue = $this->getChannelPackage()->getName();
    	$m->appendChild($p);
    	// create an element for the packages's channel name
    	$c = $doc->createElement('c');
    	$c->nodeValue = $this->getChannel()->getName();
    	$m->appendChild($c);
    	// add the package maintainers
    	foreach ($this->getCollection($this->getChannelPackage()) as $maintainer) {
    		// load the user related with the maintainer
    		$user = $assembler->getUserByUserId($maintainer->getUserIdFk());
    		// create an element for the package's maintainers
    		$mm = $doc->createElement('m');
    		// create an element for the maintainers handle
    		$h = $doc->createElement('h');
    		$h->nodeValue = $user->getUsername();
    		$mm->appendChild($h);
    		// create an element if the maintainer is active
    		$a = $doc->createElement('a');
    		$a->nodeValue = $maintainer->getActive()->__toString();
    		$mm->appendChild($a);
            // append the maintainer itself to the root element
            $m->appendChild($mm);
        }
        // append the root element to the DOM tree
        $doc->appendChild($m);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}