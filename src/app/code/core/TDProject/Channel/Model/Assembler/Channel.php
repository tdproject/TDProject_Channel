<?php

/**
 * TDProject_Channel_Model_Assembler_Channel
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
class TDProject_Channel_Model_Assembler_Channel
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
        return new TDProject_Channel_Model_Assembler_Channel($container);
    }

    /**
     * Returns a DTO initialized with the data of the channel
     * with the passed ID.
     *
     * @param TechDivision_Lang_Integer $channelId
     * 		The ID of the channel the VO has to be initialized for
     * @return TDProject_Channel_Common_ValueObjects_ChannelValue
     * 		The initialized VO
     */
    public function getChannelViewData(TechDivision_Lang_Integer $channelId = null) {
    	// check if a channel ID was passed
    	if(!empty($channelId)) { // if yes, load the channel
    		$channel = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
    			->findByPrimaryKey($channelId);
    	} else {
        	// if not, initialize a new channel
        	$channel = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
        		->epbCreate();
    	}
    	// return the initialized VO
    	return $channel->getValue();
    }

    /**
     * Returns an ArrayList with all channel assembled as LVO's.
     *
     * @return TechDivision_Collections_ArrayList
     * 		The requested channel LVO's
     */
    public function getChannelOverviewData()
    {
        // initialize a new ArrayList
        $list = new TechDivision_Collections_ArrayList();
        // load the channels
        $channels = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())->findAll();
        // assemble the channels
        foreach ($channels as $channel) {
        	// add the DTO to the ArrayList
            $list->add(
                new TDProject_Channel_Common_ValueObjects_ChannelOverviewData(
                    $channel
                )
            );
        }
        // return the ArrayList with the channel LVO's
        return $list;
    }
}