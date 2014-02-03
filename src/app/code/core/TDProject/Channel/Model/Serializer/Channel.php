<?php

/**
 * TDProject_Channel_Model_Serializer_Channel
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
class TDProject_Channel_Model_Serializer_Channel
    extends TDProject_Channel_Model_Serializer_Abstract {

    /**
     * The REST versions the channel supports.
     * @var array
     */
    protected $_restVersions = array('REST1.0', 'REST1.1');

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/channel.xml/';

    /**
     * Sets the PCRE for the serializer.
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
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::getNamespaces()
     */
    public function getNamespaces()
    {
        return array();
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::serialize()
     */
    public function serialize(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
    	// load the channel and the base URL from the assembler
    	$chn = $assembler->getChannel();
    	$baseUrl = $assembler->getBaseUrl();
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new root element
        $channel = $doc->createElement('channel');
        // set the channel's version
        $channel->setAttribute('version', '1.0');
        // add the channel's name
        $name = $doc->createElement('name');
        $name->nodeValue = $chn->getName();
        $channel->appendChild($name);
        // add the channel's summary
        $summary = $doc->createElement('summary');
        $summary->nodeValue = $chn->getSummary();
        $channel->appendChild($summary);
        // add the channel's alias
        $suggestedalias = $doc->createElement('suggestedalias');
        $suggestedalias->nodeValue = $chn->getAlias();
        $channel->appendChild($suggestedalias);
        // create the node for the REST url's
        $rest = $doc->createElement('rest');
        // append the REST url's
        for ($i = 0; $i < sizeof($this->_restVersions); $i++) {
            $baseurl = $doc->createElement('baseurl');
            $baseurl->setAttribute('type', $this->_restVersions[$i]);
            $baseurl->nodeValue = 'http://' . $chn->getName() . '/';
            $rest->appendChild($baseurl);
        }
        // add the node for the primary channel servers
        $primary = $doc->createElement('primary');
        $primary->appendChild($rest);
        // add the node for the channel servers
        $servers = $doc->createElement('servers');
        $servers->appendChild($primary);
        // add the node with the channel servers to the channel itself
        $channel->appendChild($servers);
        // append the root element to the DOM tree
        $doc->appendChild($channel);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}