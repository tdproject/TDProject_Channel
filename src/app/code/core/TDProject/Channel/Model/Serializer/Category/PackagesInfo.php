<?php

/**
 * TDProject_Channel_Model_Serializer_Category_PackagesInfo
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
class TDProject_Channel_Model_Serializer_Category_PackagesInfo
    extends TDProject_Channel_Model_Serializer_Category_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/c\/(?P<categoryName>\w+)\/packagesinfo\.xml/';

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
     * Returns the releases of the passed channel package.
     * 
     * @param TDProject_Channel_Model_Entities_ChannelPackage $channelPackage 
     * 		The channel package to return the releases for
     * @return TechDivision_Collections_ArrayList The releases itself
     */
    public function getReleases(
        TDProject_Channel_Model_Entities_ChannelPackage $channelPackage)
    {
        $releases = $channelPackage->getReleases();
        $this->sortByVersionDescending($releases);
        return $releases;
    }

    /**
     * Callback method used to sort PHP/PEAR version numbers descending with PHP usort().
     *
     * @param TDProject_Channel_Model_Entities_Release $release1 First release to compare
     * @param TDProject_Channel_Model_Entities_Release $release2 Second release to compare
     * @return integer 1 if the first version is lower than the second, 0 if they are equal, and -1 if the second is lower
     * @link http://us.php.net/usort
     */
    public function sortByVersionDescending($releases)
    {
        $comparator = new TDProject_Channel_Model_Serializer_Release_DescendingComparator();
        TechDivision_Collections_CollectionUtils::sort($releases, $comparator);
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
        $f = $doc->createElementNS($this->getNamespace(), 'f');
        // add the schema to the root element
        $f->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.categorypackages http://pear.php.net/dtd/rest.categorypackages.xsd'
        );
        // attach the channel packages information
        foreach ($this->getCollection($assembler) as $channelPackage) {
	        // create an element for the categories's channel package
	        $pi = $doc->createElement('pi');
            // create a node to import channel packages /p/<name>/info.xml information
            $info = $this->getPackageInfo(
            	$assembler, 
            	$channelPackage->getChannelPackageId()
            );
            $pi->appendChild(
                $doc->importNode($info, true)
            );
            // load the sorted releases
            $releases = $this->getReleases($channelPackage);
            // if releases are available
            if ($releases->size() > 0) {
                // create a new node for the releases
                $a = $doc->createElement('a');
                // iterate over the package's releases
                foreach ($releases as $release) {
                    // add a new node for every release
                    $r = $doc->createElement('r');
                    // add the releae's version
                    $v = $doc->createElement('v');
                    $v->nodeValue = $release->getVersion();
                    $r->appendChild($v);
                    // add the releae's stability
                    $s = $doc->createElement('s');
                    $s->nodeValue = $release->getStability();
                    $r->appendChild($s);
                    $a->appendChild($r);
                }
                // append the release
                $pi->appendChild($a);
                // iterate over the package's releases
                foreach ($releases as $release) {
                    // add a new node for every dependency
                    $deps = $doc->createElement('deps');
                    // add the dependency's version
                    $v = $doc->createElement('v');
                    $v->nodeValue = $release->getVersion();
                    $deps->appendChild($v);
                    // add the release's dependency as serialized array
                    $d = $doc->createElement('d');
                    $d->nodeValue = $release->getDependencies();
                    $deps->appendChild($d);
                    $pi->appendChild($deps);
                }
            }
	        // append the element with the channel's name to the root element
	        $f->appendChild($pi);
        }
        // append the root element to the DOM tree
        $doc->appendChild($f);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }

    /**
	 * Loads a simple version of the channel package information for
	 * importing it into the channel packages info.
	 *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
	 * @param TechDivision_Lang_Integer $channelPackageId The channel package ID
	 * @return DOMElement The element to import
     */
    public function getPackageInfo(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler,
        TechDivision_Lang_Integer $channelPackageId)
    {
        $serializer = new TDProject_Channel_Model_Serializer_ChannelPackage_Info();
        return $serializer->simple($assembler, $channelPackageId);
    }
}