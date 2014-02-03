<?php

/**
 * TDProject_Channel_Controller_Channel_Release_Upload
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'PEAR/Validate.php';

/**
 * @category    TProject
 * @package     TDProject_Channel
 * @subpackage	Controller
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Controller_Channel_Release_Upload
    extends TDProject_Channel_Controller_Release
{

	/**
	 * The key for the ActionForward to the success page.
	 * @var string
	 */
	const UPLOAD = "Upload";

	/**
     * This method is automatically invoked by the controller and implements
     * the functionality to upload a new package release.
     *
	 * @return void
     */
    public function __defaultAction()
    {
    	try {
	        // load the channel ID from the request
	        $channelId = $this->_getRequest()->getParameter(
	        	TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_ID,
	        	FILTER_VALIDATE_INT
	        );
	        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
	        $allowedExtensions = array('tgz');
	        // max file size in bytes
	        $sizeLimit = $this->_helper()->toBytes(get_cfg_var('upload_max_filesize'));
	        // initialize the uploader
	       	$uploader = $this->_helper()
	       		->setAllowedExtension($allowedExtensions)
	       		->setSizeLimit($sizeLimit)
	       		->checkServerSettings();
	       	// handle the uploaded binary
	       	$targetFilename = $uploader->handleUpload('/tmp/', true);
	       	// create the release
	       	$releaseId = $this->_getDelegate()
	       		->createBinaryRelease(
	       			new TechDivision_Lang_Integer((int) $channelId),
	       			new TechDivision_Lang_String($targetFilename)
	       		);
	       	// initialize the result array
	       	$result = array('success' => true);
	        // add a JSON result to the request
	        $this->_getRequest()->setAttribute(
	        	TDProject_Channel_Controller_Util_WebRequestKeys::JSON_RESULT,
	        	htmlspecialchars(json_encode($result), ENT_NOQUOTES)
	        );
    	}
		catch(Exception $e) {
			// log the exception otherwise
			$this->_getLogger()->error($e->__toString(), __LINE__);
			// initialize the result array
			$result = array('error' => $e->getMessage());
			// add a 500 header to signal an internal server error
			header('HTTP/1.1 500 Internal Server Error', true, 500);
			// add a JSON result to the request
			$this->_getRequest()->setAttribute(
				TDProject_Channel_Controller_Util_WebRequestKeys::JSON_RESULT,
				htmlspecialchars(json_encode($result), ENT_NOQUOTES)
			);
		}
		// go to the standard page
		return $this->_findForward(
		    TDProject_Channel_Controller_Channel_Release_Upload::UPLOAD
		);
    }

	/**
	 * Tries to load the Block class specified as path parameter
	 * in the ActionForward. If a Block was found and the class
	 * can be instanciated, the Block was registered to the Request
	 * with the path as key.
	 *
	 * @param TechDivision_Controller_Action_Forward $actionForward
	 * 		The ActionForward to initialize the Block for
	 * @return void
	 */
	protected function _getBlock(
	    TechDivision_Controller_Action_Forward $actionForward)
	{
	    // check if the class required to initialize the Block is included
	    if (!class_exists($path = $actionForward->getPath())) {
	        return;
	    }
	    // if yes, create a new instance
	    $reflectionClass = new ReflectionClass($path);
	    $page = $reflectionClass->newInstance($this->getContext());
	    // register the Block in the Request
	    $this->_getRequest()->setAttribute($path, $page);
	}

    /**
     * Returns the upload helper instance.
     *
     * @return TDProject_Core_Controller_Util_Helper_Upload
     * 		The upload helper instance
     */
	protected function _helper()
	{
		// create a new instance for the helper
		return $this->getApp()->getObjectFactory()
			->newInstance('TDProject_Core_Controller_Helper_Upload');
	}
}