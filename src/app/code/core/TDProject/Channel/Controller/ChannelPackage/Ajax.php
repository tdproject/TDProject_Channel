<?php

/**
 * TDProject_Channel_Controller_ChannelPackage_Ajax
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
class TDProject_Channel_Controller_ChannelPackage_Ajax
    extends TDProject_Channel_Controller_Abstract 
{

    /**
     * This method is automatically invoked by the controller and implements
     * the functionality to relate a user (maintainer) with the actual project.
     *
     * @return ActionForward Returns a ActionForward
     */
    public function relateMaintainerAction()
    {
        try {
            // load the user ID from the request
            $userId = TechDivision_Lang_Integer::valueOf(
                new TechDivision_Lang_String(
                    $this->_getRequest()->getParameter(
                    	TDProject_Channel_Controller_Util_WebRequestKeys::USER_ID_FK,
                    	FILTER_VALIDATE_INT
                    )
                )
            );
            // load the channel package ID from the request
            $channelPackageId = TechDivision_Lang_Integer::valueOf(
                new TechDivision_Lang_String(
                    $this->_getRequest()->getParameter(
                    	TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_PACKAGE_ID,
                    	FILTER_VALIDATE_INT
                    )
                )
            );
            // load the maintainer role from the request
            $role = new TechDivision_Lang_String(
            	$this->_getRequest()->getParameter(
                	TDProject_Channel_Controller_Util_WebRequestKeys::ROLE,
                	FILTER_SANITIZE_STRING
            	)
            );
            // load the maintainer state from the request
            $active = new TechDivision_Lang_Boolean(
            	$this->_getRequest()->getParameter(
                	TDProject_Channel_Controller_Util_WebRequestKeys::ACTIVE,
                	FILTER_SANITIZE_STRING
            	)
            );
            // initialize a new LVO
            $lvo = new TDProject_Channel_Common_ValueObjects_MaintainerLightValue();
            $lvo->setChannelPackageIdFk($channelPackageId);
            $lvo->setUserIdFk($userId);
            $lvo->setRole($role);
            $lvo->setActive($active);
            // save the channel-package-maintainer (maintainer) relations
            $this->_getDelegate()->relateMaintainer($lvo);
            // create the affirmation message
	        $actionMessages = new TechDivision_Controller_Action_Messages();
            $actionMessages->addActionMessage(
                new TechDivision_Controller_Action_Message(
                    TDProject_Project_Controller_Util_MessageKeys::AFFIRMATION,
                    $this->translate('maintainerRelate.successfull')
                )
            );
            // save the ActionMessages in the request
            $this->_saveActionMessages($actionMessages);
        } catch(Exception $e) {
            // create and add and save the error
            $errors = new TechDivision_Controller_Action_Errors();
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Project_Controller_Util_ErrorKeys::SYSTEM_ERROR,
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
        // return the ActionForward for rendering the system messages
        return $this->_findForward(
			TDProject_Core_Controller_Util_GlobalForwardKeys::SYSTEM_MESSAGES
		);
    }

    /**
     * This method is automatically invoked by the controller and implements
     * the functionality to unrelate a user (maintainer) with the actual project.
     *
     * @return ActionForward Returns a ActionForward
     */
    public function unrelateMaintainerAction()
    {
        try {
            // load the user ID from the request
            $userId = TechDivision_Lang_Integer::valueOf(
                new TechDivision_Lang_String(
                    $this->_getRequest()->getParameter(
                    	TDProject_Channel_Controller_Util_WebRequestKeys::USER_ID_FK,
                    	FILTER_VALIDATE_INT
                    )
                )
            );
            // load the channel package ID from the request
            $channelPackageId = TechDivision_Lang_Integer::valueOf(
                new TechDivision_Lang_String(
                    $this->_getRequest()->getParameter(
                    	TDProject_Channel_Controller_Util_WebRequestKeys::CHANNEL_PACKAGE_ID,
                    	FILTER_VALIDATE_INT
                    )
                )
            );
            // load the maintainer role from the request
            $role = new TechDivision_Lang_String(
            	$this->_getRequest()->getParameter(
                	TDProject_Channel_Controller_Util_WebRequestKeys::ROLE,
                	FILTER_SANITIZE_STRING
            	)
            );
            // load the maintainer state from the request
            $active = new TechDivision_Lang_Boolean(
            	$this->_getRequest()->getParameter(
                	TDProject_Channel_Controller_Util_WebRequestKeys::ACTIVE,
                	FILTER_SANITIZE_STRING
            	)
            );
            // initialize a new LVO
            $lvo = new TDProject_Channel_Common_ValueObjects_MaintainerLightValue();
            $lvo->setChannelPackageIdFk($channelPackageId);
            $lvo->setUserIdFk($userId);
            $lvo->setRole($role);
            $lvo->setActive($active);
            // delete the channel-package-user (maintainer) relations
            $this->_getDelegate()->unrelateMaintainer($lvo);
            // create the affirmation message
	        $actionMessages = new TechDivision_Controller_Action_Messages();
            $actionMessages->addActionMessage(
                new TechDivision_Controller_Action_Message(
                    TDProject_Project_Controller_Util_MessageKeys::AFFIRMATION,
                    $this->translate('maintainerUnrelate.successfull')
                )
            );
            // save the ActionMessages in the request
            $this->_saveActionMessages($actionMessages);
        } catch(Exception $e) {
            // create and add and save the error
            $errors = new TechDivision_Controller_Action_Errors();
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Project_Controller_Util_ErrorKeys::SYSTEM_ERROR,
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
        // return the ActionForward for rendering the system messages
        return $this->_findForward(
			TDProject_Core_Controller_Util_GlobalForwardKeys::SYSTEM_MESSAGES
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
	    TechDivision_Controller_Action_Forward $actionForward) {
	    // check if the class required to initialize the Block is included
	    if (!class_exists($path = $actionForward->getPath())) {
	        return;
	    }
	    // initialize the messages and add the Block
	    $page = new $path($this->getContext());
	    // register the Block in the Request
	    $this->_getRequest()->setAttribute($path, $page);
	}
}