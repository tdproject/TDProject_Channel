<?php

/**
 * TDProject_Channel_Model_Assembler_User
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
class TDProject_Channel_Model_Assembler_User
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
        return new TDProject_Channel_Model_Assembler_User($container);
    }

    /**
     * Return a Collection with user DTO extended with the
     * maintainer API-Hash if available.
     *
     * @param TechDivision_Lang_Integer $channelPackageId
     * 		The channel package to load the maintainer data for
     * @return TechDivision_Collections_ArrayList
     * 		The Collection with the user DTO's
     */
    public function getUserOverviewData(
        TechDivision_Lang_Integer $channelPackageId = null)
    {
        // load all users
        $users = TDProject_Core_Model_Utils_UserUtil::getHome($this->getContainer())
            ->findAll();
        // initialize an ArrayList for the DTO's
        $list = new TechDivision_Collections_ArrayList();
        // iterate over all found users
        foreach ($users as $user) {
            // initialize the DTO for the user
            $dto = new TDProject_Channel_Common_ValueObjects_UserOverviewData(
                $user
            );
            // check if a channel package ID has been passed
            if ($channelPackageId != null) {
                // load the maintainers
                $maintainer = TDProject_Channel_Model_Utils_MaintainerUtil::getHome($this->getContainer())
                    ->findByChannelPackageIdFkAndUserIdFk(
                        $channelPackageId,
                        $user->getUserId()
                    );
                // set the maintainer's API hash if available
                if ($maintainer != null) {
                    $dto->setHash($maintainer->getHash());
                }
            }
            // add the DTO to the ArrayList
            $list->add($dto);
        }
        // return the ArrayList
        return $list;
    }
}