<?php

/**
 * TDProject_Channel_Observer_Controller_Router
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
 * @subpackage	Controller
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Observer_Controller_Router
	extends TechDivision_Lang_Object
{

	public function __construct()
	{
		
	}
	
	/**
	 * Dummy implementation for observer test.
	 *
	 * @param TDProject_Interfaces_Event_Observer_Action $observer
	 * 		The observer instance
	 */
	public function preDispatchDefault(
		TDProject_Interfaces_Event_Observer_Action $observer)
	{
		$rewriteUrl = $observer->getApp()->getRequest()->getRedirectUrl();
		$baseUrl = $observer->getApp()->getBaseUrl();
		$source = str_replace($baseUrl, '', $rewriteUrl);
	}
}