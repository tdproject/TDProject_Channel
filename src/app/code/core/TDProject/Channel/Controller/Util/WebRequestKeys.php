<?php

/**
 * TDProject_Channel_Controller_Util_WebRequestKeys
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
class TDProject_Channel_Controller_Util_WebRequestKeys
	extends TDProject_Common_Util_WebRequestKeys {

	/**
	 * The request parameter key with the PEAR resource content.
	 * @var string
	 */
	const RESOURCE = "resource";

	/**
	 * The request parameter for the channel ID.
	 * @var string
	 */
	const CHANNEL_ID = 'channelId';

	/**
	 * The request parameter for the category ID.
	 * @var string
	 */
	const CATEGORY_ID = 'categoryId';

	/**
	 * The request parameter for the package ID.
	 * @var string
	 */
	const CHANNEL_PACKAGE_ID = 'channelPackageId';

	/**
	 * The request parameter for the release ID.
	 * @var string
	 */
	const RELEASE_ID = 'releaseId';

	/**
	 * The request parameter for a JSON result.
	 * @var string
	 */
	const JSON_RESULT = 'jsonResult';

	/**
	 * The request parameter for the upload result.
	 * @var string
	 */
	const UPLOAD_RESULT = 'uploadResult';

	/**
	 * The request parameter for the requested filename.
	 * @var string
	 */
	const FILENAME = 'filename';

	/**
	 * The request parameter for a download URL.
	 * @var string
	 */
	const DOWNLOAD_URL = 'downloadUrl';

	/**
	 * The request parameter for the user ID.
	 * @var string
	 */
	const USER_ID_FK = 'userIdFk';

	/**
	 * The request parameter for a maintainer role.
	 * @var string
	 */
	const ROLE = 'role';

	/**
	 * The request parameter for a maintainer state.
	 * @var string
	 */
	const ACTIVE = 'active';

	/**
	 * The request parameter for the maintainer's API-Hash.
	 * @var string
	 */
	const HASH = 'hash';
}