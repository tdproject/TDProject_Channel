<?php

/**
 * TDProject_Channel_Model_Serializer_Release_Abstract
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
abstract class TDProject_Channel_Model_Serializer_Release_Abstract
    extends TDProject_Channel_Model_Serializer_Abstract
{

	/**
	 * Holds the PCRE variable expression for the package name.
	 * @var string
	 */
	const PACKAGE_NAME = 'packageName';

	/**
	 * Holds the PCRE variable expression for the version.
	 * @var string
	 */
	const VERSION = 'version';

	/**
	 * Holds the PCRE variable expression for the stability.
	 * @var string
	 */
	const STABILITY = 'stability';

	/**
	 * Holds the PCRE variable expression for the build number.
	 * @var string
	 */
	const BUILD = 'build';

    /**
     * REST namespace for ressource with the releases list.
     * @var string
     */
    const REST_ALLRELEASES = 'rest.allreleases';

    /**
     * REST namespace for a release ressource.
     * @var string
     */
    const REST_RELEASE = 'rest.release';

    /**
     * The channel package the serializer has to be attached to
     * @var Faett_Channel_Model_ChannelPackage
     */
    protected $_channelPackage = null;

    /**
     * The release the serializer has to be attached to
     * @var Faett_Channel_Model_Release
     */
    protected $_release = null;

    /**
     * Array with the available namespaces for this ressource type
     * @var array
     */
    protected $_namespaces = array(
        self::REST_ALLRELEASES,
        self::REST_RELEASE
    );

    /**
     * Sets the channel package by the requested resource URI.
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
     * Sets the channel package by the requested resource URI.
     *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
     * @return void
     */
    public function getReleaseByChannelPackageNameAndVersion(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// load the requested resource URI
    	$resourceUri = $assembler->getResourceUri()->stringValue();
		// check if the serializers regex matches the resource URI
    	if (preg_match($this->getRegex(), $resourceUri, $params)) {
			// if yes, check if a package name is available
			if (array_key_exists(self::PACKAGE_NAME, $params) &&
			    array_key_exists(self::VERSION, $params)) {
				// load the package name/version
				$packageName = $params[self::PACKAGE_NAME];
				$version = $params[self::VERSION];
				$stability = $params[self::STABILITY];
				$build = $params[self::BUILD];
				// set the package for the package name/version in the resource URI
				$this->setRelease(
				    $assembler->getReleaseByChannelPackageNameAndVersion(
					    new TechDivision_Lang_String($packageName),
					    new TechDivision_Lang_String($version . $stability . $build)
				    )
				);
			}
    	}
    }

    /**
     * Callback method used to sort PHP/PEAR version numbers ascending with PHP usort().
     *
     * @param TDProject_Channel_Model_Entities_Release $release1 First release to compare
     * @param TDProject_Channel_Model_Entities_Release $release2 Second release to compare
     * @return integer -1 if the first version is lower than the second, 0 if they are equal, and 1 if the second is lower
     * @link http://us.php.net/usort
     */
    public function sortByVersionAscending($releases)
    {
        $comparator = new TDProject_Channel_Model_Serializer_Release_AscendingComparator();
        TechDivision_Collections_CollectionUtils::sort($releases, $comparator);
    }

    /**
     * Callback method used to sort PHP/PEAR version numbers descending with PHP usort().
     *
     * @param TDProject_Channel_Model_Entities_Release $release1 First release to compare
     * @param TDProject_Channel_Model_Entities_Release $release2 Second release to compare
     * @return integer 1 if the first version is lower than the second, 0 if they are equal, and -1 if the second is lower
     * @link http://us.php.net/usort
     */
    public function sortByVersionDescending($releases)
    {
        $comparator = new TDProject_Channel_Model_Serializer_Release_DescendingComparator();
        TechDivision_Collections_CollectionUtils::sort($releases, $comparator);
    }

    /**
     * Sets the channel package instance.
     *
     * @param TDProject_Channel_Model_Entities_ChannelPackage $channelPackage
     * 		The package instance to use
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

    /**
     * Sets the release instance.
     *
     * @param TDProject_Channel_Model_Entities_Release $release
     * 		The release instance to use
     * @return TDProject_Channel_Model_Serializer_Interfaces_Serializer
     * 		The serializer instance
     */
    public function setRelease(
        TDProject_Channel_Model_Entities_Release $release)
    {
        $this->_release = $release;
        return $this;
    }

    /**
     * Returns the release instance.
     *
     * @return TDProject_Channel_Model_Entities_Release The package instance
     */
    public function getRelease()
    {
        return $this->_release;
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::getNamespaces()
     */
    public function getNamespaces()
    {
        return $this->_namespaces;
    }
}