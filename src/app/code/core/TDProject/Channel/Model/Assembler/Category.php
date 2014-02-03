<?php

/**
 * TDProject_Channel_Model_Assembler_Category
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
class TDProject_Channel_Model_Assembler_Category
    extends TDProject_Core_Model_Assembler_Abstract {

    /**
     * Factory method to create a new instance.
     *
     * @param TechDivision_Model_Interfaces_Container $container The container instance
     * @return TDProject_Channel_Model_Actions_Category
     * 		The requested instance
     */
    public static function create(TechDivision_Model_Interfaces_Container $container)
    {
        return new TDProject_Channel_Model_Assembler_Category($container);
    }

    /**
     * Returns a DTO initialized with the data of the category
     * with the passed ID.
     *
     * @param TechDivision_Lang_Integer $categoryId
     * 		The ID of the category the VO has to be initialized for
     * @return TDProject_Channel_Common_ValueObjects_CategoryValue
     * 		The initialized VO
     */
    public function getCategoryViewData(TechDivision_Lang_Integer $categoryId = null) {
    	// check if a category ID was passed
    	if (!empty($categoryId)) { // if yes, load the category
    		$category = TDProject_Channel_Model_Utils_CategoryUtil::getHome($this->getContainer())
    			->findByPrimaryKey($categoryId);
    	}
    	else {
        	// if not, initialize a new category
        	$category = TDProject_Channel_Model_Utils_CategoryUtil::getHome($this->getContainer())
        		->epbCreate();
    	}
    	// return the initialized VO
    	return $category->getValue();
    }

    /**
     * Returns an ArrayList with all categories assembled as LVO's.
     *
     * @return TechDivision_Collections_ArrayList
     * 		The requested category LVO's
     */
    public function getCategoryOverviewData()
    {
        // initialize a new ArrayList
        $list = new TechDivision_Collections_ArrayList();
        // load the categories
        $categories = TDProject_Channel_Model_Utils_CategoryUtil::getHome($this->getContainer())
            ->findAll();
        // assemble the categories
        foreach ($categories as $category) {
        	// add the DTO to the ArrayList
            $list->add(
                new TDProject_Channel_Common_ValueObjects_CategoryOverviewData(
                    $category
                )
            );
        }
        // return the ArrayList with the category LVO's
        return $list;
    }
    
    /**
     * Returns the passed channel's default category. 
     *
     * @param TechDivision_Lang_Integer $channelId
     * 		The ID of the channel to return the default category for
     * @return TDProject_Channel_Model_Entities_Category
     * 		The channel's default category
     * @throws TDProject_Channel_Common_Exceptions_NoDefaultCategoryException
     * 		Is thrown if NO default category for the passed channel ID exists
     * @todo Passed channel ID is actually not considered, but has to!
     */
    public function getDefaultCategory(TechDivision_Lang_Integer $channelId)
    {
    	// load the available categories
	    $categories = TDProject_Channel_Model_Utils_CategoryUtil::getHome($this->getContainer())
	    	->findAll();
	    // initialize an iterterator
	    $iterator = $categories->getIterator();
	    // return the first category available
	    while ($iterator->valid()) {
	    	return $iterator->current();
	    }
	    // throw an exception if NO category is available
	    throw new TDProject_Channel_Common_Exceptions_NoDefaultCategoryException();
    }
}