<?php

/**
 * TDProject_Channel_Model_Serializer_Category_Abstract
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
abstract class TDProject_Channel_Model_Serializer_Category_Abstract
    extends TDProject_Channel_Model_Serializer_Abstract {

	/**
	 * Holds the PCRE variable expression for the category name.
	 * @var string
	 */
	const CATEGORY_NAME = 'categoryName';

    /**
     * REST namespace for a category ressource.
     * @var string
     */
    const REST_CATEGORY = 'rest.category';

    /**
     * REST namespace for ressource with the category list.
     * @var string
     */
    const REST_ALLCATEGORIES = 'rest.allcategories';

    /**
     * REST namespace for ressource with the category packages list.
     * @var string
     */
    const REST_CATEGORYPACKAGES = 'rest.categorypackages';

    /**
     * The category the serializer has to be attached to
     * @var TDProject_Channel_Model_Entities_Category
     */
    protected $_category = null;

    /**
     * Array with the available namespaces for this ressource type
     * @var array
     */
    protected $_namespaces = array(
        self::REST_CATEGORY,
        self::REST_ALLCATEGORIES,
        self::REST_CATEGORYPACKAGES
    );

    /**
     * Sets the category by the requested resource URI.
     *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
     * @return void
     */
    public function getCategoryByName(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// load the requested resource URI
    	$resourceUri = $assembler->getResourceUri()->stringValue();
		// check if the serializers regex matches the resource URI
    	if (preg_match($this->getRegex(), $resourceUri, $params)) {
			// if yes, check if a category name is available
			if (array_key_exists(self::CATEGORY_NAME, $params)) {
				// load the category name
				$categoryName = $params[self::CATEGORY_NAME];
				// set the category for the category name in the resource URI
				$this->setCategory(
				    $assembler->getCategoryByName(
					    new TechDivision_Lang_String($categoryName)
				    )
				);
			}
    	}
    }

    /**
     * Sets the category instance.
     *
     * @param TDProject_Channel_Model_Entities_Category $category
     * 		The category instance to use
     * @return TDProject_Channel_Model_Serializer_Interfaces_Serializer
     * 		The serializer instance
     */
    public function setCategory(
        TDProject_Channel_Model_Entities_Category $category)
    {
        $this->_category = $category;
        return $this;
    }

    /**
     * Returns the category the serializer has to be attached to.
     *
     * @return TDProject_Channel_Model_Entities_Category
     * 		The category itself
     */
    public function getCategory()
    {
        return $this->_category;
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