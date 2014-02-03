<?php

/**
 * TDProject_Channel_Model_Assembler_ChannelPackage
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
class TDProject_Channel_Model_Assembler_ChannelPackage
    extends TDProject_Core_Model_Assembler_Abstract {

    /**
     * Factory method to create a new instance.
     *
     * @param TechDivision_Model_Interfaces_Container $container The container instance
     * @return TDProject_Channel_Model_Actions_Category
     * 		The requested instance
     */
    public static function create(TechDivision_Model_Interfaces_Container $container)
    {
        return new TDProject_Channel_Model_Assembler_ChannelPackage($container);
    }

    /**
     * Returns a DTO initialized with the data of the channel package
     * with the passed ID.
     *
     * @param TechDivision_Lang_Integer $channelPackageId
     * 		The ID of the channel package the VO has to be initialized for
     * @return TDProject_Channel_Common_ValueObjects_ChannelPackageViewData
     * 		The initialized DTO
     */
    public function getChannelPackageViewData(
    	TechDivision_Lang_Integer $channelPackageId = null)
    {
    	// check if a channel package ID was passed
    	if (!empty($channelPackageId)) { // if yes, load the package
    		$channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
    			->findByPrimaryKey($channelPackageId);
    	}
    	else {
        	// if not, initialize a new channel package
        	$channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
        		->epbCreate();
    	}
        // initialize the DTO
    	$dto = new TDProject_Channel_Common_ValueObjects_ChannelPackageViewData(
    	    $channelPackage
    	);
        // set the categories
    	$dto->setCategories(
    	    TDProject_Channel_Model_Assembler_Category::create($this->getContainer())
    	        ->getCategoryOverviewData()
    	);
        // set the channels
    	$dto->setChannels(
    	    TDProject_Channel_Model_Assembler_Channel::create($this->getContainer())
    	        ->getChannelOverviewData()
    	);
		// set the system users (extended with their maintainer API hash)
		$dto->setUsers(
			TDProject_Channel_Model_Assembler_User::create($this->getContainer())
				->getUserOverviewData($channelPackageId)
		);
		// set the user ID's of the related users (maintainers)
        foreach ($dto->getMaintainers() as $maintainer) {
        	// set the ID's of the related users
        	$dto->getUserIdFk()->add($maintainer->getUserIdFk()->intValue());
        }
        // set the available maintainer roles & states
        $dto->setMaintainerRoles($this->getMaintainerRoles());
        $dto->setMaintainerStates($this->getMaintainerStates());
    	// return the initialized DTO
    	return $dto;
    }

    /**
     * Returns an ArrayList with all channel packages assembled as LVO's.
     *
     * @return TechDivision_Collections_ArrayList
     * 		The requested channel package LVO's
     */
    public function getChannelPackageOverviewData()
    {
        // initialize a new ArrayList
        $list = new TechDivision_Collections_ArrayList();
        // load the channel packages
        $channelPackages = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
        	->findAll();
        // assemble the channel packages
        foreach ($channelPackages as $channelPackage) {
        	// add the DTO to the ArrayList
            $list->add($channelPackage->getLightValue());
        }
        // return the ArrayList with the channel package LVO's
        return $list;
    }

    /**
     * Returns a channel package releases, loaded from the GIT repository.
     *
     * @param TDProject_Channel_Model_Entities_ChannelPackage $channelPackage
     * @return array The channel package releases
     * @todo Still has to be implemented (only a prototype actually)
     */
    public function getReleases(
        TDProject_Channel_Model_Entities_ChannelPackage $channelPackage)
    {
    	// define the path to the GIT repository
        $repository = '/Users/wagnert/Workspace/' . $channelPackage->getName() . '/.git';
        // specify a directory
        $git = new VersionControl_Git($repository);
		// set the GIT binary
        $git->setGitCommandPath('/usr/local/git/bin/git');
		// load the releases and explode them into an array
        $releases = explode("\n", $git->getCommand('tag')->setOption('l')->execute());
		// return the array with the releases
        return $releases;
    }

    /**
     * Initialize and return a Collection with the roles
     * available to select for an maintainer.
     *
     * @return TechDivision_Collections_ArrayList
     * 		The ArrayList with the available maintainer roles
     */
  	public function getMaintainerRoles()
  	{
  		// initialize the ArrayList
  		$list = new TechDivision_Collections_ArrayList();
  		// load the maintainer roles
  		foreach (TDProject_Channel_Common_Enums_MaintainerRole::load() as $maintainerRole) {
	  		$list->add(
	  			new TDProject_Channel_Common_ValueObjects_MaintainerRoleOverviewData(
		  			$maintainerRole->toString()
		  		)
	  		);
  		}
  		// return the ArrayList
  		return $list;
  	}

    /**
     * Initialize and return a Collection with the roles
     * available to select for an maintainer.
     *
     * @return TechDivision_Collections_ArrayList
     * 		The ArrayList with the available maintainer roles
     */
  	public function getMaintainerStates()
  	{
  		// initialize the ArrayList
  		$list = new TechDivision_Collections_ArrayList();
  		// load the maintainer states
  		foreach (TDProject_Channel_Common_Enums_MaintainerState::load() as $maintainerState) {
	  		$list->add(
	  			new TDProject_Channel_Common_ValueObjects_MaintainerStateOverviewData(
		  			$maintainerState->toBoolean()
		  		)
	  		);
  		}
  		// return the ArrayList
  		return $list;
  	}
}