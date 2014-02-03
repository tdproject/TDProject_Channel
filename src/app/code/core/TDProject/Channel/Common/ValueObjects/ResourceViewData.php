<?php

/**
 * TDProject_Channel_Common_ValueObjects_ResourceViewData
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * This class is the data transfer object between the
 * model and the controller for the table assertion.
 *
 * Each class member reflects a database field and
 * the values of the related dataset.
 *
 * @category   	TDProject
 * @package     TDProject_ERP
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Common_ValueObjects_ResourceViewData
    extends TechDivision_Lang_Object
    implements TechDivision_Model_Interfaces_Value
{

    /**
     * The resource content in text/text or application/xml format.
     * @var TechDivision_Lang_String
     */
    protected $_content = null;

    /**
     * The resource content type.
     * @var TechDivision_Lang_String
     */
    protected $_contentType = null;

    /**
     * The resource content as text or xml.
     *
     * @param TechDivision_Lang_String $content The resource content
     * @return void
     */
    public function __construct(TechDivision_Lang_String $content)
    {
        // set the content and the type
        $this->_content = $content;
        // initialize the content type as application/xml by default
        $this->_contentType = new TechDivision_Lang_String('application/xml');
    }

    /**
     * Returns the resource content as text or xml.
     *
     * @return TechDivision_Lang_String The resource content
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Sets the resource content type.
     *
     * @param TechDivision_Lang_String $contentType The content type
     * @return TDProject_Channel_Common_ValueObjects_ResourceViewData
     * 		The DTO itself
     */
    public function setContentType(TechDivision_Lang_String $contentType)
    {
        $this->_contentType = $contentType;
        return $this;
    }

    /**
     * Returns the resource content type.
     *
     * @return TechDivision_Lang_String The content type
     */
    public function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * Returns the length of the content.
     *
     * @return TechDivision_Lang_Integer
     * 		The content length
     */
    public function getContentLength()
    {
        return new TechDivision_Lang_Integer($this->getContent()->length());
    }
}