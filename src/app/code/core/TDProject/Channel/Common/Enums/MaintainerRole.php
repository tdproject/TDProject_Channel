<?php

/**
 * TDProject_Channel_Common_Enums_MaintainerRole
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * This class is the enum type for all maintainer roles available.
 *
 * @category   	TDProject
 * @package     TDProject_Channel
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Common_Enums_MaintainerRole
    extends TechDivision_Lang_Object
{
    
    /**
     * Lead role.
     * @var string
     */
    const LEAD = 'lead';
    
    /**
     * Developer role.
     * @var string
     */
    const DEVELOPER = 'developer';
    
    /**
     * Contributor role.
     * @var string
     */
    const CONTRIBUTOR = 'contributor';
    
    /**
     * Helper role.
     * @var string
     */
    const HELPER = 'helper';
    
    /**
     * The available maintainer roles.
     * @var array
     */
    protected static $_maintainerRoles = array(
    	self::LEAD, 
    	self::DEVELOPER, 
    	self::CONTRIBUTOR, 
    	self::HELPER
    );
    
    /**
     * The instance maintainer role value.
     * @var string
     */
    protected $_maintainerRole = '';
    
    /**
     * Protected constructor to avoid direct initialization.
     * 
     * @param string $maintainerRole
     * 		The requested maintainer role
     */
    protected function __construct($maintainerRole)
    {
    	$this->_maintainerRole = $maintainerRole;
    }
    
    /**
     * Factory method to create a new maintainer role 
     * instance for the requested value.
     * 
     * @param string $maintainerRole
     * 		The requested maintainer role
     * @return TDProject_Channel_Common_Enums_MaintainerRole
     * 		The requested maintainer role instance
     * @throws TDProject_Channel_Common_Exceptions_InvalidEnumException
     * 		Is thrown if the maintainer role with the requested value is not available
     */
    public static function create($maintainerRole)
    {
    	// check if the requested value is valid
    	if (in_array($maintainerRole, self::$_maintainerRoles)) {
    		return new TDProject_Channel_Common_Enums_MaintainerRole($maintainerRole);
    	}
    	// throw an exception if not
    	throw new TDProject_Channel_Common_Exceptions_InvalidEnumException(
    		'Invalid enum ' . $maintainerRole . ' requested'
    	);
    }
    
    /**
     * Returns an ArrayList with all available maintainer roles.
     * 
     * 
     * @return TechDivision_Collections_ArrayList
     * 		The ArrayList with all maintainer roles
     */
    public static function load()
    {
    	// initialize the ArrayList
    	$list = new TechDivision_Collections_ArrayList();
    	// load all maintainer roles
    	for ($i = 0; $i < sizeof(self::$_maintainerRoles); $i++) {
    		$list->add(self::create(self::$_maintainerRoles[$i]));	
    	}
    	// return the ArrayList
    	return $list;
    }
    
    /**
     * Returns the maintainer role's string value.
  	 *
     * @return string The maintainer role's value
     */
    public function getMaintainerRole()
    {
    	return $this->_maintainerRole;
    }
    
    /**
     * Returns the maintainer role's string value.
  	 *
     * @return string The maintainer role's value
     * @see TechDivision_Lang_Object::__toString()
     */
    public function __toString()
    {
    	return $this->getMaintainerRole();
    }
    
    /**
     * Returns the maintainer role's String value.
  	 *
     * @return TechDivision_Lang_String 
     * 		The maintainer role's value as String instance
     */
    public function toString()
    {
    	return new TechDivision_Lang_String($this->getMaintainerRole());
    }
}