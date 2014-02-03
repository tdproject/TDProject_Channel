<?php

/**
 * TDProject_Channel_Model_Actions_Maintainer
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
class TDProject_Channel_Model_Actions_Maintainer
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
        return new TDProject_Channel_Model_Actions_Maintainer($container);
    }

	/**
	 * Creates the passed maintainer relation and allows
	 * project handling.
	 *
	 * @param TDProject_Channel_Common_ValueObjects_MaintainerLightValue $lvo
	 * 		The data to create the maintainer relation for
	 * @return void Nothing to return
	 */
	public function createAndAllow(
	    TDProject_Channel_Common_ValueObjects_MaintainerLightValue $lvo)
	{
		// load the maintainers relations
		$maintainers = TDProject_Channel_Model_Utils_MaintainerUtil::getHome($this->getContainer())
			->findAllByChannelPackageIdFkAndUserIdFk(
				$lvo->getChannelPackageIdFK(),
				$lvo->getUserIdFk()
			);
		// check a maintainer relation is already available
		if ($maintainers->size() == 1) {
			// update the maintainers the relations
			foreach ($maintainers as $maintainer) {
		        // set and save the data
		        $maintainer->setRole($lvo->getRole());
		        $maintainer->setActive($lvo->getActive());
		        // create the relation and return the ID
		        $maintainer->update();
		        // return the ID of the maintainer
		        return $maintainer->getMaintainerId();
			}
		}
		elseif ($maintainers->size() == 0) {
	        // initialize a new maintainer relation
	        $maintainer = TDProject_Channel_Model_Utils_MaintainerUtil::getHome($this->getContainer())
	            ->epbCreate();
            // load the channel package the maintainer has to be related with
            $channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
                ->findByPrimaryKey($lvo->getChannelPackageIdFk());
            // load the user the maintainer has to be related with
            $user = TDProject_Core_Model_Utils_UserUtil::getHome($this->getContainer())
                ->findByPrimaryKey($lvo->getUserIdFk());
	        // set and save the data
	        $maintainer->setChannelPackageIdFk($lvo->getChannelPackageIdFK());
	        $maintainer->setUserIdFk($lvo->getUserIdFk());
	        $maintainer->setUsername($user->getUsername());
	        $maintainer->setRole($lvo->getRole());
	        $maintainer->setActive($lvo->getActive());
            // set the hash value for API usage (upload)
	        $maintainer->setHash(
	            new TechDivision_Lang_String(
	                md5(
                        $user->getUsername() .
    	                $channelPackage->getName() .
    	                $channelPackage->getChannel()->getAlias()
    	            )
    	        )
	        );
	        // create the relation and return the ID
	        return $maintainer->create();
		}
		else {
			// throw an Exception here
		}
	}

	/**
	 * Deletes the passed maintainer relation and denys
	 * project handling.
	 *
	 * @param TDProject_Channel_Common_ValueObjects_MaintainerLightValue $lvo
	 * 		The data to create the relation for
	 * @return void
	 */
	public function deleteAndDeny(
	    TDProject_Channel_Common_ValueObjects_MaintainerLightValue $lvo) {
        // load the maintainers relations
        $maintainers = TDProject_Channel_Model_Utils_MaintainerUtil::getHome($this->getContainer())
	        ->findAllByChannelPackageIdFkAndUserIdFk(
	        	$lvo->getChannelPackageIdFK(),
	        	$lvo->getUserIdFk()
	        );
        // delete the relations
        foreach ($maintainers as $maintainer) {
        	$maintainer->delete();
        }
	}
}