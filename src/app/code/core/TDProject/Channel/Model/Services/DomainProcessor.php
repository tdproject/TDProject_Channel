<?php

/**
 * TDProject_Channel_Model_Services_DomainProcessor
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
 * @subpackage	Model
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Model_Services_DomainProcessor
    extends TDProject_Channel_Model_Services_AbstractDomainProcessor
{

	/**
	 * This method returns the logger of the requested
	 * type for logging purposes.
	 *
     * @param string The log type to use
	 * @return TechDivision_Logger_Logger Holds the Logger object
	 * @throws Exception Is thrown if the requested logger type is not initialized or doesn't exist
	 * @deprecated 0.1.20 - 2011/12/19
	 */
    protected function _getLogger(
        $logType = TechDivision_Logger_System::LOG_TYPE_SYSTEM)
    {
    	return $this->getLogger();
    }   
    
    /**
     * This method returns the logger of the requested
     * type for logging purposes.
     *
     * @param string The log type to use
     * @return TechDivision_Logger_Logger Holds the Logger object
     * @since 0.1.21 - 2011/12/19
     */
    public function getLogger(
    	$logType = TechDivision_Logger_System::LOG_TYPE_SYSTEM)
    {
    	return $this->getContainer()->getLogger();
    } 

	/**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#getChannelViewData(TechDivision_Lang_Integer $channelId = null)
     */
	public function getChannelViewData(
	    TechDivision_Lang_Integer $channelId = null)
	{
    	try {
            // load and return the channel with the passed ID
            return TDProject_Channel_Model_Assembler_Channel::create($this->getContainer())
                ->getChannelViewData($channelId);
	    }
	    catch(TechDivision_Model_Interfaces_Exception $e) {
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
	}

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#getChannelOverviewData()
     */
	public function getChannelOverviewData()
	{
	    try {
            // load and return all users
            return TDProject_Channel_Model_Assembler_Channel::create($this->getContainer())
                ->getChannelOverviewData();
	    }
	    catch(TechDivision_Model_Interfaces_Exception $e) {
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
    }

	/**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#saveChannel(TDProject_Channel_Common_ValueObjects_ChannelLightValue $lvo)
     */
	public function saveChannel(
        TDProject_Channel_Common_ValueObjects_ChannelLightValue $lvo)
    {
		try {
			// begin the transaction
			$this->beginTransaction();
			// save the channel
			$channelId = TDProject_Channel_Model_Actions_Channel::create($this->getContainer())
			    ->saveChannel($lvo);
			// commit the transaction
			$this->commitTransaction();
			// return the channel ID
			return $channelId;
		}
		catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
	}

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#deleteChannel(TechDivision_Lang_Integer $channelId)
     */
    public function deleteChannel(TechDivision_Lang_Integer $channelId)
    {
        try {
            // start the transaction
            $this->beginTransaction();
            // load the channel
            $channel = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
                ->findByPrimaryKey($channelId);
            // delete the channel
            $channel->delete();
            // commit the transcation
            $this->commitTransaction();
        }
        catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
            // log the exception message
        	$this->_getLogger()->error($e->__toString());
        	// throw a new exception
        	throw new TDProject_Core_Common_Exceptions_SystemException(
        		$e->__toString()
        	);
        }
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#getCategoryViewData(TechDivision_Lang_Integer $categoryId = null)
     */
    public function getCategoryViewData(
    	TechDivision_Lang_Integer $categoryId = null)
    {
    	try {
    		// load and return the category with the passed ID
    		return TDProject_Channel_Model_Assembler_Category::create($this->getContainer())
    			->getCategoryViewData($categoryId);
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#getCategoryOverviewData()
     */
    public function getCategoryOverviewData()
    {
    	try {
    		// load and return all categories
    		return TDProject_Channel_Model_Assembler_Category::create($this->getContainer())
    			->getCategoryOverviewData();
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#saveCategory(TDProject_Channel_Common_ValueObjects_CategoryLightValue $lvo)
     */
    public function saveCategory(
    	TDProject_Channel_Common_ValueObjects_CategoryLightValue $lvo)
    {
    	try {
    		// begin the transaction
    		$this->beginTransaction();
    		// save the category
    		$categoryId = TDProject_Channel_Model_Actions_Category::create($this->getContainer())
    			->saveCategory($lvo);
    		// commit the transaction
    		$this->commitTransaction();
    		// return the category ID
    		return $categoryId;
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#deleteCategory(TechDivision_Lang_Integer $categoryId)
     */
    public function deleteCategory(TechDivision_Lang_Integer $categoryId)
    {
    	try {
    		// start the transaction
    		$this->beginTransaction();
    		// load the category
    		$category = TDProject_Channel_Model_Utils_CategoryUtil::getHome($this->getContainer())
    			->findByPrimaryKey($categoryId);
    		// delete the category
    		$category->delete();
    		// commit the transcation
    		$this->commitTransaction();
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#getChannelPackageViewData(TechDivision_Lang_Integer $channelPackageId = null)
     */
    public function getChannelPackageViewData(
    	TechDivision_Lang_Integer $channelPackageId = null)
    {
    	try {
    		// load and return the channel package with the passed ID
    		return TDProject_Channel_Model_Assembler_ChannelPackage::create($this->getContainer())
    			->getChannelPackageViewData($channelPackageId);
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#getChannelPackageOverviewData()
     */
    public function getChannelPackageOverviewData()
    {
    	try {
    		// load and return all channel packages
    		return TDProject_Channel_Model_Assembler_ChannelPackage::create($this->getContainer())
    			->getChannelPackageOverviewData();
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#saveChannelPackage(TDProject_Channel_Common_ValueObjects_ChannelPackageLightValue $lvo)
     */
    public function saveChannelPackage(
    	TDProject_Channel_Common_ValueObjects_ChannelPackageLightValue $lvo)
    {
    	try {
    		// begin the transaction
    		$this->beginTransaction();
    		// save the channel package
    		$channelPackageId = TDProject_Channel_Model_Actions_ChannelPackage::create($this->getContainer())
    			->saveChannelPackage($lvo);
    		// commit the transaction
    		$this->commitTransaction();
    		// return the channel package ID
    		return $channelPackageId;
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#deleteChannelPackage(TechDivision_Lang_Integer $channelPackageId)
     */
    public function deleteChannelPackage(
    	TechDivision_Lang_Integer $channelPackageId)
    {
    	try {
    		// start the transaction
    		$this->beginTransaction();
    		// load the channel package
    		$channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
    			->findByPrimaryKey($channelPackageId);
    		// delete the channel package
    		$channelPackage->delete();
    		// commit the transcation
    		$this->commitTransaction();
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

	/**
     * (non-PHPdoc)
     * @see TDProject/Core/Common/Delegates/Interfaces/DomainProcessorDelegate#getResourceViewData(TechDivision_Lang_String $baseUrl, TechDivision_Lang_Integer $channelId, TechDivision_Lang_String $resourceUri)
     */
	public function getResourceViewData(
	    TechDivision_Lang_String $baseUrl,
	    TechDivision_Lang_Integer $channelId,
	    TechDivision_Lang_String $resourceUri)
	{
    	try {
            // load and return the resource for the channel with the passed ID
            return TDProject_Channel_Model_Assembler_Channel_Api::create($this->getContainer())
                ->getResourceViewData($baseUrl, $channelId, $resourceUri);
	    } catch(TechDivision_Model_Interfaces_Exception $e) {
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
	}

	/**
     * (non-PHPdoc)
     * @see TDProject/Core/Common/Delegates/Interfaces/DomainProcessorDelegate#getChannelViewDataByAlias(TechDivision_Lang_String $alias)
     */
	public function getChannelViewDataByAlias(TechDivision_Lang_String $alias)
	{
    	try {
            // load and return the channel with the passed alias
            $channels = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
            	->findAllByAlias($alias);
            // return the first channel 
            foreach ($channels as $channel) {
            	return $channel->getLightValue();
            }
	    } catch(TechDivision_Model_Interfaces_Exception $e) {
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
	}

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#deleteRelease(TechDivision_Lang_Integer $releaseId)
     */
    public function deleteRelease(TechDivision_Lang_Integer $releaseId)
    {
    	try {
    		// start the transaction
    		$this->beginTransaction();
    		// load the release
    		$release = TDProject_Channel_Model_Utils_ReleaseUtil::getHome($this->getContainer())
    			->findByPrimaryKey($releaseId);
    		// delete the release
    		$release->delete();
    		// commit the transcation
    		$this->commitTransaction();
    		// return the package ID of the deleted release
    		return $release->getPackageIdFk();
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#createBinaryRelease(TechDivision_Lang_Integer $channelId, TechDivision_Lang_String $targetFilename)
     */
    public function createBinaryRelease(
    	TechDivision_Lang_Integer $channelId,
    	TechDivision_Lang_String $targetFilename)
    {
    	try {
    		// begin the transaction
    		$this->beginTransaction();
    		// save the release
    		$releaseId = TDProject_Channel_Model_Actions_Release::create($this->getContainer())
    			->createBinaryRelease($channelId, $targetFilename);
    		// commit the transaction
    		$this->commitTransaction();
    		// return the release ID
    		return $releaseId;
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#getDownloadUrl(TechDivision_Lang_Integer $channelId, TechDivision_Lang_String $filename)
     */
    public function getDownloadUrl(
    	TechDivision_Lang_Integer $channelId,
    	TechDivision_Lang_String $filename)
    {
    	try {
    		// load and return the download URL
    		return TDProject_Channel_Model_Actions_Release::create($this->getContainer())
    			->getDownloadUrl($channelId, $filename);
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/ERP/Common/Delegates/Interfaces/DomainProcessorDelegate#relateMaintainer(TDProject_Channel_Common_ValueObjects_MaintainerLightValue $lvo)
     */
    public function relateMaintainer(
        TDProject_Channel_Common_ValueObjects_MaintainerLightValue $lvo)
    {
        try {
            // begin the transaction
            $this->beginTransaction();
            // create the relation
			TDProject_Channel_Model_Actions_Maintainer::create($this->getContainer())
				->createAndAllow($lvo);
            // commit the transaction
            $this->commitTransaction();
        } catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/ERP/Common/Delegates/Interfaces/DomainProcessorDelegate#unrelateMaintainer(TDProject_Channel_Common_ValueObjects_MaintainerLightValue $lvo)
     */
    public function unrelateMaintainer(
        TDProject_Channel_Common_ValueObjects_MaintainerLightValue $lvo)
    {
        try {
            // begin the transaction
            $this->beginTransaction();
            // delete the relation
			TDProject_Channel_Model_Actions_Maintainer::create($this->getContainer())
				->deleteAndDeny($lvo);
            // commit the transaction
            $this->commitTransaction();
        } catch(TechDivision_Model_Interfaces_Exception $e) {
            // rollback the transaction
            $this->rollbackTransaction();
            // log the exception message
            $this->_getLogger()->error($e->__toString());
            // throw a new exception
            throw new TDProject_Core_Common_Exceptions_SystemException(
                $e->__toString()
            );
        }
    }

    /**
     * (non-PHPdoc)
     * @see TDProject/Channel/Common/Delegates/Interfaces/DomainProcessorDelegate#allowReleaseUpload(TechDivision_Lang_String $filename, TechDivision_Lang_String $hash)
     */
    public function allowReleaseUpload(
    	TechDivision_Lang_String $filename,
    	TechDivision_Lang_String $hash)
    {
    	try {
    		// check if release upload is allowed
    		return TDProject_Channel_Model_Actions_Release::create($this->getContainer())
    			->allowReleaseUpload($filename, $hash);
    	}
    	catch(TechDivision_Model_Interfaces_Exception $e) {
    		// log the exception message
    		$this->_getLogger()->error($e->__toString());
    		// throw a new exception
    		throw new TDProject_Core_Common_Exceptions_SystemException(
    			$e->__toString()
    		);
    	}
    }
}