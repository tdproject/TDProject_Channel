<?php

/**
 * TDProject_Channel_Aspect_Core_Controller_Router_Resource
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
 * @package     TDProject_Core
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Aspect_Core_Controller_Router_Resource
    extends TechDivision_Lang_Object
    implements TechDivision_AOP_Interfaces_Aspect {

    /**
     * The PCRE regex to identify a channel request.
     * @var string
     */
    const PCRE_REGEX = '/\/(?P<alias>\w+)\/.*/';
    
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
     * This method forces authorization.
     *
     * @param TechDivision_AOP_Interfaces_JoinPoint $joinPoint
     * 		The actual JoinPoint
     * @return void
     */
    public function setSource(
        TechDivision_AOP_Interfaces_JoinPoint $joinPoint) 
    {
		// load the methods arguments
		$source = end($joinPoint->getArguments());
		// initialize the subject for the regular expression
		$subject = trim(implode('/', $source));
		// try to check if an alias is available
		if (preg_match(self::PCRE_REGEX, $subject, $matches)) {
			// initialize the alias
			$alias = new TechDivision_Lang_String($matches['alias']);
			// try to load the channel with the found alias
			$channel = $this->_getDelegate()->getChannelViewDataByAlias($alias);
			// check if a channel has been found
			if ($channel != null) {				
				// initialize the method params
				$newSource = array('', 'channel/api', 'channelId', $channel->getChannelId()->intValue());
				// prepare the new method params
				for ($i = 2; $i < sizeof($source); $i++) {
				$newSource[] = $source[$i];
				}
				// replace the original method params
				$joinPoint->setArguments(array(0 => $newSource));				
			}
		}
        // proceed with the JoinPoint and return
        return $joinPoint->proceed();
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