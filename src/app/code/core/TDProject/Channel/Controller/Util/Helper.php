<?php

/**
 * TDProject_Channel_Controller_Util_Helper
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
 * @subpackage	Controller
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Controller_Util_Helper
	extends TechDivision_Lang_Object {

    /**
     * The action instance.
     * @var TDProject_Channel_Controller_Index
     */
    protected $_action = null;

    /**
     * Constructor to initialize helper.
     *
     * @param TDProject_Channel_Controller_Channel_Api $action The action instance
     * @return void
     */
    public function __construct(TDProject_Channel_Controller_Channel_Api $action)
    {
        $this->_action = $action;
    }

    /**
     * Prepares the resource URI requested by the PEAR client.
     *
     * @return TechDivision_Lang_String The PEAR resource URI.
     */
    public function prepareResourceUri()
    {
        // load the request
        $request = $this->getAction()->getApp()->getRequest();
        // load the redirect URL
        $redirectUrl = $request->getRedirectUrl();
        // load the channel ID
        $channelId = $request->getParameter(
            TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_ID,
            FILTER_VALIDATE_INT
        );
        // prepare the application base URL and append the channel ID
        $baseUrl = $this->getAction()->getApp()->getBaseUrl();
        $baseUrl .= $this->getActionPath() . '/channelId/' . $channelId;
        // strip the base URL from the redirect URL = resource URI
        $resourceUri = str_replace($baseUrl, '', $redirectUrl);
        // return the resource URI
        return new TechDivision_Lang_String($resourceUri);
    }

    /**
     * Returns the action instance.
     *
     * @return TDProject_Channel_Controller_IndexThe action instance
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Return the action path for the actual action.
     *
     * @return string The action path
     */
    public function getActionPath()
    {
        return $this->getAction()->getContext()->getActionMapping()->getPath();
    }
}