<?php

/**
 * TDProject_Channel_Controller_Release
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
class TDProject_Channel_Controller_Release
    extends TDProject_Channel_Controller_Abstract 
{

	/**
     * This method is automatically invoked by the controller and implements
     * the functionality to delete the passed channel.
     *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
     */
    public function deleteAction() 
    {
        try {
            // load the release ID from the request
        	$releaseId = $this->_getRequest()->getParameter(
                TDProject_Channel_Controller_Util_WebRequestKeys::RELEASE_ID,
                FILTER_VALIDATE_INT
            );
            // delete the release and return the package ID
            $packageId = $this->_getDelegate()->deleteRelease(
                TechDivision_Lang_Integer::valueOf(
                    new TechDivision_Lang_String($releaseId)
                )
            );
            // create the URL for the forward
            $url = $this->getUrl(
            	array(
            		'path' => '/package',
            		'method' => 'edit',
            		'packageId' => $packageId->intValue()
            	)
            );
        }
        catch(Exception $e) {
            // create and add and save the error
            $errors = new TechDivision_Controller_Action_Errors();
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Core_Controller_Util_ErrorKeys::SYSTEM_ERROR,
                    $e->__toString()
                )
            );
            // adding the errors container to the Request
			$this->_saveActionErrors($errors);
			// set the ActionForward in the Context
			return $this->_findForward(
			    TDProject_Core_Controller_Util_GlobalForwardKeys::SYSTEM_ERROR
			);
        }
        // forward to the package detail page
        return $this->forwardByUrl($url, true);
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
	    // initialize the page and add the Block
	    $page = new TDProject_Core_Block_Page($this->getContext());
	    $page->setPageTitle($this->_getPageTitle());
	    $page->addBlock($this->getContext()->getActionForm());
	    // register the Block in the Request
	    $this->_getRequest()->setAttribute($path, $page);
	}
}