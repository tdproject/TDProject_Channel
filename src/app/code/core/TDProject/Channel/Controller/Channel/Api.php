<?php

/**
 * TDProject_Channel_Controller_Api
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category    TProject
 * @package     TDProject_Channel
 * @subpackage	Controller
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Controller_Channel_Api
    extends TDProject_Channel_Controller_Abstract {

    /**
     * The key of the ActionForward returned after an successfull operation.
     * @var string
     */
    const RESOURCE = "Resource";

	/**
	 * The key for the ActionForward to the success page.
	 * @var string
	 */
	const DOWNLOAD = "Download";

	/**
	 * The key for the ActionForward to the success page.
	 * @var string
	 */
	const UPLOAD = "Upload";

    /**
     * Builds and adds the channel.xml content to the
     * response.
     *
     * @return void
     */
    public function __defaultAction()
    {
        try {
            // load the channel ID
            $channelId = $this->_getRequest()->getParameter(
                TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_ID,
                FILTER_VALIDATE_INT
            );
            // load the resource URI
            $resourceUri = $this->_helper()->prepareResourceUri();
            // log the found resource URI
            $this->_getLogger()->debug(
            	'Found resource URI: ' . $resourceUri,
            	__LINE__
            );
            // prepare the base URL
            $baseUrl = 'http://' . $this->_getRequest()->getServerName() . $this->getApp()->getBaseUrl();
            // load the resource by the channel ID/redirect URL
            $dto = $this->_getDelegate()->getResourceViewData(
                new TechDivision_Lang_String($baseUrl),
                TechDivision_Lang_Integer::valueOf(
                    new TechDivision_Lang_String($channelId)
                ),
                $resourceUri
            );
            // set the XML content in the Request
            $this->_getRequest()->setAttribute(
                TDProject_Channel_Controller_Util_WebRequestKeys::RESOURCE,
                $dto
            );
        }
        // TODO Has to be refactored -> Redirect to a real 404 instead
        catch (TDProject_Channel_Common_Exceptions_InvalidResourceUri $iru) {
            // create action errors container
            $errors = new TechDivision_Controller_Action_Errors();
            // add error to container
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Core_Controller_Util_ErrorKeys::SYSTEM_ERROR,
                    '404 Not Found'
                )
            );
            // log the exception
            $this->_getLogger()->error($iru->__toString());
            // save container in request
            $this->_saveActionErrors($errors);
            // set the ActionForward in the Context
            return $this->_findForward(
                TDProject_Core_Controller_Util_GlobalForwardKeys::SYSTEM_ERROR
            );
        }
        catch (Exception $e) {
            // create action errors container
            $errors = new TechDivision_Controller_Action_Errors();
            // add error to container
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Core_Controller_Util_ErrorKeys::SYSTEM_ERROR,
                    $e->__toString()
                )
            );
			// log the exception
			$this->getLogger()->error($e->__toString());
            // save container in request
            $this->_saveActionErrors($errors);
            // set the ActionForward in the Context
            return $this->_findForward(
                TDProject_Core_Controller_Util_GlobalForwardKeys::SYSTEM_ERROR
            );
        }
        // return the ActionForward to the start page of the protected area
        return $this->_findForward(
            TDProject_Channel_Controller_Channel_Api::RESOURCE
        );
    }

	/**
     * This method is automatically invoked by the controller and implements
     * the functionality to upload a new package release.
     *
	 * @return void
     */
    public function downloadAction()
    {
    	try {
	        // load the channel ID from the request
	        $channelId = $this->_getRequest()->getParameter(
	        	TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_ID,
	        	FILTER_VALIDATE_INT
	        );
	        // load the requested filename from the request
	        $filename = $this->_getRequest()->getParameter(
	        	TDProject_Channel_Controller_Util_WebRequestKeys::FILENAME,
	        	FILTER_SANITIZE_STRING
	        );
	       	// load the path to the binary release
	       	$downloadUrl = $this->_getDelegate()
	       		->getDownloadUrl(
	       			new TechDivision_Lang_Integer((int) $channelId),
	       			new TechDivision_Lang_String($filename)
	       		);
	        // set the download URL for the release in the request
	       	$this->_getRequest()->setAttribute(
	       		TDProject_Channel_Controller_Util_WebRequestKeys::DOWNLOAD_URL,
	       		$downloadUrl
	       	);
    	}
    	catch(Exception $e) {
			$this->_getLogger()->error($e->__toString(), __LINE__);
		}
		// go to the standard page
		return $this->_findForward(
		    TDProject_Channel_Controller_Channel_Api::DOWNLOAD
		);
    }

	/**
     * This method is automatically invoked by the controller and implements
     * the functionality to upload a new package release.
     *
	 * @return void
     */
    public function uploadAction()
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
	        $sizeLimit = $this->_uploadHelper()->toBytes(get_cfg_var('upload_max_filesize'));
	        // initialize the uploader
	       	$uploader = $this->_uploadHelper()
	       		->setAllowedExtension($allowedExtensions)
	       		->setSizeLimit($sizeLimit)
	       		->checkServerSettings();
	       	// handle the uploaded binary
	       	$targetFilename = $uploader->handleUpload('/tmp/', true);
	       	// calculate the filesize in KB
	       	$filesize = filesize($targetFilename) / 1024;
	       	// create the release
	       	$releaseId = $this->_getDelegate()
	       		->createBinaryRelease(
	       			new TechDivision_Lang_Integer((int) $channelId),
	       			new TechDivision_Lang_String($targetFilename)
	       		);
	        // set the upload result in the request
	        $this->_getRequest()->setAttribute(
	        	TDProject_Channel_Controller_Util_WebRequestKeys::UPLOAD_RESULT,
	        	"Successfully uploaded PEAR package $targetFilename with $filesize KB"
	        );
    	}
		catch(Exception $e) {
			// log the exception message
			$this->_getLogger()->error($e->__toString(), __LINE__);
	        // set the upload result in the request
	        $this->_getRequest()->setAttribute(
	        	TDProject_Channel_Controller_Util_WebRequestKeys::UPLOAD_RESULT,
	        	"Upload failed: {$e->getMessage()}"
	        );
			// add a 500 header to signal an internal server error
			header('HTTP/1.1 500 Internal Server Error', true, 500);
		}
		// go to the standard page
		return $this->_findForward(
		    TDProject_Channel_Controller_Channel_Api::UPLOAD
		);
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Core_Controller_Abstract::getResourceId()
     */
    public function getResourceId()
    {
        // load the channel ID
        $channelId = $this->_getRequest()->getParameter(
            TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_ID,
            FILTER_VALIDATE_INT
        );
        // return the channel ID as TechDivision_Lang_Integer
        return TechDivision_Lang_Integer::valueOf(
            new TechDivision_Lang_String($channelId)
        );
    }

    /**
     * Returns the helper instance.
     *
     * @return TDProject_Channel_Controller_Util_Helper
     * 		The helper instance
     */
    protected function _helper()
    {
        // create a new instance for the helper
        return $this->getApp()->getObjectFactory()
            ->newInstance('TDProject_Channel_Controller_Util_Helper', array($this));
    }

    /**
     * Returns the upload helper instance.
     *
     * @return TDProject_Core_Controller_Util_Helper_Upload
     * 		The upload helper instance
     */
	protected function _uploadHelper()
	{
		// create a new instance for the helper
		return $this->getApp()->getObjectFactory()
			->newInstance('TDProject_Core_Controller_Helper_Upload');
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
	    TechDivision_Controller_Action_Forward $actionForward) {
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
}