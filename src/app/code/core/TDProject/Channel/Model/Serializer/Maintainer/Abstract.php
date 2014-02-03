<?php

/**
 * TDProject_Channel_Model_Serializer_Maintainer_Abstract
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
abstract class TDProject_Channel_Model_Serializer_Maintainer_Abstract
    extends TDProject_Channel_Model_Serializer_Abstract {

	/**
	 * Holds the PCRE variable expression for the username.
	 * @var string
	 */
	const USERNAME = 'username';

    /**
     * REST namespace for a maintainer ressource.
     * @var string
     */
    const REST_MAINTAINER = 'rest.maintainer';

    /**
     * REST namespace for ressource with the maintainer list.
     * @var string
     */
    const REST_ALLMAINTAINERS = 'rest.allmaintainers';

    /**
     * The user the serializer has to be attached to
     * @var TDProject_Core_Model_Entities_User
     */
    protected $_user = null;

    /**
     * Array with the available namespaces for this ressource type
     * @var array
     */
    protected $_namespaces = array(
        self::REST_MAINTAINER,
        self::REST_ALLMAINTAINERS
    );

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::getNamespaces()
     */
    public function getNamespaces()
    {
        return $this->_namespaces;
    }

    /**
     * Sets the package by the requested resource URI.
     *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
     * @return void
     */
    public function getUserByUsername(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// load the requested resource URI
    	$resourceUri = $assembler->getResourceUri()->stringValue();
		// check if the serializers regex matches the resource URI
    	if (preg_match($this->getRegex(), $resourceUri, $params)) {
			// if yes, check if a username is available
			if (array_key_exists(self::USERNAME, $params)) {
				// load the username
				$username = $params[self::USERNAME];
				// set the user for the username in the resource URI
				$this->setUser(
				    $assembler->getUserByUsername(
					    new TechDivision_Lang_String($username)
				    )
				);
			}
    	}
    }

    /**
     * Sets the user instance.
     *
     * @param TDProject_Core_Model_Entities_User $maintainer
     * 		The user instance to use
     * @return TDProject_Channel_Model_Serializer_Interfaces_Serializer
     * 		The serializer instance
     */
    public function setUser(
        TDProject_Core_Model_Entities_User $user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * Returns the user instance.
     *
     * @return TDProject_Core_Model_Entities_User The user instance
     */
    public function getUser()
    {
        return $this->_user;
    }
}