<?php

/**
 * TDProject_Channel_Model_Serializer_Maintainer_Info
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
class TDProject_Channel_Model_Serializer_Maintainer_Info
    extends TDProject_Channel_Model_Serializer_Maintainer_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/m\/(?P<username>\w+)\/info\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_MAINTAINER);
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
    	$this->getUserByUsername($assembler);
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new namespaced root element
        $m = $doc->createElementNS($this->getNamespace(), 'm');
        // add the schema to the root element
        $m->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.maintainer http://pear.php.net/dtd/rest.maintainer.xsd'
        );
        // create an element for the maintainer's name
        $h = $doc->createElement('h');
        $h->nodeValue = $this->getUser()->getUsername();
        $m->appendChild($h);
        // create an element for the maintainer's handle
        $n = $doc->createElement('n');
        $n->nodeValue = $this->getUser()->getUsername();
        $m->appendChild($n);
        // append the root element to the DOM tree
        $doc->appendChild($m);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}