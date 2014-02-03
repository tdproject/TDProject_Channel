<?php

/**
 * TDProject_Channel_Model_Actions_Channel
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
class TDProject_Channel_Model_Actions_Channel
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
        return new TDProject_Channel_Model_Actions_Channel($container);
    }

    /**
     * Save/Update the channel with the given values.
     *
     * @param TDProject_Channel_Common_ValueObjects_ChannelLightValue $lvo
     * 		The LVO with the channel data to save/update
     */
    public function saveChannel(
        TDProject_Channel_Common_ValueObjects_ChannelLightValue $lvo)
    {
        // look up channel ID
        $channelId = $lvo->getChannelId();
        // store the channel
        if ($channelId->equals(new TechDivision_Lang_Integer(0))) {
            // check for an existing channel name
            $channels = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
                ->findAllByName(
                    $name = $lvo->getName()
                );
            // check if a channel with the requested name already exists
            if ($channels->size() > 0) {
                throw new TDProject_Channel_Common_Exceptions_ChannelNameNotUniqueException(
            		'Channel name ' . $name . ' is not unique'
                );
            }
            // check for an existing channel alias
            $channels = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
                ->findAllByAlias(
                    $alias = $lvo->getAlias()
                );
            // check if a channel with the requested alias already exists
            if ($channels->size() > 0) {
                throw new TDProject_Channel_Common_Exceptions_ChannelAliasNotUniqueException(
            		'Channel alias ' . $alias . ' is not unique'
                );
            }
            // create a new channel
            $channel = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
                ->epbCreate();
            // set the data
            $channel->setName($lvo->getName());
            $channel->setAlias($lvo->getAlias());
            $channel->setSummary($lvo->getSummary());
            $channel->setCreatePackage($lvo->getCreatePackage());
            $channel->setAllowedNetworks($lvo->getAllowedNetworks());
            $channel->setAllowedIps($lvo->getAllowedIps());
            $channelId = $channel->create();
        }
        else {
            // update the channel
            $channel = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
                ->findByPrimaryKey($channelId);
            $channel->setName($lvo->getName());
            $channel->setAlias($lvo->getAlias());
            $channel->setSummary($lvo->getSummary());
            $channel->setCreatePackage($lvo->getCreatePackage());
            $channel->setAllowedNetworks($lvo->getAllowedNetworks());
            $channel->setAllowedIps($lvo->getAllowedIps());
            $channel->update();
        }
        return $channelId;
    }
}