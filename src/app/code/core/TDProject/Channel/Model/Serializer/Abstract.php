<?php

/**
 * TDProject_Channel_Model_Serializer_Abstract
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
abstract class TDProject_Channel_Model_Serializer_Abstract
    implements TDProject_Channel_Model_Serializer_Interfaces_Serializer {

    /**
     * The XSD namespace to use for  rendering the ressource
     * @var string
     */
    protected $_namespace = '';

    /**
     * The PCRE regex to check if the resource URI matches the serializer.
     * @var string
     */
    protected $_regex = '';

    /**
     * The actual channel instance to use.
     * @var TDProject_Channel_Model_Entities_Channel
     */
    protected $_channel = null;
    
    /**
     * The container instance.
     * @var TechDivision_Model_Interfaces_Container
     */
    protected $_container = null;

    /**
     * Sets the PCRE regex to check if the resource URI
     * matches the serializer.
     *
     * @param string $regex The PCRE regex to set
     * @return
     */
    public function setRegex($regex)
    {
    	$this->_regex = $regex;
    	return $this;
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::getRegex()
     */
    public function getRegex()
    {
    	return $this->_regex;
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::matches()
     */
    public function matches(TechDivision_Lang_String $resourceUri)
    {
    	// check if the passed resource URI matches the serializers PCRE regex
		if (preg_match($this->getRegex(), $resourceUri->stringValue())) {
			// if yes, return TRUE
			return true;
		}
		// return FALSE instead
		return false;
    }

    /**
     * Sets the namespace to use for rendering the ressource and
     * checks if the passed namespace is valid for the requested
     * ressource.
     *
     * @param string $namespace The namespace to set
     * @return void
     * @throws TDProject_Channel_Common_Exceptions_InvalidNamespaceException
     * 		Is thrown if an invalid namespace was passed
     */
    public function setNamespace($namespace)
    {
        // check if the namespace is valid
        if (!in_array($namespace, $this->getNamespaces())) {
            throw TDProject_Channel_Common_Exceptions_InvalidNamespaceException::create(
            	"Invalid namespace $namespace"
            );
        }
        // initialize the namespace
        $this->_namespace = new TechDivision_Lang_String(
        	"http://pear.php.net/dtd/$namespace"
        );
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::getNamespace()
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * Sets the actual channel instance.
     *
     * @param TDProject_Channel_Model_Entities_Channel $channel
     * 		The channel instance to use
     * @return TDProject_Channel_Model_Serializer_Interfaces_Serializer
     * 		The serializer instance
     */
    public function setChannel(
        TDProject_Channel_Model_Entities_Channel $channel)
    {
        $this->_channel = $channel;
        return $this;
    }

    /**
     * Returns the actual channel instance.
     *
     * @return TDProject_Channel_Model_Entities_Channel The channel instance
     */
    public function getChannel()
    {
        return $this->_channel;
    }
    
    /**
     * Sets the container instance.
     * 
     * @param TechDivision_Model_Interfaces_Container $container
     *     The container instance
     * @return TDProject_Channel_Model_Serializer_Interfaces_Serializer The instance itself
     */
    public function setContainer(TechDivision_Model_Interfaces_Container $container)
    {
        $this->_container = $container;
        return $this;
    }
    
    /**
     * Returns the container instance.
     * 
     * @return TechDivision_Model_Interfaces_Container
     *     The container instance
     */
    public function getContainer()
    {
        return $this->_container;
    }
}