<?php

/**
 * TDProject_Channel_Common_ValueObjects_ChannelPackageViewData
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * This class is the data transfer object between the
 * model and the controller for a package.
 *
 * @category   	TDProject
 * @package     TDProject_Channel
 * @subpackage	Common
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Common_ValueObjects_ChannelPackageViewData
    extends TDProject_Channel_Common_ValueObjects_ChannelPackageValue
    implements TechDivision_Model_Interfaces_Value {

    /**
     * The categories available in the system.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_categories = null;

    /**
     * The channels available in the system.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_channels = null;

    /**
     * The available system users.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_users = null;

    /**
     * The available package-user (mainainer) relations.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_userIdFk = null;

    /**
     * The available maintainer states.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_maintainerStates = null;

    /**
     * The available maintainer roles.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_maintainerRoles = null;

    /**
     * The selected maintainer states.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_maintainerState = null;

    /**
     * The selected maintainer roles.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_maintainerRole = null;

    /**
     * The constructor intializes the DTO with the
     * values passed as parameter.
     *
     * @param TDProject_Channel_Common_ValueObjects_ChannelPackageValue $vo
     * 		Holds the VO with the package data
     * @return void
     */
    public function __construct(
        TDProject_Channel_Common_ValueObjects_ChannelPackageValue $vo)
    {
        // call the parents constructor
        parent::__construct($vo);
        // initialize the ValueObject with the passed data
        $this->_categories = new TechDivision_Collections_ArrayList();
        $this->_channels = new TechDivision_Collections_ArrayList();
        $this->_users = new TechDivision_Collections_ArrayList();
        $this->_userIdFk = new TechDivision_Collections_ArrayList();
        $this->_maintainerStates = new TechDivision_Collections_ArrayList();
        $this->_maintainerRoles = new TechDivision_Collections_ArrayList();
        $this->_maintainerState = new TechDivision_Collections_ArrayList();
        $this->_maintainerRole = new TechDivision_Collections_ArrayList();
    }

    /**
     * Sets the available categories.
     *
     * @param TechDivision_Collections_Interfaces_Collection $categories
     * 		The categories available in the system
     * @return void
     */
    public function setCategories(
        TechDivision_Collections_Interfaces_Collection $categories)
    {
        $this->_categories = $categories;
    }

    /**
     * Returns the available categories.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The categories available in the system
     */
    public function getCategories()
    {
        return $this->_categories;
    }

    /**
    * Sets the available channels.
    *
    * @param TechDivision_Collections_Interfaces_Collection $channels
    * 		The channels available in the system
    * @return void
    */
    public function setChannels(
        TechDivision_Collections_Interfaces_Collection $channels)
    {
        $this->_channels = $channels;
    }

    /**
     * Returns the available channels.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The channels available in the system
     */
    public function getChannels()
    {
        return $this->_channels;
    }

    /**
     * Sets the available system users.
     *
     * @param TechDivision_Collections_Interfaces_Collection $users
     * 		The available system users
     * @return void
     */
    public function setUsers(
        TechDivision_Collections_Interfaces_Collection $users)
    {
        $this->_users = $users;
    }

    /**
     * Returns the available system users.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The available system users
     */
    public function getUsers()
    {
        return $this->_users;
    }

    /**
     * Sets the Collection with the package-user (maintainer) relations.
     *
     * @param TechDivision_Collections_Interfaces_Collection $userIdFk
     * 		The Collection with the package-user relations
     * @return void
     */
    public function setUserIdFk(
        TechDivision_Collections_Interfaces_Collection $userIdFk)
    {
        $this->_userIdFk = $userIdFk;
    }

    /**
     * Returns the Collection with the package-user (maintainer) relations.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The Collection with the package-user relations
     */
    public function getUserIdFk()
    {
        return $this->_userIdFk;
    }

    /**
     * Sets the available maintainer states.
     *
     * @param TechDivision_Collections_Interfaces_Collection $maintainerStates
     * 		The maintainer states available in the system
     * @return void
     */
    public function setMaintainerStates(
        TechDivision_Collections_Interfaces_Collection $maintainerStates)
    {
        $this->_maintainerStates = $maintainerStates;
    }

    /**
     * Returns the available maintainer states.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The maintainer states available in the system
     */
    public function getMaintainerStates()
    {
        return $this->_maintainerStates;
    }

    /**
     * Sets the available maintainer roles.
     *
     * @param TechDivision_Collections_Interfaces_Collection $maintainerRoles
     * 		The maintainer roles available in the system
     * @return void
     */
    public function setMaintainerRoles(
        TechDivision_Collections_Interfaces_Collection $maintainerRoles)
    {
        $this->_maintainerRoles = $maintainerRoles;
    }

    /**
     * Returns the available maintainer roles.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The maintainer roles available in the system
     */
    public function getMaintainerRoles()
    {
    	return $this->_maintainerRoles;
    }

    /**
     * Sets the selected maintainer states.
     *
     * @param TechDivision_Collections_Interfaces_Collection $maintainerState
     * 		The selected maintainer states
     * @return void
     */
    public function setMaintainerState(
    	TechDivision_Collections_Interfaces_Collection $maintainerState)
    {
    	$this->_maintainerStates = $maintainerState;
    }

    /**
     * Returns the selected maintainer states.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The selected maintainer states
     */
    public function getMaintainerState()
    {
    	return $this->_maintainerState;
    }

    /**
     * Sets the selected maintainer roles.
     *
     * @param TechDivision_Collections_Interfaces_Collection $maintainerRole
     * 		The selected maintainer roles
     * @return void
     */
    public function setMaintainerRole(
    	TechDivision_Collections_Interfaces_Collection $maintainerRole)
    {
    	$this->_maintainerRole = $maintainerRole;
    }

    /**
     * Returns the selected maintainer roles.
     *
    * @return TechDivision_Collections_Interfaces_Collection
    * 		The selected maintainer roles
    */
    public function getMaintainersRole()
    {
    	return $this->_maintainerRole;
    }
}