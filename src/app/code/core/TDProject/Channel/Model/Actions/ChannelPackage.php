<?php

/**
 * TDProject_Channel_Model_Actions_ChannelPackage
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
class TDProject_Channel_Model_Actions_ChannelPackage
    extends TDProject_Core_Model_Actions_Abstract {

    /**
     * Factory method to create a new instance.
     *
     * @param TechDivision_Model_Interfaces_Container $container The container instance
     * @return TDProject_Channel_Model_Actions_Category
     * 		The requested instance
     */
    public static function create(TechDivision_Model_Interfaces_Container $container)
    {
        return new TDProject_Channel_Model_Actions_ChannelPackage($container);
    }

    /**
     * Create/Update the package with the given values.
     *
     * @param TDProject_Channel_Common_ValueObjects_ChannelPackageLightValue $lvo
     * 		The channel package data to save
     * @return TechDivision_Lang_Integer 
     * 		The ID of the created/updated channel package
     */
    public function saveChannelPackage(
        TDProject_Channel_Common_ValueObjects_ChannelPackageLightValue $lvo)
    {
        // look up channel package ID
        $channelPackageId = $lvo->getChannelPackageId();
        // store the channel package
        if ($channelPackageId->equals(new TechDivision_Lang_Integer(0))) {
            // create a new channel package
            $channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
                ->epbCreate();
            // set the data
            $channelPackage->setChannelIdFk($lvo->getChannelIdFk());
            $channelPackage->setCategoryIdFk($lvo->getCategoryIdFk());
            $channelPackage->setName($lvo->getName());
            $channelPackage->setLicence($lvo->getLicence());
            $channelPackage->setLicenceUri($lvo->getLicenceUri());
            $channelPackage->setShortDescription($lvo->getShortDescription());
            $channelPackage->setDescription($lvo->getDescription());
            $channelPackage->setDeprecated($lvo->getDeprecated());
            $channelPackage->setDeprecatedChannel($lvo->getDeprecatedChannel());
            $channelPackage->setDeprecatedPackage($lvo->getDeprecatedPackage());
            $channelPackageId = $channelPackage->create();
        } else {
            // update the channel package
            $channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
                ->findByPrimaryKey($channelPackageId);
            $channelPackage->setChannelIdFk($lvo->getChannelIdFk());
            $channelPackage->setCategoryIdFk($lvo->getCategoryIdFk());
            $channelPackage->setName($lvo->getName());
            $channelPackage->setLicence($lvo->getLicence());
            $channelPackage->setLicenceUri($lvo->getLicenceUri());
            $channelPackage->setShortDescription($lvo->getShortDescription());
            $channelPackage->setDescription($lvo->getDescription());
            $channelPackage->setDeprecated($lvo->getDeprecated());
            $channelPackage->setDeprecatedChannel($lvo->getDeprecatedChannel());
            $channelPackage->setDeprecatedPackage($lvo->getDeprecatedPackage());
            $channelPackage->update();
        }
        return $channelPackageId;
    }

    /**
     * Creates a new channel package in the channel with the passed ID, based
     * on the information from the also passed binary release.
     * 
     * @param TechDivision_Lang_Integer $channelId
     * 		ID of the channel to create the channel package for
     * @param PEAR_PackageFile_v2 $pf
     * 		The binary release with the channel package information to use
     * @return TechDivision_Lang_Integer The ID of the created channel package
     * @throws TDProject_Channel_Common_Exceptions_InvalidChannelException
     * 		Is thrown if the channel package channel doesn't match the actual channel
     */
    public function createFromBinary(
    	TechDivision_Lang_Integer $channelId,
    	PEAR_PackageFile_v2 $pf)
    {
    	// try to load the channel
    	$channel = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
    		->findByPrimaryKey($channelId);
    	// check if package channel matches the actual channel
    	if (!$channel->getName()->equals(new TechDivision_Lang_String($pf->getChannel()))) {
    		throw new TDProject_Channel_Common_Exceptions_InvalidChannelException(
    			"Package channel {$pf->getChannel()} doesn't match channel {$channel->getName()}"
    		);
    	}
    	// load the channel's default category
    	$category = TDProject_Channel_Model_Assembler_Category::create($this->getContainer())
    		->getDefaultCategory($channelId);
    	// create a new channel package
    	$channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
    		->epbCreate();
    	// set the data
    	$channelPackage->setChannelIdFk($channelId);
    	$channelPackage->setCategoryIdFk($category->getCategoryId());
    	$channelPackage->setName(new TechDivision_Lang_String($pf->getName()));
    	$channelPackage->setLicence(new TechDivision_Lang_String($pf->getLicense()));
    	$channelPackage->setShortDescription(new TechDivision_Lang_String($pf->getSummary()));
    	$channelPackage->setDescription(new TechDivision_Lang_String($pf->getDescription()));
    	$channelPackage->setDeprecated(new TechDivision_Lang_Boolean(false));        
        // set the licence URI if available
       	if (is_array($loc = $pf->getLicenseLocation())) {	
       		if (array_key_exists('uri', $loc)) {
       			$channelPackage->setLicenceUri(new TechDivision_Lang_String($loc['uri']));
       		} 
       		elseif (array_key_exists('filesource', $loc)) {
       			$channelPackage->setLicenceUri(new TechDivision_Lang_String($loc['filesource']));
       		}
       	}
        // create the channel package and return the ID
        return $channelPackage->create();
    }
}