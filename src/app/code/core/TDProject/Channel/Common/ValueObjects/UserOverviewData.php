<?php

/**
 * TDProject_Channel_Common_ValueObjects_UserOverviewData
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * This class is the data transfer object between the
 * model and the controller for a maintainer.
 *
 * @category   	TDProject
 * @package     TDProject_Channel
 * @subpackage	Common
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Common_ValueObjects_UserOverviewData
    extends TDProject_Core_Common_ValueObjects_UserLightValue
{

    /**
     * The the maintainer's API hash.
     * @var TechDivision_Lang_String
     */
    protected $_hash = null;

    /**
     * The constructor intializes the DTO with the
     * values passed as parameter.
     *
     * @param TDProject_Core_Common_ValueObjects_UserLightValue $lvo
     * 		Holds the array with the virtual members to pass to the AbstractDTO's constructor
     * @param TechDivision_Lang_String $defaultRole
     * 		The maintainer's API hash
     * @return void
     */
    public function __construct(TDProject_Core_Common_ValueObjects_UserLightValue $lvo) {
        // call the parents constructor
        parent::__construct($lvo);
        // initialize the maintainer's API hash
        $this->setHash(new TechDivision_Lang_String());
    }

    /**
     * Sets the maintainer's API hash.
     *
     * @param TechDivision_Lang_String $hash
     * 		The maintainer's API hash
     * @return void
     */
    public function setHash(TechDivision_Lang_String $hash)
    {
        $this->_hash = $hash;
    }

    /**
     * Returns the maintainer's API hash.
     *
     * @return TechDivision_Lang_String
     * 		The maintainer's API hash
     */
    public function getHash()
    {
        return $this->_hash;
    }
}