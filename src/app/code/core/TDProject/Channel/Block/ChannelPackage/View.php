<?php

/**
 * TDProject_Channel_Block_ChannelPackage_View
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
class TDProject_Channel_Block_ChannelPackage_View
	extends TDProject_Channel_Block_Abstract_ChannelPackage
{

    /**
     * The selected project-user relations.
     * @var TechDivision_Collections_ArrayList
     */
    protected $_userIdFk = null;

    /**
     * The available users.
     * @var TechDivision_Collections_ArrayList
     */
    protected $_users = null;

    /**
     * The categories available in the system.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_categories = null;

    /**
     * The channels available in the system.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_channels = null;

    /**
     * The available maintainer states.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_mainteinerStates = null;

    /**
     * The available maintainer roles.
     * @var TechDivision_Collections_Interfaces_Collection
     */
    protected $_maintainerRoles = null;

    /**
     * Sets the available categories.
     *
     * @param TechDivision_Collections_Interfaces_Collection $categories
     * 		The categories available in the system
     * @return void
     */
    public function setCategories(
        TechDivision_Collections_Interfaces_Collection $categories)
    {
        $this->_categories = $categories;
    }

    /**
     * Returns the available categories.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The categories available in the system
     */
    public function getCategories()
    {
        return $this->_categories;
    }

    /**
     * Sets the available channels.
     *
     * @param TechDivision_Collections_Interfaces_Collection $channels
     * 		The channels available in the system
     * @return void
     */
     public function setChannels(
         TechDivision_Collections_Interfaces_Collection $channels)
     {
         $this->_channels = $channels;
     }

    /**
     * Returns the available channels.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The channels available in the system
     */
    public function getChannels()
    {
        return $this->_channels;
    }

    /**
     * Getter method for the available users.
     *
     * @return TechDivision_Lang_Integer The available users
     */
    public function getUsers()
    {
        return $this->_users;
    }

    /**
     * Setter method for the available users.
     *
     * @param TechDivision_Collections_Interfaces_Collection $users The available users
     * @return void
     */
    public function setUsers(
        TechDivision_Collections_Interfaces_Collection $users)
    {
        $this->_users = $users;
    }

    /**
     * Getter method for the selected project-user relations.
     *
     * @return array The selected project-user relations
     */
    public function getUserIdFk()
    {
        return $this->_userIdFk;
    }

    /**
     * Setter method for the selected project-user relations.
     *
     * @param array $userIdFk The selected project-user relations.
     * @return void
     */
    public function setUserIdFk(array $userIdFk)
    {
    	foreach ($userIdFk as $value) {
        	$this->_userIdFk->add((integer) $value);
    	}
    }

    /**
     * Sorts the users according if the user is related
     * with the package (as maintainer) or not.
     *
     * @return TDProject_Channel_Block_Package_View
     * 		The instance itself
     */
    protected function _sortUsers()
    {
    	// sort the addresses
        TechDivision_Collections_CollectionUtils::sort(
    		$this->getUsers(),
        	new TDProject_Project_Block_Project_View_UserSortComparator(
        		$this->getUserIdFk()
        	)
        );
        // return the instance
        return $this;
    }

    /**
     * Returns the available maintainer roles.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The available maintainer roles
     */
    public function getMaintainerRoles()
    {
    	return $this->_maintainerRoles;
    }

    /**
     * Setter method for the available maintainer roles.
     *
     * @param TechDivision_Collections_Interfaces_Collection $maintainerRoles The available maintainer roles
     * @return void
     */
    public function setMaintainerRoles(
        TechDivision_Collections_Interfaces_Collection $maintainerRoles)
    {
        $this->_maintainerRoles = $maintainerRoles;
    }

    /**
     * Returns the available maintainer states.
     *
     * @return TechDivision_Collections_Interfaces_Collection
     * 		The available maintainer states
     */
    public function getMaintainerStates()
    {
    	return $this->_maintainerStates;
    }

    /**
     * Setter method for the available maintainer states.
     *
     * @param TechDivision_Collections_Interfaces_Collection $maintainerStates The available maintainer states
     * @return void
     */
    public function setMaintainerStates(
        TechDivision_Collections_Interfaces_Collection $maintainerStates)
    {
        $this->_maintainerStates = $maintainerStates;
    }

    /**
     * Returns a HashMap with the ID's of the selected maintainer roles.
     * 
     * @return TechDivision_Collections_HashMap 
     * 		The HashMap with the maintainer roles
     */
    public function getSelectedMaintainerRoles()
    {
    	$list = new TechDivision_Collections_HashMap();
    	// set the user ID's of the selected roles
    	foreach ($this->getMaintainers() as $maintainer) {
	    	$list->add(
		    	$maintainer->getUserIdFk()->intValue(),
		    	$maintainer->getRole()->stringValue()
	    	);
    	}
    	return $list;
    }

    /**
     * Returns a HashMap with the ID's of the selected maintainer states.
     * 
     * @return TechDivision_Collections_HashMap 
     * 		The HashMap with the maintainer states
     */
    public function getSelectedMaintainerStates()
    {
    	$list = new TechDivision_Collections_HashMap();
    	// set the user ID's of the selected states
    	foreach ($this->getMaintainers() as $maintainer) {
	    	$list->add(
		    	$maintainer->getUserIdFk()->intValue(),
		    	$maintainer->getActive()->booleanValue()
	    	);
    	}
    	return $list;
    }

	/**
     * (non-PHPdoc)
     * @see TDProject/Interfaces/Block#prepareLayout()
     */
    public function prepareLayout()
    {
    	// initialize the tabs
    	$tabs = $this->addTabs('tabs', 'Tabs');
        // add the tab for the package data
        $tab = $tabs->addTab(
        	'package', 'Package Data'
        );
        // add a new fieldset for the package information
    	$tab->addFieldset(
    		'package', 'Package Data'
    	)
		->addElement(
		    $this->getElement('select', 'channelIdFk', 'Channel')->setOptions($this->getChannels())
        )
		->addElement(
		    $this->getElement('select', 'categoryIdFk', 'Category')->setOptions($this->getCategories())
        )
		->addElement(
		    $this->getElement('textfield', 'name', 'Name')->setMandatory()
        )
    	->addElement(
    	    $this->getElement('textfield', 'shortDescription', 'Short Description')
    	)
    	->addElement(
    	    $this->getElement('textfield', 'licence', 'Licence')->setMandatory()
    	)
    	->addElement(
    	    $this->getElement('textfield', 'licenceUri', 'Licence URI')->setMandatory()
    	)
        ->addElement(
            $this->getElement('textarea', 'description', 'Description')
        );
		// add a new fieldset for the deprecated information
        $tab->addFieldset(
        	'deprecated', 'Deprecated'
        )
        ->addElement(
            $this->getElement('checkbox', 'deprecated', 'Deprecated')
        )
        ->addElement(
            $this->getElement('textfield', 'deprecatedChannel', 'Channel')
        )
        ->addElement(
            $this->getElement('textfield', 'deprecatedPackage', 'Package')
        );
        // add the tab for the release data
        $tab = $tabs->addTab(
        	'release', 'Releases'
        )
        ->addGrid($this->_prepareReleaseGrid());
        // add the tab for the release data
        $tab = $tabs->addTab(
        	'users', 'Maintainers'
        )
        ->addGrid($this->_prepareUserGrid());
	    // return the instance itself
	    return parent::prepareLayout();
    }

    /**
     * Initializes and returns the grid for the package releases.
     *
     * @return TDProject_Core_Block_Widget_Grid
     * 		The initialized grid
     */
    protected function _prepareReleaseGrid()
    {
    	// instanciate the grid
    	$grid = new TDProject_Core_Block_Widget_Grid($this, 'releaseGrid', 'Releases');
    	// set the collection with the data to render
    	$grid->setCollection($this->getReleases());
    	// add the columns
    	$grid->addColumn(
    	    new TDProject_Core_Block_Widget_Grid_Column(
    	    	'releaseId', 'ID', 10
    	    )
    	);
    	$grid->addColumn(
    	    new TDProject_Core_Block_Widget_Grid_Column(
    	    	'version', 'Version', 10
    	    )
    	);
    	$grid->addColumn(
    	    new TDProject_Core_Block_Widget_Grid_Column(
    	    	'licence', 'Licence', 10
    	    )
    	);
    	$grid->addColumn(
    	    new TDProject_Core_Block_Widget_Grid_Column(
    	    	'stability', 'Stability', 10
    	    )
    	);
    	$grid->addColumn(
    	    new TDProject_Core_Block_Widget_Grid_Column(
    	    	'shortDescription', 'Short Description', 45
    	    )
    	);
    	// add the actions
    	$action = new TDProject_Core_Block_Widget_Grid_Column_Actions(
    		'actions', 'Actions', 15
    	);
    	$action->addAction(
    	    new TDProject_Core_Block_Widget_Grid_Column_Actions_Delete(
    	        $this->getContext(),
    	        'releaseId',
    	        '?path=/release&method=delete'
    	    )
    	);
    	$grid->addColumn($action);
    	// return the initialized instance
    	return $grid;
    }

    /**
     * Initializes and returns the grid for the package maintainers.
     *
     * @return TDProject_Core_Block_Widget_Grid
     * 		The initialized grid
     */
    protected function _prepareUserGrid()
    {
    	// instanciate the grid
    	$grid = new TDProject_Core_Block_Widget_Grid(
    	    $this,
    	    'userGrid',
    	    'Maintainers'
    	);
    	// set the collection with the data to render
    	$grid->setCollection($this->_sortUsers()->getUsers());
    	// add the columns
    	$grid->addColumn(
    	    new TDProject_Core_Block_Widget_Grid_Column_Checkbox(
    	    	'userIdFk', '', 5
    	    )
    	)
		->setCheckedUrl(
			'?path=/channelPackage/ajax&method=relateMaintainer&channelPackageId=' .
		    $this->getChannelPackageId()
		)
		->setUncheckedUrl(
			'?path=/channelPackage/ajax&method=unrelateMaintainer&channelPackageId=' .
		    $this->getChannelPackageId()
		)
		->setProperty('userId')
		->setSourceCollection($this->getUserIdFk())
		->setTargetProperty('userId');
    	$grid->addColumn(
    	    new TDProject_Core_Block_Widget_Grid_Column(
    	    	'userId',
    	    	'ID',
    	        5
    	    )
    	);
    	$grid->addColumn(
    	    new TDProject_Core_Block_Widget_Grid_Column(
    	    	'username',
    	    	'Username',
    	        40
    	    )
    	);
    	$grid->addColumn(
    	    new TDProject_Core_Block_Widget_Grid_Column(
    	    	'hash',
    	    	'API-Hash',
    	        25
    	    )
    	);
    	$grid->addColumn(
	    	new TDProject_Core_Block_Widget_Grid_Column_Select(
	    		'role',
	    		'Role',
	    	   	15
	    	)
    	)
    	->setOptions($this->getMaintainerRoles())
    	->setOptionProperty('role')
    	->setSourceCollectionKeyType('TechDivision_Lang_String')
    	->setSourceCollection($this->getSelectedMaintainerRoles())
    	->setTargetProperty('userId');
    	$grid->addColumn(
	    	new TDProject_Core_Block_Widget_Grid_Column_Select(
	    		'active',
	    		'Active',
	    	   	10
	    	)
    	)
    	->setOptions($this->getMaintainerStates())
    	->setOptionProperty('active')
    	->setSourceCollectionKeyType('TechDivision_Lang_Boolean')
    	->setSourceCollection($this->getSelectedMaintainerStates())
    	->setTargetProperty('userId');
    	// return the initialized instance
    	return $grid;
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Block_Abstract_Package::reset()
     */
    public function reset()
    {
        // call the parent method
        parent::reset();
        // reset categories, channels and releases
        $this->_categories = new TechDivision_Collections_ArrayList();
        $this->_channels = new TechDivision_Collections_ArrayList();
        $this->_users = new TechDivision_Collections_ArrayList();
        $this->_userIdFk = new TechDivision_Collections_ArrayList();
        $this->_maintainerRoles = new TechDivision_Collections_ArrayList();
        $this->_maintainerStates = new TechDivision_Collections_ArrayList();
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Block_Abstract_Package::populate()
     */
    public function populate(
        TDProject_Channel_Common_ValueObjects_ChannelPackageValue $lvo)
    {
        // invoke the parent method
        parent::populate($lvo);
        // set the values from the passed DTO
        $this->setCategories($lvo->getCategories());
        $this->setChannels($lvo->getChannels());
        $this->setUsers($lvo->getUsers());
        $this->setUserIdFk($lvo->getUserIdFk()->toArray());
        $this->setMaintainerRoles($lvo->getMaintainerRoles());
        $this->setMaintainerStates($lvo->getMaintainerStates());
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Core_Block_Abstract::repopulate()
     */
    public function repopulate()
    {
        // initialize the DTO with the data to save
		$lvo = new TDProject_Channel_Common_ValueObjects_ChannelPackageLightValue();
		$lvo->setChannelPackageId($this->getChannelPackageId());
		$lvo->setChannelIdFk($this->getChannelIdFk());
		$lvo->setCategoryIdFk($this->getCategoryIdFk());
		$lvo->setChannelPackageId($this->getChannelPackageId());
		$lvo->setName($this->getName());
		$lvo->setLicence($this->getLicence());
		$lvo->setLicenceUri($this->getLicenceUri());
		$lvo->setShortDescription($this->getShortDescription());
		$lvo->setDescription($this->getDescription());
		$lvo->setDeprecated($this->getDeprecated());
		$lvo->setDeprecatedChannel($this->getDeprecatedChannel());
		$lvo->setDeprecatedPackage($this->getDeprecatedPackage());
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
                    $this->translate('package-name.none')
                )
            );
        }
        // check if a licence was entered
        if ($this->_licence->length() == 0) {
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::LICENCE,
                    $this->translate('package-licence.none')
                )
            );
        }
        // check if a licence URI was entered
        if ($this->_licenceUri->length() == 0) {
            $errors->addActionError(
                new TechDivision_Controller_Action_Error(
                    TDProject_Channel_Controller_Util_ErrorKeys::LICENCE_URI,
                    $this->translate('package-licence-uri.none')
                )
            );
        }
        // return the ActionErrors
        return $errors;
    }
}