<?php

/**
 * TDProject_Channel_Model_Assembler_Channel_Api
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
class TDProject_Channel_Model_Assembler_Channel_Api
    extends TDProject_Core_Model_Assembler_Abstract {

	/**
	 * The ArrayList with the available serializers.
	 * @var TechDivision_Collections_ArrayList
	 */
	protected $_serializers = null;

    /**
     * The object factory instance.
     * @var TDProject_Factory_Object
     */
    protected $_objectFactory = null;

    /**
     * The actual channel.
     * @var TDProject_Channel_Model_Entities_Channel
     */
    protected $_channel = null;

    /**
     * The requested resource URI.
     * @var TechDivision_Lang_String
     */
    protected $_resourceUri = null;

    /**
     * The base URL.
     * @var TechDivision_Lang_String
     */
    protected $_baseUrl = null;

    /**
     * Protected constructor to prevent class
     * from direct instanciation.
	 *
     * @param TechDivision_Model_Interfaces_Container $container The container instance
	 * @return void
     */
    public function __construct(TechDivision_Model_Interfaces_Container $container)
    {
        // pass the container to the parent instance
        parent::__construct($container);
    	// initialize the ArrayList for the serializers
    	$this->_serializers = new TechDivision_Collections_ArrayList();
        // initialize the object factory
        $this->_objectFactory = $this->getContainer()->getObjectFactory();
    }

    /**
     * Factory method to create a new instance.
     *
     * @param TechDivision_Model_Interfaces_Container $container The container instance
     * @return TDProject_Channel_Model_Actions_Category
     * 		The requested instance
     */
    public static function create(TechDivision_Model_Interfaces_Container $container)
    {
        $instance = new TDProject_Channel_Model_Assembler_Channel_Api($container);
        return $instance->initialize();
    }

    /**
     * Sets the channel instance.
     *
     * @param TDProject_Channel_Model_Entities_Channel The channel instance
     * @return TDProject_Channel_Model_Assembler_Channel_Api The instance itself
     */
    public function setChannel(
    	TDProject_Channel_Model_Entities_Channel $channel)
    {
    	$this->_channel = $channel;
    	return $this;
    }

    /**
	 * Sets the requested resource URI.
	 *
	 * @param TechDivision_Lang_String $resourceUri
	 * 		The URI of the requested resource
     * @return TDProject_Channel_Model_Assembler_Channel_Api The instance itself
     */
    public function setResourceUri(TechDivision_Lang_String $resourceUri)
    {
    	$this->_resourceUri = $resourceUri;
    	return $this;
    }

    /**
	 * Returns the URI of the requested resource.
	 *
	 * @return TechDivision_Lang_String The resource URI
     */
    public function getResourceUri()
    {
    	return $this->_resourceUri;
    }

    /**
	 * Sets the base URL.
	 *
	 * @param TechDivision_Lang_String $baseUrl
	 * 		The base URL
     * @return TDProject_Channel_Model_Assembler_Channel_Api The instance itself
     */
    public function setBaseUrl(TechDivision_Lang_String $baseUrl)
    {
    	$this->_baseUrl = $baseUrl;
    	return $this;
    }

    /**
	 * Returns the base URL.
	 *
	 * @return TechDivision_Lang_String The base URL
     */
    public function getBaseUrl()
    {
    	return $this->_baseUrl;
    }

    /**
     * Returns the channel instance.
     *
     * @return TDProject_Channel_Model_Entities_Channel
     * 		The channel instance
     */
    public function getChannel()
    {
    	return $this->_channel;
    }

    /**
     * Returns the available serializers.
     *
     * @return TechDivision_Collections_ArrayList
     * 		The ArrayList with the serializers
     */
    public function getSerializers()
    {
    	return $this->_serializers;
    }

    /**
     * Loads and initializes the ArrayList with serializers.
     *
     * @return TDProject_Channel_Model_Assembler_Channel_Api
     * 		The instance itself
     */
    public function initialize()
    {
        // create the directory iterator
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(getcwd())
        );
        // iterate over the directory recursively and look for configurations
        while ($iterator->valid()) {
        	// if a configuration file was found
            if (basename($iterator->getSubPathName()) == 'serializers.xml') {
            	// initialize the SimpleXMLElement with the content of serializer XML file
                $sxe = new SimpleXMLElement(
                	file_get_contents($iterator->getSubPathName(), true)
                );
                // iterate over the found nodes
                foreach ($sxe->xpath('/serializers/serializer') as $child) {
                	// initialize the serializer
                    $serializer = $this->getObjectFactory()
                     	->newInstance((string) $child->serializerType)
                        ->setContainer($this->getContainer());
                    // add the serializer to the ArrayList
                    $this->_serializers->add($serializer);
                }
            }
            // proceed with the next folder
            $iterator->next();
        }
        // the instance itself
        return $this;
    }

    /**
     * Generates and returns the channel.xml for the the channel
     * with the passed ID.
     *
     * @param TechDivision_Lang_String $baseUrl
     * 		The base URL
     * @param TechDivision_Lang_Integer $channelId
     * 		The ID of the channel to return the XML for
     * @param TechDivision_Lang_String $resourceUri
     * 		The PEAR resource URI
     * @return TDProject_Channel_Common_ValueObjects_ResourceViewData
     * 		The DTO with the resource data
     */
    public function getResourceViewData(
        TechDivision_Lang_String $baseUrl,
        TechDivision_Lang_Integer $channelId,
        TechDivision_Lang_String $resourceUri)
    {
        // load and set the channel itself
    	$this->setChannel(
    		TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
    			->findByPrimaryKey($channelId)
    		);
    	// set the resource URI
    	$this->setResourceUri($resourceUri);
    	// set the base URL
    	$this->setBaseUrl($baseUrl);
		// load the iterator to process the resource URI
    	$iterator = $this->getSerializers()->getIterator();
		// load the number of found serializers
    	$serializersFound = $this->getSerializers()->size();
		// iterate over all serializers
    	while ($iterator->valid()) {
			// load the serializer instance
    		$serializer = $iterator->current();
			// check if the resource URI matches the serializer instance
    		if ($serializer->matches($resourceUri)) {
    			// if yes serialize the resource URI
    			return $serializer->serialize($this);
    		}
			// if not proceed with the next serializer
    		$iterator->next();
    	}
        // throw an exception if NO serializer for the resource URI is available
    	throw new TDProject_Channel_Common_Exceptions_InvalidResourceUri(
            'Found no serializer for resource URI ' . $resourceUri
    	);
    }

    /**
     * Returns the channel package with the passed ID.
     *
     * @param TechDivision_Lang_Integer $channelPackageId 
     * 		The ID of the requested channel package
     * @return TDProject_Channel_Model_Entities_ChannelPackage 
     * 		The requested channel package
     */
    public function getChannelPackageByChannelPackageId(
    	TechDivision_Lang_Integer $channelPackageId)
    {
   		return TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
    		->findByPrimaryKey($channelPackageId);
    }

    /**
     * Returns the channel package for the passed channel and category ID.
     *
     * @param TechDivision_Lang_Integer $channelId
     * 		The ID of the channel for the requested packages
     * @param TechDivision_Lang_Integer $categoryId
     * 		The ID of the category for the requested packages
     * @return TDProject_Channel_Model_Entities_ChannelPackage
     * 		The requested channel packages
     */
    public function getChannelPackageByChannelIdAndCategoryId(
        TechDivision_Lang_Integer $channelId,
        TechDivision_Lang_Integer $categoryId)
    {
        return TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
            ->findAllByChannelIdFkAndCategoryIdFk($channelId, $categoryId);
    }

    /**
     * Returns the channel package by the unique channel package name.
     *
     * @param TechDivision_Lang_String $name 
     * 		The name of the requested channel package
     * @return TDProject_Channel_Model_Entities_ChannelPackage 
     * 		The requested channel package
     */
    public function getChannelPackageByName(TechDivision_Lang_String $name)
    {
   		return TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
    		->findByName($name);
    }

    /**
     * Returns the user with the passed ID.
     *
     * @param TechDivision_Lang_Integer $userId The ID of the user to return
     * @return TDProject_Core_Model_Entities_User The user instance
     */
    public function getUserByUserId(TechDivision_Lang_Integer $userId)
    {
   		return TDProject_Core_Model_Utils_UserUtil::getHome($this->getContainer())
    		->findByPrimaryKey($userId);
    }

    /**
     * Returns the user by the unique username.
     *
     * @param TechDivision_Lang_String $username The name of the requested user
     * @return TDProject_Core_Model_Entities_User The requested user
     */
    public function getUserByUsername(TechDivision_Lang_String $username)
    {
   	    return TDProject_Core_Model_Utils_UserUtil::getHome($this->getContainer())
    		->findByUsername($username);
    }

    /**
     * Returns the category by the unique category name.
     *
     * @param TechDivision_Lang_String $categoryName
     * 		The name of the requested category
     * @return TDProject_Channel_Model_Entities_Category The requested category
     */
    public function getCategoryByName(TechDivision_Lang_String $categoryName)
    {
   	    return TDProject_Channel_Model_Utils_CategoryUtil::getHome($this->getContainer())
    		->findByName($categoryName);
    }

    /**
     * Returns the release for the passed channel package name and version.
     *
     * @param TechDivision_Lang_String $packageName
     * 		The package name to return the release for
     * @param TechDivision_Lang_String $version
     * 		The version to return the release for
     * @return TDProject_Channel_Model_Entities_Release
     * 		The release
     */
    public function getReleaseByChannelPackageNameAndVersion(
        TechDivision_Lang_String $packageName,
        TechDivision_Lang_String $version)
    {
        // load the package release
        return TDProject_Channel_Model_Utils_ReleaseUtil::getHome($this->getContainer())
            ->findByChannelPackageNameAndVersion($packageName, $version);
    }

	/**
	 * Loads the media directory from the system settings.
	 *
	 * @return string The path to the media directory
	 */
	public function getMediaDirectory()
	{
		// initialize a new LocalHome to load the settings
		$settings = TDProject_Core_Model_Utils_SettingUtil::getHome($this->getContainer())
			->findAll();
		// return the directory for storing media data
		foreach ($settings as $setting) {
			return $setting->getMediaDirectory();
		}
	}

	/**
	 * Returns the object factory.
	 *
	 * @return TDProject_Factory_Object The object factory
	 */
	public function getObjectFactory()
	{
	    return $this->_objectFactory;
	}
}