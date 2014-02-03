<?php

/**
 * TDProject_Channel_Model_Actions_Category
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
class TDProject_Channel_Model_Actions_Category
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
        return new TDProject_Channel_Model_Actions_Category($container);
    }
    
    /**
     * Save/Update the category with the given values.
     * 
     * @param TDProject_Channel_Common_ValueObjects_CategoryLightValue $lvo
     * 		The LVO with the category data to save/update
     */
    public function saveCategory(
        TDProject_Channel_Common_ValueObjects_CategoryLightValue $lvo)
    {
        // look up category ID
        $categoryId = $lvo->getCategoryId();
        // store the category
        if ($categoryId->equals(new TechDivision_Lang_Integer(0))) {
            // check for an existing category name
            $categories = TDProject_Channel_Model_Utils_CategoryUtil::getHome($this->getContainer())
                ->findAllByName(
                    $name = $lvo->getName()
                );
            // check if a category with the requested name already exists
            if ($categories->size() > 0) {
                throw new TDProject_Channel_Common_Exceptions_CategoryNameNotUniqueException(
            		'Category name ' . $name . ' is not unique'
                );
            }
            // create a new category
            $category = TDProject_Channel_Model_Utils_CategoryUtil::getHome($this->getContainer())
                ->epbCreate();
            // set the data
            $category->setName($lvo->getName());
            $category->setAlias($lvo->getAlias());
            $category->setDescription($lvo->getDescription());
            $categoryId = $category->create();
        } 
        else {
            // update the category
            $category = TDProject_Channel_Model_Utils_CategoryUtil::getHome($this->getContainer())
                ->findByPrimaryKey($categoryId);
            $category->setName($lvo->getName());
            $category->setAlias($lvo->getAlias());
            $category->setDescription($lvo->getDescription());
            $category->update();
        }
        return $categoryId;
    }
}