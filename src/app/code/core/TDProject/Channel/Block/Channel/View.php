<?php

/**
 * TDProject_Channel_Block_Channel_View
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category    TDProject
 * @package     TDProject_Channel
 * @subpackage	View
 * @copyright   Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license     http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Block_Channel_View
	extends TDProject_Channel_Block_Abstract_Channel {

	/**
     * (non-PHPdoc)
     * @see TDProject/Interfaces/Block#prepareLayout()
     */
    public function prepareLayout() {
    	// initialize the tabs
    	$tabs = $this->addTabs('tabs', 'Tabs');
        // add the tab for the channel data
        $tab = $tabs->addTab(
        	'channel', 'Channel Data'
        );
        // add a fieldset for the channel data
    	$tab->addFieldset(
    		'channel', 'Channel Data'
    	)
		->addElement(
		    $this->getElement('textfield', 'name', 'Name')->setMandatory()
        )
    	->addElement(
    	    $this->getElement('textfield', 'alias', 'Alias')->setMandatory()
    	)
        ->addElement(
            $this->getElement('textarea', 'summary', 'Summary')
        )
    	->addElement(
    	    $this->getElement(
    	    	'checkbox', 'createPackage', 'Create package if not available'
    	    )
    	);
        // add a fieldset for authorization
        $tab->addFieldset(
    		'authorization', 'Authorization'
    	)
    	->addElement(
    	    $this->getElement(
    	    	'textfield', 'allowedNetworks', 'Allowed networks (comma separated)'
    	    )
    	)
    	->addElement(
    	    $this->getElement(
    	    	'textfield', 'allowedIps', 'Allowed IP\'s (comma separated)'
    	    )
    	);
        // check if a new channel will be created, if yes don't show upload
        if (!$this->getChannelId()->equals(new TechDivision_Lang_Integer(0))) {
            // add a fieldset for the uploads
            $tab->addFieldset(
        		'upload', 'Upload Release'
        	)
            ->addElement(
                $this->getElement('file', 'release', 'Release')
            );
        }
	    // return the instance itself
	    return parent::prepareLayout();
    }

    /**
     * The target URL to handle the release binary uploads.
     *
     * @return string The URL to handle the uploads
     */
    public function getUploadUrl()
    {
    	return $this->getUrl(
    		array(
    			'path' => '/channel/release/upload',
    			'channelId' => $this->getChannelId()
    		)
    	);
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Core_Block_Abstract::repopulate()
     */
    public function repopulate()
    {
        // initialize the DTO with the data to save
		$lvo = new TDProject_Channel_Common_ValueObjects_ChannelLightValue();
		$lvo->setChannelId($this->getChannelId());
		$lvo->setName($this->getName());
		$lvo->setAlias($this->getAlias());
		$lvo->setSummary($this->getSummary());
        $lvo->setCreatePackage($this->getCreatePackage());
        $lvo->setAllowedNetworks($this->getAllowedNetworks());
        $lvo->setAllowedIps($this->getAllowedIps());
        // return the initialized DTO
    	return $lvo;
    }

    /**
     * This method checks if the values in the member variables
     * holds valid data. If not, a ActionErrors container will
     * be initialized an for every incorrect value a ActionError
     * object with the apropriate error message will be added.
     *
     * @return ActionErrors Returns a ActionErrors container with ActionError objects
     */
    public function validate()
    {
        // initialize the ActionErrors
        $errors = new TechDivision_Controller_Action_Errors();
        // check if a name was entered
        if ($this->_name->length() == 0) {
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::NAME,
                    $this->translate('channel-name.none')
                )
            );
        }
        // check if a alias was entered
        if ($this->_alias->length() == 0) {
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::ALIAS,
                    $this->translate('channel-alias.none')
                )
            );
        }
        // return the ActionErrors
        return $errors;
    }
}