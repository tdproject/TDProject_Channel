<?php

/**
 * TDProject_Channel_Block_Category_View
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category    TDProject
 * @package     TDProject_Channel
 * @subpackage	View
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Block_Category_View
	extends TDProject_Channel_Block_Abstract_Category {
    
	/**
     * (non-PHPdoc)
     * @see TDProject/Interfaces/Block#prepareLayout()
     */
    public function prepareLayout() {
    	// initialize the tabs
    	$tabs = $this->addTabs('tabs', 'Tabs');
        // add the tab for the channel data
        $tabs->addTab(
        	'category', 'Category Data'
        )
    	->addFieldset(
    		'category', 'Category Data'
    	)
		->addElement(
		    $this->getElement('textfield', 'name', 'Name')->setMandatory()
        )
    	->addElement(
    	    $this->getElement('textfield', 'alias', 'Alias')->setMandatory()
    	)
        ->addElement(
            $this->getElement('textarea', 'description', 'Description')
        );
	    // return the instance itself
	    return parent::prepareLayout();
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Core_Block_Abstract::repopulate()
     */
    function repopulate()
    {
        // initialize the DTO with the data to save
		$lvo = new TDProject_Channel_Common_ValueObjects_CategoryLightValue();
		$lvo->setCategoryId($this->getCategoryId());
		$lvo->setName($this->getName());
		$lvo->setAlias($this->getAlias());
		$lvo->setDescription($this->getDescription());
        // return the initialized DTO
    	return $lvo;
    }

    /**
     * This method checks if the values in the member variables
     * holds valid data. If not, a ActionErrors container will
     * be initialized an for every incorrect value a ActionError
     * object with the apropriate error message will be added.
     *
     * @return ActionErrors Returns a ActionErrors container with ActionError objects
     */
    function validate() {
        // initialize the ActionErrors
        $errors = new TechDivision_Controller_Action_Errors();
        // check if a name was entered
        if ($this->_name->length() == 0) {
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::NAME,
                    $this->translate('category-name.none')
                )
            );
        }
        // check if a alias was entered
        if ($this->_alias->length() == 0) {
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::ALIAS,
                    $this->translate('category-alias.none')
                )
            );
        }
        // return the ActionErrors
        return $errors;
    }
}