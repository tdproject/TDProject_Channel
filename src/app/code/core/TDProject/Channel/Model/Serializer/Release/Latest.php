<?php

/**
 * TDProject_Channel_Model_Serializer_Release_Latest
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
class TDProject_Channel_Model_Serializer_Release_Latest
    extends TDProject_Channel_Model_Serializer_Release_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/r\/(?P<packageName>\w+)\/latest\.txt/';

    /**
     * Sets the regular expression for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
     * Returns the collection with the channel package's releases.
     *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
     * @return TechDivision_Collections_ArrayList
     * 		The Collection with the releases
     */
    public function getCollection(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// load the channel package ID
    	$channelPackageId = $this->getChannelPackage()->getChannelPackageId();
        // load, sort and return the latest release's for the requested package
        $releases = TDProject_Channel_Model_Utils_ReleaseUtil::getHome($this->getContainer())
            ->findAllByChannelPackageIdFk($channelPackageId);
        $this->sortByVersionAscending($releases);
        return $releases;
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
    	// initialize the version
    	$version = new TechDivision_Lang_String();
    	// return the latest release version
        $iter = $this->getCollection($assembler)->getIterator();
		// load the last release
        if ($release = $iter->last()) {
        	$version = $release->getVersion();
        }
		// return the DTO with the version
        $dto =  new TDProject_Channel_Common_ValueObjects_ResourceViewData($version);
        return $dto->setContentType(new TechDivision_Lang_String('text/html'));
    }
}