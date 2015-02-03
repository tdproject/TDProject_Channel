<?php

/**
 * TDProject_Channel_Model_Actions_Release
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

// necessary because of a PEAR naming bug!
require_once 'PEAR/PackageFile/Parser/v2.php';

/**
 * @category   	TDProject
 * @package    	TDProject_Channel
 * @subpackage	Model
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 * 				Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class TDProject_Channel_Model_Actions_Release
    extends TDProject_Core_Model_Actions_Abstract {

    /**
     * Allowed metafile names.
     * @var array
     */
    protected $_metafiles = array('package.xml', 'package2.xml');

    /**
     * Factory method to create a new instance.
     *
     * @param TechDivision_Model_Interfaces_Container $container The container instance
     * @return TDProject_Channel_Model_Actions_Category
     * 		The requested instance
     */
    public static function create(TechDivision_Model_Interfaces_Container $container)
    {
        return new TDProject_Channel_Model_Actions_Release($container);
    }


    /**
     * Checks if the maintainer with the passed API-Hash is
     * allowed to upload the file with the passed name.
     *
     * @param TechDivision_Lang_String  $filename
     *        The name of the file to be uploaded
     * @param TechDivision_Lang_String  $hash
     *        The API-Hash of the maintainer who tries to upload the file
     * @param TechDivision_Lang_Integer $channelIdFk
     *        The Channel ID foreign key of the channel containing the package
     *
     * @return boolean TRUE if the upload is allowed, else FALSE
     * @throws TDProject_Channel_Common_Exceptions_InvalidApiHashException
     *        Is thrown if the an unknown API-Hash has been passed
     * @throws TDProject_Channel_Common_Exceptions_InvalidRoleException
     *        Is thrown if the user with the passed API-Hash has not role 'lead'
     * @throws TDProject_Channel_Common_Exceptions_UnknownPackageException
     *        Is thrown if the passed API-Hash is not related with the passed package
     */
    public function allowReleaseUpload(
        TechDivision_Lang_String $filename,
        TechDivision_Lang_String $hash,
        TechDivision_Lang_Integer $channelIdFk
    ) {
        // try to load the maintainer by the passed API-Hash
        $maintainer = TDProject_Channel_Model_Utils_MaintainerUtil::getHome($this->getContainer())
            ->findByHash($hash);
        // check if a maintainer with the passed API-Hash is available
        if ($maintainer == null) {
            throw new TDProject_Channel_Common_Exceptions_InvalidApiHashException(
                "Invalid hash '$hash' passed for authentication"
            );
        }
        // initialize the maintainer lead role Enum to compare the maintainer role with
        $lead = TDProject_Channel_Common_Enums_MaintainerRole::create(
            TDProject_Channel_Common_Enums_MaintainerRole::LEAD
        );
        // check if the maintainer is 'active'
        if ($maintainer->getActive()->equals(new TechDivision_Lang_Boolean(false))) {
            throw new TDProject_Channel_Common_Exceptions_InvalidRoleException(
                "Maintainer MUST be active"
            );
        }
        // compare the roles maintainer MUST have role 'lead'
        if (!$maintainer->getRole()->equals($lead->toString())) {
            throw new TDProject_Channel_Common_Exceptions_InvalidRoleException(
                "Maintainer MUST have role '$lead'"
            );
        }
        // explode the channel package name
        list ($packageName, $version) = explode('-', basename($filename, "tgz"));
        // try load the channel package by it's name
        $channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
            ->findByNameAndChannelIdFk(new TechDivision_Lang_String($packageName), $channelIdFk);
        // check if channel package is available
        if ($channelPackage == null) {
            throw new TDProject_Channel_Common_Exceptions_UnknownChannelPackageException(
                "Unknown package '{$channelPackage->getName()}'"
            );
        }
        // check if the API-Hash is valid for the uploaded channel package
        if (!$channelPackage->getChannelPackageId()->equals($maintainer->getChannelPackageIdFk())) {
            throw new TDProject_Channel_Common_Exceptions_InvalidApiHashException(
                "Hash '$hash' is not valid for package '{$channelPackage->getName()}'"
            );
        }
        // return TRUE if the
        return true;
    }

    /**
     * Creates a new release from the passed binary.
     *
     * @param TechDivision_Lang_Integer $channelId
     * 		The ID of the channel to upload the release to
     * @param TechDivision_Lang_String $targetFilename
     * 		The path to the binary file to create the release from
     * @return TechDivision_Lang_Integer The ID of the created release
     * @throws TDProject_Channel_Common_Exceptions_InvalidChannelException
     * 		Is thrown if the package channel doesn't match the actual channel
     * @throws TDProject_Channel_Common_Exceptions_ReleaseAlreadyExistsException
     * 		Is thrown if a release with package name and version is already available
     */
    public function createBinaryRelease(
        TechDivision_Lang_Integer $channelId,
        TechDivision_Lang_String $targetFilename)
    {
        // initialize the archive
        $tar = $this->extractBinary($targetFilename);
        // extract the XML content of the package.xml/package2.xml file
        for ($i = 0; $i < sizeof($this->_metafiles); $i++) {
            // load the next metafile name
            $packageFilename = $this->_metafiles[$i];
            // try to load the content of the binary's metafile
            if ($contents = $this->extractMetadata($tar, $packageFilename)) {
                break;
            }
        }
        // parse and validate the binary
        $pf = $this->parseAndValidate($contents, $packageFilename);
        // load the channel
        $channel = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
            ->findByPrimaryKey($channelId);
        // check if the actual channel matches the package channel
        if (!$channel->getName()->equals(new TechDivision_Lang_String($pf->getChannel()))) {
            throw new TDProject_Channel_Common_Exceptions_InvalidChannelException(
                "Package channel {$pf->getChannel()} doesn't match channel {$channel->getName()}"
            );
        }
        // move the file to the destination directory
        $destinationFilename = $this->moveUpload($targetFilename, $channel);
        // load the channel package by it's name
        $channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
            ->findByNameAndChannelIdFk(new TechDivision_Lang_String($pf->getName()), $channelId);
        // check if channel package is already available
        if ($channelPackage == null) {
            // if NOT, create a new channel package in the actual channel
            $channelPackageId = TDProject_Channel_Model_Actions_ChannelPackage::create($this->getContainer())
                ->createFromBinary($channelId, $pf);
        }
        else {
            $channelPackageId = $channelPackage->getChannelPackageId();
        }
        // validate the package maintainers
        $this->validateMaintainers($channelPackageId, $pf);
        // check if the package has already been uploaded
        $release = TDProject_Channel_Model_Utils_ReleaseUtil::getHome($this->getContainer())
            ->findByChannelPackageNameAndVersionAndChannelIdFk(
                new TechDivision_Lang_String($pf->getName()),
                new TechDivision_Lang_String($pf->getVersion()),
                $channelId
            );
        // if a release is already available
        if ($release != null) {
            // throw an exception
            throw new TDProject_Channel_Common_Exceptions_ReleaseAlreadyExistsException(
                "Release {$pf->getChannel()}/{$pf->getName()}-{$pf->getVersion()} already exists"
            );
        }
        // create a new release
        $release = TDProject_Channel_Model_Utils_ReleaseUtil::getHome($this->getContainer())
            ->epbCreate();
        // set the data from the binary
        $release->setChannelPackageIdFk($channelPackageId);
        $release->setPackageFile(new TechDivision_Lang_String($contents));
        $release->setPackageSize(new TechDivision_Lang_Integer(filesize($destinationFilename)));
        $release->setDependencies(new TechDivision_Lang_String(serialize($pf->getDependencies())));
        $release->setStability(new TechDivision_Lang_String($pf->getState()));
        $release->setVersion(new TechDivision_Lang_String($pf->getVersion()));
        $release->setLicence(new TechDivision_Lang_String($pf->getLicense()));
        $release->setShortDescription(new TechDivision_Lang_String($pf->getSummary()));
        $release->setDescription(new TechDivision_Lang_String($pf->getDescription()));
        $release->setNotes(new TechDivision_Lang_String($pf->getNotes()));
        // convert and set the release date as UNIX timestamp
        $releaseDate = new Zend_Date($pf->getDate());
        $release->setReleaseDate(new TechDivision_Lang_Integer((int) $releaseDate->getTimestamp()));
        // set the licence URI if available
        if (is_array($loc = $pf->getLicenseLocation())) {
            if (array_key_exists('uri', $loc)) {
                $release->setLicenceUri(new TechDivision_Lang_String($loc['uri']));
            }
            elseif (array_key_exists('filesource', $loc)) {
                $release->setLicenceUri(new TechDivision_Lang_String($loc['filesource']));
            }
        }
        // save the release and return the ID
        return $release->create();
    }

    /**
     * Returns the URL to download the requested filename.
     *
     * @param TechDivision_Lang_Integer $channelId
     *        ID of the release's channel
     * @param TechDivision_Lang_String  $filename
     *        The filename of the requested releaes
     *
     * @throws TDProject_Channel_Common_Exceptions_UnknownChannelPackageException
     *
     * @return TechDivision_Lang_String
     *        The download URL for the release
     */
    public function getDownloadUrl(
        TechDivision_Lang_Integer $channelId,
        TechDivision_Lang_String $filename
    ) {
        // load the channel
        $channel = TDProject_Channel_Model_Utils_ChannelUtil::getHome($this->getContainer())
            ->findByPrimaryKey($channelId);
        // load the channel name
        $channelName = $channel->getName();
        // load the channel alias
        $channelAlias = $channel->getAlias();
        // extract the package name from the passed filename
        list ($packageName, $version) = explode('-', basename($filename, 'tgz'));
        // load the channel package by it's name
        $channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
            ->findByNameAndChannelIdFk(new TechDivision_Lang_String($packageName), $channelId);
        // check if the package is part of the passed channel
        if (!$channelPackage->getChannelIdFk()->equals($channelId)) {
            // if not, throw an exception
            throw new TDProject_Channel_Common_Exceptions_UnknownChannelPackageException(
                "Package $packageName not available in channel $channelName"
            );
        }
        // load the path to the media directory
        $mediaDirectory = $this->_getMediaDirectory();

        /**
         * Legacy implementation stored all files in one folder, not regarding the channel name.
         * To allow packages with the same name to be present in more than one channel independently,
         * the channel name is used as folder in all new release files which have been uploaded
         * after the change.
         *
         * Unless explicitly migrated, all previously existing release files stay untouched,
         * so for those a fallback logic exists.
         */
        try {
            // First try to locate the release file in the correct folder of the channel.
            $filePath = new TechDivision_Lang_String(
                "$mediaDirectory/Channel/get/$channelAlias/$filename"
            );

            // Throw an exception if the file does not exist in the channel-specific folder.
            if (!file_exists($filePath->toString())) {
                throw new TDProject_Channel_Common_Exceptions_ReleaseFileNotFoundException(
                    sprintf('File "%s" does not exist.', $filePath->toString())
                );
            }
        } catch (TDProject_Channel_Common_Exceptions_ReleaseFileNotFoundException $e) {
            // Legacy fallback. try to locate the file in the flat structure without channel name.
            $filePath = new TechDivision_Lang_String(
                "$mediaDirectory/Channel/get/$filename"
            );
        }

        // Return the prepared download URL.
        return $filePath;
    }

    /**
     * Checks if the maintainers specified in the binarys meta information
     * are also related with the channel package in the channel.
     *
     * @param TechDivision_Lang_Integer $channelPackageId The channel package to check against
     * @param PEAR_PackageFile_v2 $pf The binary to check
     * @throws TDProject_Channel_Common_Exceptions_MaintainerNotRelatedException
     * 		Is thrown if the maintainer is not related with the package
     */
    public function validateMaintainers(
        TechDivision_Lang_Integer $channelPackageId,
        PEAR_PackageFile_v2 $pf)
    {
        // load the channel package with the passed ID
        $channelPackage = TDProject_Channel_Model_Utils_ChannelPackageUtil::getHome($this->getContainer())
            ->findByPrimaryKey($channelPackageId);
        // iterate over the maintainers specified in the binary
        foreach ($pf->getMaintainers() as $mnt) {
            // load the maintainers handle
            list ($name, $email, $active, $handle, $role) = array_values($mnt);
            // iterate over the packages maintainer (channel)
            foreach ($channelPackage->getMaintainers() as $maintainer) {
                // load the user by the found handle
                $user = TDProject_Core_Model_Utils_UserUtil::getHome($this->getContainer())
                    ->findByPrimaryKey($maintainer->getUserIdFk());
                // load the user's username
                $username = $user->getUsername();
                // check if the package maintainer matches the channel maintainer
                if ($username->equals(new TechDivision_Lang_String($handle)) &&
                    $maintainer->getRole()->equals(new TechDivision_Lang_String($role)) &&
                    $maintainer->getActive()->equals(new TechDivision_Lang_Boolean($active))) {
                    // if yes, continue with the next maintainer (binary)
                    continue 2;
                }
            }
            // create a maintainer identifier
            $identifier = implode(', ', $mnt);
            // throw an exception that the maintainer is NOT related with the package
            throw new TDProject_Channel_Common_Exceptions_MaintainerNotRelatedException(
                "Maintainer $identifier not matches channel configuration"
            );
        }
    }

    /**
     * Moves the binary to the target folder.
     *
     * @param TechDivision_Lang_String                 $targetFilename
     *        The binary to move
     * @param TDProject_Channel_Model_Entities_Channel $channel
     *
     * @throws TDProject_Channel_Common_Exceptions_ReleaseMoveException
     * @return string The destination file the file has been moved to
     */
    public function moveUpload(
        TechDivision_Lang_String $targetFilename,
        TDProject_Channel_Model_Entities_Channel $channel)
    {
        // load the path to the media directory
        $mediaDirectory = $this->_getMediaDirectory();
        // load the filename from the target
        $filename = basename($oldname = $targetFilename->stringValue());
        // build the base directory including the channel name
        $baseDir = sprintf('%s/Channel/get/%s/', $mediaDirectory, $channel->getAlias());

        // try to create the channel-specific directory
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777);
        }

        // prepare the destination
        $newname = "$baseDir/$filename";
        // move the uploaded file to the destination directory
        if (!rename($oldname, $newname)) {
            throw new TDProject_Channel_Common_Exceptions_ReleaseMoveException(
                "Binary '$oldname' can't be moved to destination '$newname'"
            );
        }
        // return the new filename
        return $newname;
    }

    /**
     * Loads and returns the passed binary as Archive_Tar.
     *
     * @param TechDivision_Lang_String $targetFilename
     * @return Archive_Tar The extracted binary
     */
    public function extractBinary(TechDivision_Lang_String $targetFilename)
    {
        return new Archive_Tar($targetFilename->stringValue());
    }

    /**
     * Loads the package.xml/package2.xml file from the passed Archive_Tar.
     *
     * @param Archive_Tar $tar The archive to load the metafile from
     * @param string $packageFilename Name of the metafile package.xml/package2.xml
     * @return string The metadata
     */
    public function extractMetadata(Archive_Tar $tar, $packageFilename)
    {
        return $tar->extractInString($packageFilename);
    }

    /**
     * Parses the content of the passed metafile and returns
     * an initialized package file instance.
     *
     * @param string $contents The content with the metadata to parse
     * @param unknown_type $packageFilename The name of the metafile
     * @throws Exception Is thrown if the content is not valid
     * @return PEAR_PackageFile_v2 The package file instance
     * @throws TDProject_Channel_Common_Exceptions_ChannelPackageParseException
     * 		Is thrown if the package.xml or package2.xml could not be parsed
     */
    public function parseAndValidate($contents, $packageFilename)
    {
        // initialize the parser for the package file and parse it
        $pkg = new PEAR_PackageFile_Parser_v2();
        $pkg->setConfig(new PEAR_Config());
        $pf = $pkg->parse($contents, $packageFilename);
        // check if errors occurs and throw an exception if necessary
        if (PEAR::isError($pf)) {
            throw new TDProject_Channel_Common_Exceptions_ChannelPackageParseException(
                $pf->getMessage()
            );
        }
        // return the package file
        return $pf;
    }

    /**
     * Returns the PEAR system configuration instance.
     *
     * @return PEAR_Config The PEAR system configuration
     */
    public function getSystemConfig()
    {
        return $this->getContainer()->getSystemConfig();
    }

    /**
     * Loads the media directory from the system settings.
     *
     * @return string The path to the media directory
     */
    protected function _getMediaDirectory()
    {
        // load the data directory
        $dataDir = $this->getSystemConfig()->get('data_dir');
        // initialize a new LocalHome to load the settings
        $settings = TDProject_Core_Model_Utils_SettingUtil::getHome($this->getContainer())
            ->findAll();
        // return the directory for storing media data
        foreach ($settings as $setting) {
            return  $dataDir . DIRECTORY_SEPARATOR .  $setting->getMediaDirectory();
        }
    }
}