<?php

/**
 * TDProject_Channel_Model_Serializer_Release_AllReleases
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
class TDProject_Channel_Model_Serializer_Release_AllReleases
    extends TDProject_Channel_Model_Serializer_Release_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/r\/(?P<packageName>\w+)\/allreleases\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_ALLRELEASES);
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
     * Returns the collection with the channel package's releases.
     *
     * @param TDProject_Channel_Model_Entities_ChannelPackage $channelPackage
     * 		The channel package instance
     * @return TechDivision_Collections_ArrayList
     * 		The Collection with the releases
     */
    public function getCollection(
    	TDProject_Channel_Model_Entities_ChannelPackage $channelPackage)
    {
    	// load and sort the Collection with the channel package's releases
		$releases = $channelPackage->getReleases();
        $this->sortByVersionDescending($releases);
        return $releases;
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::serialize()
     */
    public function serialize(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// load the package instance
    	$this->getChannelPackageByName($assembler);
    	$this->setChannel($assembler->getChannel());
    	// initialize a new DOM document
    	$doc = new DOMDocument('1.0', 'UTF-8');
    	// create new namespaced root element
    	$a = $doc->createElementNS($this->getNamespace(), 'a');
    	// add the schema to the root element
    	$a->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.allreleases http://pear.php.net/dtd/rest.allreleases.xsd'
    	);
    	// create an element for the package's name
    	$p = $doc->createElement('p');
    	$p->nodeValue = $this->getChannelPackage()->getName();
    	// append the element with the packages's name to the root element
    	$a->appendChild($p);
    	// create an element for the channel's name
    	$c = $doc->createElement('c');
    	$c->nodeValue = $this->getChannel()->getName();
    	// append the element with the channel's name to the root element
    	$a->appendChild($c);
    	// load the available releases
    	$releases = $this->getCollection($this->getChannelPackage());
    	// if releases are available
    	if ($releases->size() > 0) {
    		// iterate over the channel's categories
    		foreach ($releases as $release) {
    			// create an element for each release
    			$r = $doc->createElement('r');
    			// create an element for the version number
    			$v = $doc->createElement('v');
    			$v->nodeValue = $release->getVersion();
    			$r->appendChild($v);
    			// create an element for the versions stability
    			$s = $doc->createElement('s');
    			$s->nodeValue = $release->getStability();
    			$r->appendChild($s);
    			// append the element to the root element
                $a->appendChild($r);
        	}
        }
        // append the root element to the DOM tree
        $doc->appendChild($a);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}