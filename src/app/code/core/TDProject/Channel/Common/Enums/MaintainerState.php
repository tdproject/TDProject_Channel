<?php

/**
 * TDProject_Channel_Common_Enums_MaintainerState
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * This class is the enum type for all maintainer states available.
 *
 * @category   	TDProject
 * @package     TDProject_Channel
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Common_Enums_MaintainerState
    extends TechDivision_Lang_Object
{

    /**
     * Active state.
     * @var boolean
     */
    const ACTIVE = true;

    /**
     * Inactive state.
     * @var boolean
     */
    const INACTIVE = false;

    /**
     * The available maintainer states.
     * @var array
     */
    protected static $_maintainerStates = array(
    	self::INACTIVE,
    	self::ACTIVE
    );

    /**
     * The instance maintainer state value.
     * @var boolean
     */
    protected $_maintainerState = false;

    /**
     * Protected constructor to avoid direct initialization.
     *
     * @param string $maintainerState
     * 		The requested maintainer stae
     */
    protected function __construct($maintainerState)
    {
    	$this->_maintainerState = $maintainerState;
    }

    /**
     * Factory method to create a new maintainer state
     * instance for the requested value.
     *
     * @param string $maintainerState
     * 		The requested maintainer state
     * @return TDProject_Channel_Common_Enums_MaintainerState
     * 		The requested maintainer state instance
     * @throws TDProject_Channel_Common_Exceptions_InvalidEnumException
     * 		Is thrown if the maintainer state with the requested value is not available
     */
    public static function create($maintainerState)
    {
    	// check if the requested value is valid
    	if (in_array($maintainerState, self::$_maintainerStates)) {
    		return new TDProject_Channel_Common_Enums_MaintainerState($maintainerState);
    	}
    	// throw an exception if not
    	throw new TDProject_Channel_Common_Exceptions_InvalidEnumException(
    		'Invalid enum ' . $maintainerState . ' requested'
    	);
    }

    /**
     * Returns an ArrayList with all available maintainer states.
     *
     *
     * @return TechDivision_Collections_ArrayList
     * 		The ArrayList with all maintainer states
     */
    public static function load()
    {
    	// initialize the ArrayList
    	$list = new TechDivision_Collections_ArrayList();
    	// load all maintainer states
    	for ($i = 0; $i < sizeof(self::$_maintainerStates); $i++) {
    		$list->add(self::create(self::$_maintainerStates[$i]));
    	}
    	// return the ArrayList
    	return $list;
    }

    /**
     * Returns the maintainer state's string value.
  	 *
     * @return string The maintainer state's value
     */
    public function getMaintainerState()
    {
    	return $this->_maintainerState;
    }

    /**
     * Returns the maintainer state's string value.
  	 *
     * @return string The maintainer state's value
     * @see TechDivision_Lang_Object::__toString()
     */
    public function __toString()
    {
    	return $this->getMaintainerState();
    }

    /**
     * Returns the maintainer state's String value.
  	 *
     * @return TechDivision_Lang_String
     * 		The maintainer state's value as String instance
     */
    public function toBoolean()
    {
    	return new TechDivision_Lang_Boolean($this->getMaintainerState());
    }
}