<?php

/**
 * TDProject_Channel_Model_Serializer_ChannelPackage_Abstract
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
abstract class TDProject_Channel_Model_Serializer_ChannelPackage_Abstract
    extends TDProject_Channel_Model_Serializer_Abstract
{

	/**
	 * Holds the PCRE variable expression for the package name.
	 * @var string
	 */
	const PACKAGE_NAME = 'packageName';

    /**
     * REST namespace for a package ressource.
     * @var string
     */
    const REST_PACKAGE = 'rest.package';

    /**
     * REST namespace for the ressource with all package maintainers.
     * @var string
     */
    const REST_PACKAGEMAINTAINERS = 'rest.packagemaintainers';

    /**
     * REST namespace for the ressource the package list.
     * @var string
     */
    const REST_ALLPACKAGES = 'rest.allpackages';

    /**
     * The channel package instance.
     * @var TDProject_Channel_Model_Entities_ChannelPackage
     */
    protected $_channelPackage = null;

    /**
     * Array with the available namespaces for this ressource type
     * @var array
     */
    protected $_namespaces = array(
        self::REST_PACKAGE,
        self::REST_PACKAGEMAINTAINERS,
        self::REST_ALLPACKAGES
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
    public function getChannelPackageByName(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// load the requested resource URI
    	$resourceUri = $assembler->getResourceUri()->stringValue();
		// check if the serializers regex matches the resource URI
    	if (preg_match($this->getRegex(), $resourceUri, $params)) {
			// if yes, check if a package name is available
			if (array_key_exists(self::PACKAGE_NAME, $params)) {
				// load the package name
				$packageName = $params[self::PACKAGE_NAME];
				// set the package for the package name in the resource URI
				$this->setChannelPackage(
				    $assembler->getChannelPackageByName(
					    new TechDivision_Lang_String($packageName)
				    )
				);
			}
    	}
    }

    /**
     * Sets the channel package instance.
     *
     * @param TDProject_Channel_Model_Entities_ChannelPackage $channelPackage
     * 		The channel package instance to use
     * @return TDProject_Channel_Model_Serializer_Interfaces_Serializer
     * 		The serializer instance
     */
    public function setChannelPackage(
        TDProject_Channel_Model_Entities_ChannelPackage $channelPackage)
    {
        $this->_channelPackage = $channelPackage;
        return $this;
    }

    /**
     * Returns the channel package instance.
     *
     * @return TDProject_Channel_Model_Entities_ChannelPackage 
     * 		The channel package instance
     */
    public function getChannelPackage()
    {
        return $this->_channelPackage;
    }
}