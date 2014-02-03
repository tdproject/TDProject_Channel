<?php

/**
 * TDProject_Channel_Model_Serializer_Release_DescendingComparator
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * This class is the comparator used to sort releases descending by their PHP/PEAR version numbers.
 *
 * @category   	TDProject
 * @package    	TDProject_Channel
 * @subpackage	Model
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Model_Serializer_Release_DescendingComparator
    extends TDProject_Channel_Model_Serializer_Release_AscendingComparator {

    /**
     * Compares the version number of the two passed releases.
     *
     * @param TDProject_Channel_Model_Entities_Release $o1 Holds the first release
     * @param TDProject_Channel_Model_Entities_Release $o2 Holds the second release
     * @return integer 1 if the first version is lower than the second, 0 if they are equal, and -1 if the second is lower
     * @see TDProject_Channel_Model_Serializer_Release_AscendingComparator::_compare()
     */
    protected function _compare(
        TDProject_Channel_Model_Entities_Release $release1,
        TDProject_Channel_Model_Entities_Release $release2)
    {
        return parent::_compare($release1, $release2) * -1;
    }
}