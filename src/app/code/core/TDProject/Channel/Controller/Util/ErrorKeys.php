<?php

/**
 * TDProject_Channel_Controller_Util_ErrorKeys
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   	TDProject
 * @package     TDProject_Channel
 * @subpackage	Controller
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Controller_Util_ErrorKeys
{
	/**
	 * Private constructor for marking
	 * the class as utiltiy.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }

	/**
	 * The key for a name.
	 * @var string
	 */
	const NAME = "name";

	/**
	 * The key for an alias.
	 * @var string
	 */
	const ALIAS = "alias";

	/**
	 * The key for a licence.
	 * @var string
	 */
	const LICENCE = "licence";

	/**
	 * The key for a licence URI.
	 * @var string
	 */
	const LICENCE_URI = "licenceUri";
}