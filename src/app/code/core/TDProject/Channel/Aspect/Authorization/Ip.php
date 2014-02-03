<?php

/**
 * TDProject_Channel_Aspect_Authorization_Ip
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

require_once 'TechDivision/Lang/Object.php';
require_once 'TechDivision/AOP/Interfaces/Aspect.php';
require_once 'TechDivision/AOP/Interfaces/JoinPoint.php';

/**
 * This class is the Aspect used to authorize the system user
 * by using the Aspect adviced around.
 *
 * @category    TDProject
 * @package     TDProject_Channel
 * @subpackage	Aspect
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Aspect_Authorization_Ip
    extends TDProject_Core_Aspect_Authorization_Acl
    implements TechDivision_AOP_Interfaces_Aspect {

    /**
     * String for authorization process in progress.
     * @var string
     */
    const AUTHORIZATION_IN_PROGRESS = 'authorizationInProgress';
    
    /**
     * Dummy constructor to avoid object factory
     * initialization problems.
     * 
     * @return void
     */
    public function __construct()
    {
    	// dummy constructor	
    }

    /**
     * Start authorization process.
     *
     * @param TechDivision_Collections_Interfaces_Map $context
     * 		The Proxy context
     */
    public function setAuthorizationInProgess(
        TechDivision_Collections_Interfaces_Map $context) {
        $context->add(self::AUTHORIZATION_IN_PROGRESS, true);
    }

    /**
     * Stop the authorization process.
     *
     * @param TechDivision_Collections_Interfaces_Map $context
     * 		The Proxy context
     */
    public function setAuthorizationNotInProgress(
        TechDivision_Collections_Interfaces_Map $context) {
        $context->add(self::AUTHORIZATION_IN_PROGRESS, false);
    }

    /**
     * Returns TRUE if authorization is in progress, else FALSE.
     *
     * @param TechDivision_AOP_Interfaces_JoinPoint $context
     * 		The Proxy context
     * @return The flag if authorization is in progress or not
     */
    public function inAuthorizationProcess($context)
    {
        // check if the authentication flag is set, if not initialize it
        if ($context->exists(self::AUTHORIZATION_IN_PROGRESS) === false) {
            $context->add(self::AUTHORIZATION_IN_PROGRESS, false);
        }
        // return the flag
        return $context->get(self::AUTHORIZATION_IN_PROGRESS);
    }

    /**
     * This method forces authorization.
     *
     * @param TechDivision_AOP_Interfaces_JoinPoint $joinPoint
     * 		The actual JoinPoint
     * @return void
     */
    public function forceAuthorization(
        TechDivision_AOP_Interfaces_JoinPoint $joinPoint)
    {
        // load the Proxy context and the object the Aspect
        $context = $joinPoint->getProxyContext();
        // load the Proxy instance
		$aspectable = $joinPoint
    		->getMethodInterceptor()
            ->getAspectContainer()
            ->getAspectable();
        // load the application instance
        $app = TDProject_Factory::get();
        // load the user's IP address
        $ip = $app->getRequest()->getRemoteAddr();
        // check if already an authorization process is running
        if (!$this->inAuthorizationProcess($context)) {
            // set one running
            $this->setAuthorizationInProgess($context);
            // initialize the resource name to authorize
            $resourceId = $aspectable->getResourceId();
            // check if the resource is part of the ACL
            if ($this->isAllowed($resourceId, $ip)) {
                // finish authorization progress
                $this->setAuthorizationNotInProgress($context);
                // proceed with the JoinPoint and return
                return $joinPoint->proceed();
            }
			// create and add and save the error
			$errors = new TechDivision_Controller_Action_Errors();
			$errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Core_Controller_Util_ErrorKeys::SYSTEM_ERROR,
                    "Authorization for IP $ip failed"
                )
            );
			// adding the errors container to the request
            $app->getRequest()->setAttribute(
            	TechDivision_Controller_Action_Errors::ACTION_ERRORS,
            	$errors
            );
            // is thrown if user is not authorized to use resource
	        $aspectable->forward(
	            TDProject_Core_Controller_Util_GlobalForwardKeys::SYSTEM_ERROR
	        );
            // finish authorization process
            $this->setAuthorizationNotInProgress($context);
            // return
            return;
        }
        // proceed with the JoinPoint and return
        return $joinPoint->proceed();
    }

    /**
     * Checks if the passed IP is allowed to use resources
     * of the channel with the passed ID.
     *
     * @param TechDivision_Lang_Integer $channelId
     * 		ID of the channel to check the passed IP for
     * @param string $ip The IP to check against the channel
     * @return boolean TRUE if the IP is allowed to use the channel's resources
     */
    public function isAllowed(TechDivision_Lang_Integer $channelId, $ip)
    {
        // load the channel
        $channel = $this->_getDelegate()->getChannelViewData($channelId);
        // load the channel's allowed networks
        $allowedNetworks = $channel->getAllowedNetworks()->split("/[\s,]/");
        // check if IP is in one of the allowed networks
        foreach ($allowedNetworks as $allowedNetwork) {
            if (Net_IPv4::ipInNetwork($ip, $allowedNetwork) || 
            	Net_IPv6::isInNetmask($ip, $allowedNetwork)) {
		        // return TRUE to allow resource
                return true;
            }
        }
        // load the channel's allowed IP's
        $allowedIps = $channel->getAllowedIps()->split("/[\s,]/");
        // check if the IP is in one of the allowed IP's
        if (in_array($ip, $allowedIps)) {
            return true;
        }
        // if NOT, return FALSE
        return false;
    }

	/**
	 * This method returns the delegate for calling
	 * the backend functions.
	 *
	 * @return TDProject_Channel_Common_Delegates_DomainProcessorDelegateUtil
	 * 		The requested delegate
	 */
	protected function _getDelegate()
	{
		return TDProject_Channel_Common_Delegates_DomainProcessorDelegateUtil::getDelegate(TDProject_Factory::get());
	}
}