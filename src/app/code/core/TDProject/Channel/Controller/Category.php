<?php

/**
 * TDProject_Channel_Controller_Category
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
class TDProject_Channel_Controller_Category
    extends TDProject_Channel_Controller_Abstract {

	/**
	 * The key for the ActionForward to the category overview template.
	 * @var string
	 */
	const CATEGORY_OVERVIEW = "CategoryOverview";

	/**
	 * The key for the ActionForward to the category view template.
	 * @var string
	 */
	const CATEGORY_VIEW = "CategoryView";

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
	 * the functionality to load a list with with all categories.
	 *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
	 */
	function __defaultAction()
	{
		try {
			// replace the default ActionForm
			$this->getContext()->setActionForm(
				new TDProject_Channel_Block_Category_Overview($this->getContext())
			);
            // load and register the category overview data
            $this->_getRequest()->setAttribute(
            	TDProject_Core_Controller_Util_WebRequestKeys::OVERVIEW_DATA,
            	$this->_getDelegate()->getCategoryOverviewData()
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
		    TDProject_Channel_Controller_Category::CATEGORY_OVERVIEW
		);
	}

	/**
	 * This method is automatically invoked by the controller and implements
	 * the functionality to load the category data with the id passed in the
	 * request for editing it.
	 *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
	 */
	function editAction()
	{
        try {
            // try to load the category ID from the Request
            if (($categoryId = $this->_getRequest()->getAttribute(
                TDProject_Channel_Controller_Util_WebRequestKeys::CATEGORY_ID)) == null) {
                $categoryId = $this->_getRequest()->getParameter(
                    TDProject_Channel_Controller_Util_WebRequestKeys::CATEGORY_ID,
                    FILTER_VALIDATE_INT
                );
            }
            // initialize the ActionForm with the data from the DTO
            $this->_getActionForm()->populate(
                $dto = $this->_getDelegate()->getCategoryViewData(
                	TechDivision_Lang_Integer::valueOf(
                    	new TechDivision_Lang_String($categoryId)
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
        // return to the category detail page
        return $this->_findForward(
            TDProject_Channel_Controller_Category::CATEGORY_VIEW
        );
	}

   /**
     * This method is automatically invoked by the controller and implements
     * the functionality to create a new category.
     *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
     */
    function createAction()
    {
        // return to the category detail page
        return $this->_findForward(TDProject_Channel_Controller_Category::CATEGORY_VIEW);
    }

	/**
	 * This method is automatically invoked by the controller and implements
	 * the functionality to save the passed category data.
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
			// save the category
			$categoryId = $this->_getDelegate()
			    ->saveCategory($actionForm->repopulate());
			// store the category ID of the request
			$this->_getRequest()->setAttribute(
                TDProject_Channel_Controller_Util_WebRequestKeys::CATEGORY_ID,
                $categoryId->intValue()
            );
			// create the affirmation message
	        $actionMessages = new TechDivision_Controller_Action_Messages();
            $actionMessages->addActionMessage(
                new TechDivision_Controller_Action_Message(
                    TDProject_Channel_Controller_Util_MessageKeys::AFFIRMATION,
                    $this->translate('categoryUpdate.successfull')
                )
            );
            // save the ActionMessages in the request
            $this->_saveActionMessages($actionMessages);
		} catch(TDProject_Channel_Common_Exceptions_CategoryNameNotUniqueException $cnnue) {
            // create action errors container
            $errors = new TechDivision_Controller_Action_Errors();
            // add error to container
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::NAME,
                    $this->translate('category-name.not.unique')
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
		// return to the category detail page
        return $this->editAction();
	}

	/**
     * This method is automatically invoked by the controller and implements
     * the functionality to delete the passed category.
     *
	 * @return TechDivision_Controller_Action_Forward Returns a ActionForward
     */
    function deleteAction() {
        try {
            // load the category ID from the request
        	$categoryId = $this->_getRequest()->getParameter(
                TDProject_Channel_Controller_Util_WebRequestKeys::CATEGORY_ID,
                FILTER_VALIDATE_INT
            );
            // delete the category
            $this->_getDelegate()->deleteCategory(
                TechDivision_Lang_Integer::valueOf(
                    new TechDivision_Lang_String($categoryId)
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
        // return to the category overview page
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