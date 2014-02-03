<?php

/**
 * TDProject_Channel_Controller_ChannelPackage
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
class TDProject_Channel_Controller_ChannelPackage
    extends TDProject_Channel_Controller_Abstract 
{

	/**
	 * The key for the ActionForward to the channel package overview template.
	 * @var string
	 */
	const CHANNEL_PACKAGE_OVERVIEW = "ChannelPackageOverview";

	/**
	 * The key for the ActionForward to the channel package view template.
	 * @var string
	 */
	const CHANNEL_PACKAGE_VIEW = "ChannelPackageView";

	/**
     * Initializes the Action with the Context for the
     * actual request.
	 *
     * @param TechDivision_Controller_Interfaces_Context $context
     * 		The Context for the actual Request
     * @return void
	 */
	public function __construct(
        TechDivision_Controller_Interfaces_Context $context) 
    {
        // call the parent method
        parent::__construct($context);
		// initialize the default page title
		$this->_setPageTitle(new TechDivision_Lang_String('TDProject, v2.0 - Channels'));
	}

	/**
	 * This method is automatically invoked by the controller and implements
	 * the functionality to load a list with with all packages.
	 *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
	 */
	public function __defaultAction()
	{
		try {
			// replace the default ActionForm
			$this->getContext()->setActionForm(
				new TDProject_Channel_Block_ChannelPackage_Overview(
					$this->getContext()
				)
			);
            // load and register the package overview data
            $this->_getRequest()->setAttribute(
            	TDProject_Core_Controller_Util_WebRequestKeys::OVERVIEW_DATA,
            	$this->_getDelegate()->getChannelPackageOverviewData()
            );
		} 
		catch(Exception $e) {
			// create and add and save the error
			$errors = new TechDivision_Controller_Action_Errors();
			$errors->addActionError(new TechDivision_Controller_Action_Error(
                TDProject_Core_Controller_Util_Errorkeys::SYSTEM_ERROR, $e->__toString())
            );
			// adding the errors container to the Request
			$this->_saveActionErrors($errors);
			// set the ActionForward in the Context
			return $this->_findForward(
			    TDProject_Core_Controller_Util_GlobalForwardKeys::SYSTEM_ERROR
			);
		}
		// go to the channel package overview page
		return $this->_findForward(
		    TDProject_Channel_Controller_ChannelPackage::CHANNEL_PACKAGE_OVERVIEW
		);
	}

	/**
	 * This method is automatically invoked by the controller and implements
	 * the functionality to load the package data with the id passed in the
	 * request for editing it.
	 *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
	 */
	public function editAction()
	{
        try {
            // try to load the package ID from the Request
            if (($channelPackageId = $this->_getRequest()->getAttribute(
                TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_PACKAGE_ID)) == null) {
                $channelPackageId = $this->_getRequest()->getParameter(
                    TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_PACKAGE_ID,
                    FILTER_VALIDATE_INT
                );
            }
            // initialize the ActionForm with the data from the DTO
            $this->_getActionForm()->populate(
                $dto = $this->_getDelegate()->getChannelPackageViewData(
                	TechDivision_Lang_Integer::valueOf(
                    	new TechDivision_Lang_String($channelPackageId)
                	)
                )
            );
            // register the DTO in the Request
            $this->_getRequest()->setAttribute(
                TDProject_Channel_Controller_Util_WebRequestKeys::VIEW_DATA,
                $dto
            );
        } 
        catch(Exception $e) {
			// create and add and save the error
			$errors = new TechDivision_Controller_Action_Errors();
			$errors->addActionError(
			    new TechDivision_Controller_Action_Error(
                    TDProject_Core_Controller_Util_Errorkeys::SYSTEM_ERROR,
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
        // return to the channel package detail page
        return $this->_findForward(
            TDProject_Channel_Controller_ChannelPackage::CHANNEL_PACKAGE_VIEW
        );
	}

   /**
     * This method is automatically invoked by the controller and implements
     * the functionality to create a new package.
     *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
     */
    public function createAction()
    {
        try {
            // initialize the ActionForm with the data from the DTO
            $this->_getActionForm()->populate(
                $this->_getDelegate()->getChannelPackageViewData()
            );
        } 
        catch(Exception $e) {
			// create and add and save the error
			$errors = new TechDivision_Controller_Action_Errors();
			$errors->addActionError(
			    new TechDivision_Controller_Action_Error(
                    TDProject_Core_Controller_Util_Errorkeys::SYSTEM_ERROR,
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
        // return to the channel package detail page
        return $this->_findForward(
            TDProject_Channel_Controller_ChannelPackage::CHANNEL_PACKAGE_VIEW
        );
    }

	/**
	 * This method is automatically invoked by the controller and implements
	 * the functionality to save the passed package data.
	 *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
	 */
	public function saveAction()
	{
		try {
		    // load the ActionForm
		    $actionForm = $this->_getActionForm();
		    // validate the ActionForm with the login credentials
            $actionErrors = $actionForm->validate();
            if (($errorsFound = $actionErrors->size()) > 0) {
                $this->_saveActionErrors($actionErrors);
                return $this->createAction();
            }
			// save the package
			$channelPackageId = $this->_getDelegate()
			    ->saveChannelPackage($actionForm->repopulate());
			// store the channel package ID of the request
			$this->_getRequest()->setAttribute(
                TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_PACKAGE_ID,
                $channelPackageId->intValue()
            );
			// create the affirmation message
	        $actionMessages = new TechDivision_Controller_Action_Messages();
            $actionMessages->addActionMessage(
                new TechDivision_Controller_Action_Message(
                    TDProject_Channel_Controller_Util_MessageKeys::AFFIRMATION,
                    $this->translate('channelPackageUpdate.successfull')
                )
            );
            // save the ActionMessages in the request
            $this->_saveActionMessages($actionMessages);
		} 
		catch(TDProject_Channel_Common_Exceptions_ChannelPackageNameNotUniqueException $cpnnue) {
            // create action errors container
            $errors = new TechDivision_Controller_Action_Errors();
            // add error to container
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::NAME,
                    $this->translate('channel-package-name.not.unique')
                )
            );
            // save container in request
            $this->_saveActionErrors($errors);
            // return failure mapping
            return $this->create();
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
		// return to the package detail page
        return $this->editAction();
	}

	/**
     * This method is automatically invoked by the controller and implements
     * the functionality to delete the passed package.
     *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
     */
    public function deleteAction() {
        try {
            // load the package ID from the request
        	$channelPackageId = $this->_getRequest()->getParameter(
                TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_PACKAGE_ID,
                FILTER_VALIDATE_INT
            );
            // delete the package
            $this->_getDelegate()->deleteChannelPackage(
                TechDivision_Lang_Integer::valueOf(
                    new TechDivision_Lang_String($channelPackageId)
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
        // return to the package overview page
        return $this->__defaultAction();
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
	    // initialize the page and add the Block
	    $page = new TDProject_Core_Block_Page($this->getContext());
	    $page->setPageTitle($this->_getPageTitle());
	    $page->addBlock($this->getContext()->getActionForm());
	    // register the Block in the Request
	    $this->_getRequest()->setAttribute($path, $page);
	}
}