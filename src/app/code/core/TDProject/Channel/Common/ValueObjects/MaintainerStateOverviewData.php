<?php

/**
 * TDProject_Channel_Common_ValueObjects_MaintainerStateOverviewData
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * This class is the data transfer object between the model
 * and the controller for the a maintainer state selection.
 *
 * @category   	TDProject
 * @package     TDProject_Channel
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Common_ValueObjects_MaintainerStateOverviewData
    extends TechDivision_Lang_Object
    implements TDProject_Core_Interfaces_Block_Widget_Element_Select_Option {

    /**
     * The state value.
     * @var TechDivision_Lang_String
     */
    protected $_stateValue = null;

    /**
     * Initializes the DTO with the passed values.
     *
     * @param TechDivision_Lang_Boolean $stateValue
     * 		The state value
     * @return void
     */
    public function __construct(TechDivision_Lang_Boolean $stateValue) {
		// set the values
    	$this->_stateValue = $stateValue;
    }

	/**
	 * (non-PHPdoc)
	 * @see TDProject_Core_Interfaces_Block_Widget_Element_Select_Option::getOptionValue()
	 */
   	public function getOptionValue() {
   		return $this->_stateValue;
   	}

   	/**
   	 * (non-PHPdoc)
   	 * @see TDProject_Core_Interfaces_Block_Widget_Element_Select_Option::getOptionLabel()
   	 */
   	public function getOptionLabel() {
   		return $this->getOptionValue();
   	}

   	/**
   	 * (non-PHPdoc)
   	 * @see TDProject_Core_Interfaces_Block_Widget_Element_Select_Option::isSelected()
   	 */
   	public function isSelected(TechDivision_Lang_Object $value = null)
   	{
   		if ($value == null) {
   			return false;
   		}
   		return $this->getOptionValue()->equals($value);
   	}
}