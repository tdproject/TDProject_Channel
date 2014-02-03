<?php

/**
 * TDProject_Channel_Model_Serializer_Interfaces_Serializer
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
interface TDProject_Channel_Model_Serializer_Interfaces_Serializer
{

	/**
	 * Checks if the passed resource URI matches the serializer.
	 *
	 * @param TechDivision_Lang_String $resourceUri
	 * 		The resource URI to match the serializer for
	 * @return boolean TRUE if the serializer matches, else FALSE
	 */
	public function matches(TechDivision_Lang_String $resourceUri);

    /**
     * Serializes the ressource to the requested respresentation.
     *
     * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
     * 		The assembler instance
     * @return string The requested ressource representation
     */
    public function serialize(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler);

    /**
     * Returns the namespace to use.
     *
     * @return TechDivision_Lang_String
     * 		The acutal namespace to use
     */
    public function getNamespace();


    /**
     * Returns the valid namespaces for the ressource of the
     * Serializer implementation.
     *
     * @return array The valid namespaces as array
     */
    public function getNamespaces();

    /**
     * Returns the serializers PCRE regex.
     *
     * @return string
     */
    public function getRegex();
}