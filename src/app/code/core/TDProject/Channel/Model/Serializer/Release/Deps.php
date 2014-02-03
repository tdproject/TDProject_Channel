<?php

/**
 * TDProject_Channel_Model_Serializer_Release_Deps
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
class TDProject_Channel_Model_Serializer_Release_Deps
    extends TDProject_Channel_Model_Serializer_Release_Abstract
{

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/r\/(?P<packageName>\w+)\/deps\.(?P<version>[0-9]{1,}\.[0-9]{1,}\.[0-9]{1,})(?P<stability>snapshot|devel|alpha|beta|stable)?(?P<build>[0-9]{1,})?\.txt/';

    /**
     * Sets the regular expression for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::serialize()
     */
    public function serialize(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// load the channel package instance
    	$this->getReleaseByChannelPackageNameAndVersion($assembler);
        // return the DTO with the version
        $dto =  new TDProject_Channel_Common_ValueObjects_ResourceViewData(
            $this->getRelease()->getDependencies()
        );
        return $dto->setContentType(new TechDivision_Lang_String('text/html'));
    }
}