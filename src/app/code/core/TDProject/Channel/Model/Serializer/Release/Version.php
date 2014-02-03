<?php

/**
 * TDProject_Channel_Model_Serializer_Release_Version
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
class TDProject_Channel_Model_Serializer_Release_Version
    extends TDProject_Channel_Model_Serializer_Release_Abstract {

    /**
     * The PCRE regex the serializers match.
     * @var string
     */
    const PCRE_REGEX = '/\/r\/(?P<packageName>\w+)\/(?P<version>[0-9]{1,}\.[0-9]{1,}\.[0-9]{1,})(?P<stability>snapshot|devel|alpha|beta|stable)?(?P<build>[0-9]{1,})?\.xml/';

    /**
     * Sets the namespace for the serializer.
     *
     * @return void
     */
    public function __construct()
    {
        // set the namespace
        $this->setNamespace(parent::REST_RELEASE);
        // set the PCRE regex to match the serializer
        $this->setRegex(self::PCRE_REGEX);
    }

    /**
	 * Returns the link title for the actual product with
	 * the requested version.
	 *
	 * @return TechDivision_Lang_Sring The requested release title
	 */
    public function getReleaseTitle()
    {
        // load and return the release title
        return $this->getRelease()->getTitle();
    }

    /**
	 * Adds the download link.
	 *
	 * @param TDProject_Channel_Model_Assembler_Channel_Api $assembler
	 * 		The assembler instance
	 * @param DOMDocument $doc The DOM document to add the download link to
	 * @param DOMNode $r The DOM node to add the download link to
	 * @return void
	 */
    public function addDownloadLink(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler, 
    	DOMDocument $doc, 
    	DOMNode $r)
    {
        // load the release
        $release = $this->getRelease();
        $packageName = $release->getChannelPackage()->getName();
        $version = $release->getVersion();
     	// load the channel name and the path to the media directory
        $channelName = $assembler->getChannel()->getName();
        $mediaDirectory = $assembler->getMediaDirectory();
        // create the download URL
        $downloadUrl = "http://$channelName/download/filename/$packageName-$version";
        // create an element for the release's download location
        $g = $doc->createElement('g');
        // get the download URL depending on the customer's group
        $g->nodeValue = $downloadUrl;
        // append the element with the release's download location to the root element
        $r->appendChild($g);
    }

    /**
     * (non-PHPdoc)
     * @see TDProject_Channel_Model_Serializer_Interfaces_Serializer::serialize()
     */
    public function serialize(
    	TDProject_Channel_Model_Assembler_Channel_Api $assembler)
    {
        // set the channel
    	$this->setChannel($assembler->getChannel());
		// load the channel package instance
    	$this->getChannelPackageByName($assembler);
    	// load the release instance
    	$this->getReleaseByChannelPackageNameAndVersion($assembler);
        // initialize a new DOM document
        $doc = new DOMDocument('1.0', 'UTF-8');
        // create new namespaced root element
        $r = $doc->createElementNS($this->getNamespace(), 'r');
        // add the schema to the root element
        $r->setAttributeNS(
        	'http://www.w3.org/2001/XMLSchema-instance',
        	'xsi:schemaLocation',
        	'http://pear.php.net/dtd/rest.release http://pear.php.net/dtd/rest.release.xsd'
        );
        // create an element for the channel package's name
        $p = $doc->createElement('p');
        $p->setAttributeNS(
        	'http://www.w3.org/1999/xlink',
        	'xlink:href',
        	'/channel/index/p/' . $this->getChannelPackage()->getName()
        );
        $p->nodeValue = $this->getChannelPackage()->getName();
        // append the element with the channel packages's name to the root element
        $r->appendChild($p);
        // create an element for the channel's name
        $c = $doc->createElement('c');
        $c->nodeValue = $this->getChannel()->getName();
        // append the element with the channel's name to the root element
        $r->appendChild($c);
        // load the release
        $release = $this->getRelease();
        // create an element for the version number
        $v = $doc->createElement('v');
        $v->nodeValue = $release->getVersion();
        // append the element with the version number to the root element
        $r->appendChild($v);
        // create an element for the stability
        $st = $doc->createElement('st');
        $st->nodeValue = $release->getStability();
        // append the element with the state to the root element
        $r->appendChild($st);
        // create an element for the licence
        $l = $doc->createElement('l');
        $l->nodeValue = $release->getLicence();
        // append the element with the licence to the root element
        $r->appendChild($l);
        // iterate over the package maintainers
        foreach ($this->getChannelPackage()->getMaintainers() as $maintainer) {
            // create an element for the maintainer
            $m = $doc->createElement('m');
            $m->nodeValue = $assembler->getUserByUserId($maintainer->getUserIdFk())->getUsername();
            // append the element with the maintainer to the root element
            $r->appendChild($m);
            break;
        }
        // create an element for the release's summary
        $s = $doc->createElement('s');
        $s->nodeValue = $release->getShortDescription();
        // append the element with the release's summary to the root element
        $r->appendChild($s);
        // create an element for the release's description
        $d = $doc->createElement('d');
        $d->nodeValue = $release->getDescription();
        // append the element with the release's description to the root element
        $r->appendChild($d);
        // create an element for the release's release date
        $da = $doc->createElement('da');
        $da->nodeValue = $release->getReleaseDate();
        // append the element with the release's release date to the root element
        $r->appendChild($da);
        // create an element for the release's release notes
        $n = $doc->createElement('n');
        $n->nodeValue = $release->getNotes();
        // append the element with the release's release release notes to the root element
        $r->appendChild($n);
        // create an element for the release's package size
        $f = $doc->createElement('f');
        $f->nodeValue = $release->getPackageSize();
        // append the element with the release's package size to the root element
        $r->appendChild($f);
        // create an element for the link to the package's release directory
        $x = $doc->createElement('x');
        $x->setAttributeNS(
        	'http://www.w3.org/1999/xlink',
        	'xlink:href',
        	'package.'.$this->getRelease()->getVersion().'.xml'
        );
        // append the XLink
        $r->appendChild($x);
        // add the download link
        $this->addDownloadLink($assembler, $doc, $r);
        // append the root element to the DOM tree
        $doc->appendChild($r);
		// return the DTO with the serialized resource content
        return new TDProject_Channel_Common_ValueObjects_ResourceViewData(
        	new TechDivision_Lang_String($doc->saveXML())
        );
    }
}