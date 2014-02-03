<?php

/**
 * TDProject_Channel_Aspect_Authorization_Ip_Hash
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
class TDProject_Channel_Aspect_Authorization_Ip_Hash
    extends TDProject_Channel_Aspect_Authorization_Ip
    implements TechDivision_AOP_Interfaces_Aspect
{

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
        try {
	        // invoke the parent method (check for uploader IP address)
	        if (parent::isAllowed($channelId, $ip) === false) {
	            return false;
	        }
	        // load the application instance
	        $app = TDProject_Factory::get();
	        // load the user's API-Hash from the request
	        $hash = $app->getRequest()->getParameter(
	            TDProject_Channel_Controller_Util_WebRequestKeys::HASH,
	            FILTER_SANITIZE_STRING
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
	        // load the name of the uploaded file
	        $filename = $uploader->getFilename();
	        // load the maintainer DTO by the passed hash
	        return $this->_getDelegate()
	            ->allowReleaseUpload(
	                new TechDivision_Lang_String($filename),
	                new TechDivision_Lang_String($hash)
	            );
        }
        catch(Exception $e) {
        	// return FALSE
        	return false;
        }
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
		return TDProject_Factory::get()->getObjectFactory()
			->newInstance('TDProject_Core_Controller_Helper_Upload');
	}
}