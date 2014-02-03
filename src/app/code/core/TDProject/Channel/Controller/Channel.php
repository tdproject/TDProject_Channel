<?php

/**
 * TDProject_Channel_Controller_Channel
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
class TDProject_Channel_Controller_Channel
    extends TDProject_Channel_Controller_Abstract {

	/**
	 * The key for the ActionForward to the channel overview template.
	 * @var string
	 */
	const CHANNEL_OVERVIEW = "ChannelOverview";

	/**
	 * The key for the ActionForward to the channel view template.
	 * @var string
	 */
	const CHANNEL_VIEW = "ChannelView";

	/**
     * Initializes the Action with the Context for the
     * actual request.
	 *
     * @param TechDivision_Controller_Interfaces_Context $context
     * 		The Context for the actual Request
     * @return void
	 */
	public function __construct(
        TechDivision_Controller_Interfaces_Context $context) {
        // call the parent method
        parent::__construct($context);
		// initialize the default page title
		$this->_setPageTitle(new TechDivision_Lang_String('TDProject, v2.0 - Channels'));
	}

	/**
	 * This method is automatically invoked by the controller and implements
	 * the functionality to load a list with with all channels.
	 *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
	 */
	function __defaultAction()
	{
		try {
			// replace the default ActionForm
			$this->getContext()->setActionForm(
				new TDProject_Channel_Block_Channel_Overview($this->getContext())
			);
            // load and register the channel overview data
            $this->_getRequest()->setAttribute(
            	TDProject_Core_Controller_Util_WebRequestKeys::OVERVIEW_DATA,
            	$this->_getDelegate()->getChannelOverviewData()
            );
		} catch(Exception $e) {
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
		// go to the standard page
		return $this->_findForward(
		    TDProject_Channel_Controller_Channel::CHANNEL_OVERVIEW
		);
	}

	/**
	 * This method is automatically invoked by the controller and implements
	 * the functionality to load the channel data with the id passed in the
	 * request for editing it.
	 *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
	 */
	function editAction()
	{
        try {
            // try to load the channel ID from the Request
            if (($channelId = $this->_getRequest()->getAttribute(
                TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_ID)) == null) {
                $channelId = $this->_getRequest()->getParameter(
                    TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_ID,
                    FILTER_VALIDATE_INT
                );
            }
            // initialize the ActionForm with the data from the DTO
            $this->_getActionForm()->populate(
                $dto = $this->_getDelegate()->getChannelViewData(
                	TechDivision_Lang_Integer::valueOf(
                    	new TechDivision_Lang_String($channelId)
                	)
                )
            );
            // register the DTO in the Request
            $this->_getRequest()->setAttribute(
                TDProject_Channel_Controller_Util_WebRequestKeys::VIEW_DATA,
                $dto
            );
        } catch(Exception $e) {
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
        // return to the channel detail page
        return $this->_findForward(
            TDProject_Channel_Controller_Channel::CHANNEL_VIEW
        );
	}

   /**
     * This method is automatically invoked by the controller and implements
     * the functionality to create a new channel.
     *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
     */
    function createAction()
    {
        // return to the channel detail page
        return $this->_findForward(TDProject_Channel_Controller_Channel::CHANNEL_VIEW);
    }

	/**
	 * This method is automatically invoked by the controller and implements
	 * the functionality to save the passed channel data.
	 *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
	 */
	function saveAction()
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
			// save the channel
			$channelId = $this->_getDelegate()
			    ->saveChannel($actionForm->repopulate());
			// store the channel ID of the request
			$this->_getRequest()->setAttribute(
                TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_ID,
                $channelId->intValue()
            );
			// create the affirmation message
	        $actionMessages = new TechDivision_Controller_Action_Messages();
            $actionMessages->addActionMessage(
                new TechDivision_Controller_Action_Message(
                    TDProject_Channel_Controller_Util_MessageKeys::AFFIRMATION,
                    $this->translate('channelUpdate.successfull')
                )
            );
            // save the ActionMessages in the request
            $this->_saveActionMessages($actionMessages);
		} catch(TDProject_Channel_Common_Exceptions_ChannelNameNotUniqueException $cnnue) {
            // create action errors container
            $errors = new TechDivision_Controller_Action_Errors();
            // add error to container
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::NAME,
                    $this->translate('channel-name.not.unique')
                )
            );
            // save container in request
            $this->_saveActionErrors($errors);
            // return failure mapping
            return $this->create();
        } catch(TDProject_Channel_Common_Exceptions_ChannelAliasNotUniqueException $canue) {
            // create action errors container
            $errors = new TechDivision_Controller_Action_Errors();
            // add error to container
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::ALIAS,
                    $this->translate('channel-alias.not.unique')
                )
            );
            // save container in request
            $this->_saveActionErrors($errors);
            // return failure mapping
            return $this->create();
        } catch(Exception $e) {
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
		// return to the channel detail page
        return $this->editAction();
	}

	/**
     * This method is automatically invoked by the controller and implements
     * the functionality to delete the passed channel.
     *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
     */
    function deleteAction() {
        try {
            // load the channel ID from the request
        	$channelId = $this->_getRequest()->getParameter(
                TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_ID,
                FILTER_VALIDATE_INT
            );
            // delete the channel
            $this->_getDelegate()->deleteChannel(
                TechDivision_Lang_Integer::valueOf(
                    new TechDivision_Lang_String($channelId)
                )
            );
        } catch(Exception $e) {
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
        // return to the channel overview page
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