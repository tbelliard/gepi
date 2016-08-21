<?php

/****************************************************
 * @class : RSSFeed
 * @parent : RSSFeedBase
 * @abstract : no
 * @aim : create a new RSS Feed for your site
 * @author : Hugo 'Emacs' HAMON
 * @email : webmaster[at]apprendre-php[dot]com
 * @version : 1.0
 * @changelog :
 ***************************************************/

 // Loading necessary classes
 require_once(dirname(__FILE__).'/RSSFeedBase.class.php');

 // Start of class
 class RSSFeed extends RSSFeedBase
 {
	// Attributes
	private static $_protectString = false;
	private static $_weekDays = array('sunday','monday','tuesday','wednesday','thursday','friday','saturday');

	private $_feddCloud = array();
	private $_feedImage = array();
	private $_feedItems = array();
	private $_feedManagingEditor = array();
	private $_feedSkipDays = array();
	private $_feedSkipHours = array();
	private $_feedWebMaster = array();

	private $_copyright = '';
	private $_docs = '';
	private $_encoding = '';
	private $_generator = '';
	private $_language = '';
	private $_lastBuildDate = '';
	private $_rating = '';
	private $_ttl = '';

	private $_finalFeed = '';

	// Constants
	const RSS_ENCODING_UTF8='utf-8';

	// Autoloader
	/****************************************************
	 * @function : __autoload
	 * @aim : import needed classes
	 * @access : private
	 * @static : no
	 * @param : void
	 * @return : void
	 ***************************************************/
	private function __autoload()
	{
		require_once(dirname(__FILE__).'/RSSFeedException.class.php');
		require_once(dirname(__FILE__).'/RSSFeedTools.class.php');
		require_once(dirname(__FILE__).'/RSSFeedItem.class.php');
 	}

 	// Constructor

	/****************************************************
	 * @function : __construct
	 * @aim : create the instance of the class
	 * @access : public
	 * @static : no
	 * @param : string $encoding
	 * @return : void
	 ***************************************************/
	public function __construct($encoding)
 	{
 		$this->__autoload();
 		$this->_encoding = $encoding;
 	}

 	// Destructor

	/****************************************************
	* @function : __destruct
	* @aim : delete the instance from the memory
	* @access : public
	* @static : no
	* @param : void
	* @return : void
	***************************************************/
	public function __destruct() {}

	// GET methods

	/****************************************************
	* @function : _getIndentationNumber
	* @aim : retun the number of indentation before for a tag
	* @access : protected
	* @static : yes
	* @param : string $parentNode
	* @return : integer $indentationNumber
	****************************************************/
	protected static function _getIndentationNumber($parentNode)
	{
		switch($parentNode)
		{
			case 'channel' :
				$indentationNumber = 2;
			break;

			case 'item' :
				$indentationNumber = 3;
			break;

			default :
				$indentationNumber = 0;
			break;
		}

		// Return the indentation number
		return $indentationNumber;
	}

	// SET Methods

	/****************************************************
	* @function : setCloud
	* @aim : set the feed cloud
	* @access : public
	* @static : no
	* @param : string $domain
	*	@param	: int $port
	*	@param : string $path
	*	@param : string $registerProcedure
	*	@param	: string $protocol
	* @return : void
	***************************************************/
	public function setCloud($domain, $port, $path, $registerProcedure, $protocol)
	{
		if(!empty($domain)
			&& is_numeric($port)
			&& !empty($path)
			&& !empty($registerProcedure)
			&& !empty($protocol))
		{
			$this->_feedCloud['domain'] = $domain;
			$this->_feedCloud['port'] = intval($port);
			$this->_feedCloud['path'] = $path;
			$this->_feedCloud['registerProcedure'] = $registerProcedure;
			$this->_feedCloud['protocol'] = $protocol;
		}
	}

	/****************************************************
	* @function : setCopyright
	* @aim : set the feed copyright
	* @access : public
	* @static : no
	* @param : string $copyright
	* @return : void
	***************************************************/
	public function setCopyright($copyright)
	{
		$this->_copyright = $copyright;
	}

	/****************************************************
	* @function : setDocs
	* @aim : set the feed docs
	* @access : public
	* @static : no
	* @param : string $docs
	* @return : void
	***************************************************/
	public function setDocs($docs)
	{
		$this->_docs = RSSFeedTools::checkUrl($docs);
	}

	/****************************************************
	* @function : setGenerator
	* @aim : set the feed generator
	* @access : public
	* @static : no
	* @param : string $generator
	* @return : void
	***************************************************/
	public function setGenerator($generator)
	{
		$this->_generator = $generator;
	}

	/****************************************************
	* @function : setImage
	* @aim : set the feed image
	* @access : public
	* @static : no
	* @param : string $url
	* @param : string $title
	* @param : string $link
	* @param : string $description
	* @param : int $width
	* @param : int $height
	* @return : void
	***************************************************/
	public function setImage($url, $title, $link, $description='', $width=0, $height=0)
	{
		// Add the needed image information in the image array
		$this->_feedImage['url'] = $url;
		$this->_feedImage['title'] = $title;
		$this->_feedImage['link'] = RSSFeedTools::checkUrl($link);

		// Check the image description
		if(!empty($description))
			$this->_image['description'] = $description;

		// Check the image width
		if(!empty($width) && is_numeric($width))
		{
			$width = intval($width);

			if($width>0 && $width<=144)
			{
				$this->_feedImage['width'] = $width;
			}
			else
			{
				throw new RSSFeedException('The feed image width must be lower than 144px', __CLASS__, __METHOD__);
			}
		}

		// Check the image height
		if(!empty($height) && is_numeric($height))
		{
			$height = intval($height);

			if($height>0 && $height<=400)
			{
				$this->_feedImage['height'] = $height;
			}
			else
			{
				throw new RSSFeedException('The feed image height must be lower than 400px', __CLASS__, __METHOD__);
			}
		}
	}

	/****************************************************
	* @function : setLanguage
	* @aim : set the feed language
	* @access : public
	* @static : no
	* @param : string $language
	* @return : void
	***************************************************/
	public function setLanguage($language)
	{
		if(!empty($language) && (2 === mb_strlen($language)))
		{
			$this->_language = $language;
		}
	}

	/****************************************************
	* @function : setLastBuildDate
	* @aim : set the lastBuildDate element of the feed / item
	* @access : public
	* @static : no
	* @param : string $lastBuildDate
	* @return : void
	***************************************************/
	public function setLastBuildDate($lastBuildDate)
	{
		$this->_lastBuildDate = RSSFeedTools::prepareFeedDate($lastBuildDate);
	}

	/****************************************************
	* @function : setManagingEditor
	* @aim : set the managing editor
	* @access : public
	* @static : no
	* @param : string $email
	* @param : string $name
	* @return : void
	***************************************************/
	public function setManagingEditor($email, $name='')
	{
	// Set the managing editor information
		$this->_feedManagingEditor['email'] = RSSFeedTools::checkEmail($email);
		$this->_feedManagingEditor['name'] = $name;
	}

	/****************************************************
	* @function : setProtectString
	* @aim : set the protect string policy
	* @access : public
	* @static : no
	* @param : boolean $protectString
	* @return : void
	***************************************************/
	public function setProtectString($protectString)
	{
		if(is_bool($protectString))
		{
			self::$_protectString = $protectString;
		}
	}

	/****************************************************
	* @function : setRating
	* @aim : set the feed rating
	* @access : public
	* @static : no
	* @param : string $rating
	* @return : void
	***************************************************/
	public function setRating($rating)
	{
		$this->_rating = $rating;
	}

	/****************************************************
	* @function : setSkipDay
	* @aim : set a feed skip day
	* @access : public
	* @static : no
	* @param : int $day
	* @return : void
	***************************************************/
	public function setSkipDay($day)
	{
		$day = strtolower($day);

		if(in_array($day, self::$_weekDays) && !in_array($day, $this->_feedSkipDays))
		{
			$this->_feedSkipDays[] = ucfirst($day);
		}
	}

	/****************************************************
	* @function : setSkipHour
	* @aim : set a feed skip hour
	* @access : public
	* @static : no
	* @param : int $hour
	* @return : void
	***************************************************/
	public function setSkipHour($hour)
	{
		if(is_numeric($hour)
			&& is_int($hour)
			&& ($hour>=0)
			&& ($hour<=23))
		{
			$this->_feedSkipHours[] = intval($hour);
		}
	}

	/****************************************************
	* @function : setTimeToLive
	* @aim : set the feed time to live
	* @access : public
	* @static : no
	* @param : int $timeToLive
	* @return : void
	***************************************************/
	public function setTimeToLive($timeToLive)
	{
		$this->_ttl = $timeToLive;
	}

	/****************************************************
	* @function : setTitle
	* @aim : set the title element of the feed / item
	* @access : public
	* @static : no
	* @param : string $title
	* @return : void
	***************************************************/
	public function setTitle($title)
	{
		$this->_title = $title;
	}

	/****************************************************
	* @function : setWebMaster
	* @aim : set the webMaster information
	* @access : public
	* @static : no
	* @param : string $email
	* @param : string $name
	* @return : void
	***************************************************/
	public function setWebMaster($email, $name='')
	{
		// Set the webmaster information
		$this->_feedWebMaster['email'] = RSSFeedTools::checkEmail($email);
		$this->_feedWebMaster['name'] = $name;
	}

	// Other methods

	/****************************************************
	* @function : appendItem
	* @aim : set a new feed item
	* @access : public
	* @static : no
	* @param : RSSFeedItem $rssFeedItem
	* @return : void
	***************************************************/
	public function appendItem(RSSFeedItem $rssFeedItem)
	{
		if($rssFeedItem instanceof RSSFeedItem)
		{
			$this->_feedItems[] = $rssFeedItem;
		}
		else
		{
			throw new RSSFeedException('The $rssFeedItem parameter of '. __CLASS__ .'::'. __METHOD__ .' has to be a RSSFeedItem instance', __CLASS__, __METHOD__);
		}
	}

	/****************************************************
	* @function : display
	* @aim : display the generated feed
	* @access : public
	* @static : no
	* @param : void
	* @return : void
	*****************************************************/
	public function display()
	{
		if(empty($this->_finalFeed))
			$this->_generate();

		echo $this->_finalFeed;
	}

	/****************************************************
	* @function : regenerate
	* @aim : regenerate the feed
	* @access : public
	* @static : no
	* @param : void
	* @return : void
	*****************************************************/
	public function regenerate()
	{
		$this->_generate();
	}


	/****************************************************
	* @function : save
	* @aim : save the feed on the server
	* @access : public
	* @static : no
	* @param : string $fileName
	* @return : void
	*****************************************************/
	public function save($xmlFileName='')
	{
		$fp = null;
		$this->_generate();

		if(empty($xmlFileName) || ('xml' !== pathinfo($xmlFileName, PATHINFO_EXTENSION)))
			$xmlFileName = 'feed-'. time() .'.xml';

		// Try to open the file in writting mode
		if($fp = fopen($xmlFileName, 'w')) {

			// Try to write the file
			if(!fwrite($fp, $this->_finalFeed)) {

				throw new RSSFeedException('Can\'t write the xml feed code in '. $xmlFileName .' !', __CLASS__, __METHOD__);
			}

			// Close the file resource
			fclose($fp);
		}
		  else
		{
			throw new RSSFeedException('Can\'t manage to open the file '. $xmlFileName .' in writting mode !', __CLASS__, __METHOD__);
		}

		return true;
	}

	/****************************************************
	* @function : _checkNecessaryElements
	* @aim : check if the feed can be minimal before
	*							 its generation
	* @access : private
	* @static : no
	* @param : void
	* @return : true or throws RSSFeedException
	*****************************************************/
	private function _checkNecessaryElements()
	{
		if(empty($this->_title)) 		throw new RSSFeedException('The feed title is not defined', __CLASS__, __METHOD__);
		if(empty($this->_description)) 	throw new RSSFeedException('The feed description is not defined', __CLASS__, __METHOD__);
		if(empty($this->_link)) 		throw new RSSFeedException('The feed link is not defined', __CLASS__, __METHOD__);

		// Is there at least one item ?
		if(0 === sizeof($this->_feedItems))
		{
			throw new RSSFeedException('The feed hasn\'t any item', __CLASS__, __METHOD__);
		}
		else
		{
			$feedRank = 1;
			// Check if every item has its threw mandatory tag
			foreach($this->_feedItems as $item)
			{
				$itemTitle = $item->getTitle();
				$itemDescription = $item->getDescription();
				$itemLink = $item->getLink();

				if(empty($itemTitle))
					throw new RSSFeedException('The feed item #'. $feedRank .' has no title', __CLASS__, __METHOD__);

				if(empty($itemDescription))
					throw new RSSFeedException('The feed item #'. $feedRank .' has no description', __CLASS__, __METHOD__);

				if(empty($itemLink))
					throw new RSSFeedException('The feed item #'. $feedRank .' has no linked url', __CLASS__, __METHOD__);

			 	$feedRank++;
			}
		}
		return true;
	}

	/****************************************************
	* @function : _generate
	* @aim : generate the RSS feed
	* @access : private
	* @static : no
	* @param : void
	* @return : void
	***************************************************/
	private function _generate()
	{
		// Local data structures
		$parentNode = 'channel';

		// Check the necessary elements for a minimal feed
		if(true === $this->_checkNecessaryElements())
		{
			// Generate the feed
			$this->_finalFeed = '<?xml version="1.0" encoding="'. $this->_encoding .'" ?>'."\n";
			$this->_finalFeed.= '<rss version="2.0">'."\n";
			$this->_finalFeed.= "\t".'<channel>'."\n";
			$this->_finalFeed.= $this->_generateTitle($parentNode, $this->_title);
			$this->_finalFeed.= $this->_generateLink($parentNode, $this->_link);
			$this->_finalFeed.= $this->_generateDescription($parentNode, $this->_description);
			$this->_finalFeed.= $this->_generatePubDate($parentNode, $this->_pubDate);
			$this->_finalFeed.= $this->_generateCategories($parentNode, $this->_categories);
			$this->_finalFeed.= $this->_generateImage();
			$this->_finalFeed.= $this->_generateLastBuildDate();
			$this->_finalFeed.= $this->_generateTimeToLive();
			$this->_finalFeed.= $this->_generateManagingEditor();
			$this->_finalFeed.= $this->_generateWebMaster();
			$this->_finalFeed.= $this->_generateCopyright();
			$this->_finalFeed.= $this->_generateRating();
			$this->_finalFeed.= $this->_generateGenerator();
			$this->_finalFeed.= $this->_generateLanguage();
			$this->_finalFeed.= $this->_generateDocs();
			$this->_finalFeed.= $this->_generateSkipDays();
			$this->_finalFeed.= $this->_generateSkipHours();
			//$this->_finalFeed.= $this->_generateCloud();
			$this->_finalFeed.= $this->_generateItems();
			$this->_finalFeed.= "\t".'</channel>'."\n";
			$this->_finalFeed.= '</rss>';
		}
	}

	/****************************************************
	* @function : _generateAuthor
	* @aim : generate the item author element
	* @access : private
	* @static : no
	* @param : RSSFeedItem $item
	* @return : string $author
	****************************************************/
	private function _generateAuthor(RSSFeedItem $item)
	{
		// Local data structures
		$author = '';
		$arrayAuthor = $item->getAuthor();

		if(sizeof($arrayAuthor)>0)
		{
			$author.= str_repeat("\t", 3).'<author>'. $arrayAuthor['email'];

			if(!empty($arrayAuthor['name']))
				$author.= ' ('. $this->_protectString($arrayAuthor['name']) .')';

			$author.= '</author>'."\n";
		}

		// Return the generated string
		return $author;
	}

	/****************************************************
	* @function : _generateCategories
	* @aim : generate the feed / item category elements
	* @access : private
	* @static : no
	* @param : string $parentNode
	*	@param	: array $categories
	* @return : string $xmlCategories
	****************************************************/
	private function _generateCategories($parentNode, $categories)
	{
		// Local data structures
		$xmlCategories = '';
		$indentNumber = self::_getIndentationNumber($parentNode);

		if(sizeof($categories)>0)
		{
			foreach($categories as $categorie)
			{
				$xmlCategories.= str_repeat("\t", $indentNumber).'<category';
				$xmlCategories.= ' domain="'. $this->_protectString($categorie['domain']) .'"';
				$xmlCategories.= '>'. $this->_protectString($categorie['content']);
				$xmlCategories.= '</category>'."\n";
			}
		}

		// Return category elements
		return $xmlCategories;
	}

	/****************************************************
	* @function : _generateCloud
	* @aim : generate the RSS feed cloud element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $cloud
	***************************************************/
	private function _generateCloud()
	{
		// Local data structures
		$cloud = '';

		if(5 === sizeof($this->_feedCloud))
		{
			$cloud = str_repeat("\t", 2).'<cloud ';

			foreach($this->_feedCloud as $attribut => $valeur)
			{
				$cloud.= $attribut.'="'. $this->_protectString($valeur) .'" ';
			}

			$cloud.= '/>'."\n";
		}

		// Return the cloud element
		return $cloud;
	}

	/****************************************************
	* @function : _generateComments
	* @aim : generate the item comments element
	* @access : private
	* @static : no
	* @param : RSSFeedItem $item
	* @return : string $comments
	****************************************************/
	private function _generateComments(RSSFeedItem $item)
	{
		// Local data structures
		$comments = '';
		$itemComments = $item->getComments();

		if(!empty($itemComments))
		{
			$comments.= str_repeat("\t", 3).'<comments>'. $this->_protectString($itemComments) .'</comments>'."\n";
		}

		// Return the generated string
		return $comments;
	}

	/****************************************************
	* @function : _generateCopyright
	* @aim : generate the RSS feed copyright element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $copyright
	***************************************************/
	private function _generateCopyright()
	{
		// Local data structures
		$copyright = '';

		if(!empty($this->_copyright))
		{
			$copyright = str_repeat("\t", 2).'<copyright>'. $this->_protectString($this->_copyright) .'</copyright>'."\n";
		}

		// Return the generator element
		return $copyright;
	}

	/****************************************************
	* @function : _generateDescription
	* @aim : generate the feed / item description
	* @access : private
	* @static : no
	* @param : string $parentNode
	* @param : string $description
	* @return : string $xmlDescription
	****************************************************/
	private function _generateDescription($parentNode, $description)
	{
		// Local data structures
		$xmlDescription = '';
		$indentNumber = self::_getIndentationNumber($parentNode);

		$xmlDescription = str_repeat("\t", $indentNumber).'<description>'. $this->_protectString($description) .'</description>'."\n";

		// Return the generated string
		return $xmlDescription;
	}

	/****************************************************
	* @function : _generateEnclosure
	* @aim : generate the item enclosure element
	* @access : private
	* @static : no
	* @param : RSSFeedItem $item
	* @return : string $enclosure
	****************************************************/
	private function _generateEnclosure(RSSFeedItem $item)
	{
		// Local data structures
		$enclosure = '';
		$arrayEnclosure = $item->getEnclosure();

		if(sizeof($arrayEnclosure)>0)
		{
			$enclosure.= str_repeat("\t", 3).'<enclosure';

			foreach($arrayEnclosure as $attribute => $value)
			{
				$enclosure.= ' '. $attribute .'="'. $this->_protectString($value) .'"';
			}
			$enclosure.= ' />'."\n";
		}

		// Return the generated string
		return $enclosure;
	}

	/****************************************************
	* @function : _generateGenerator
	* @aim : generate the RSS feed generator element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $generator
	***************************************************/
	private function _generateGenerator()
	{
		// Local data structures
		$generator = '';

		if(!empty($this->_generator))
		{
			$generator = str_repeat("\t", 2).'<generator>'. $this->_protectString($this->_generator) .'</generator>'."\n";
		}

		// Return the generator element
		return $generator;
	}

	/****************************************************
	* @function : _generateDocs
	* @aim : generate the RSS feed docs element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $docs
	***************************************************/
	private function _generateDocs()
	{
		// Local data structures
		$docs = '';

		if(!empty($this->_docs))
		{
			$docs = str_repeat("\t", 2).'<docs>'. $this->_protectString($this->_docs) .'</docs>'."\n";
		}

		// Return the docs element
		return $docs;
	}

	/****************************************************
	* @function : _generateGuid
	* @aim : generate the item guid element
	* @access : private
	* @static : no
	* @param : RSSFeedItem $item
	* @return : string $guid
	****************************************************/
	private function _generateGuid(RSSFeedItem $item)
	{
		// Local data structures
		$guid = '';
		$arrayGuid = $item->getGuid();

		if(sizeof($arrayGuid)>0)
		{
			$guid.= str_repeat("\t", 3).'<guid isPermaLink="'. $arrayGuid['isPermaLink'] .'">'. $this->_protectString($arrayGuid['content']) .'</guid>'."\n";
		}

		// Return the generated string
		return $guid;
	}

	/****************************************************
	* @function : _generateImage
	* @aim : generate the RSS feed image element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $image
	***************************************************/
	private function _generateImage()
	{
		// Local data structures
		$image = '';

		if(sizeof($this->_feedImage)>0)
		{
			$image = str_repeat("\t", 2).'<image>'."\n";
			$image.= str_repeat("\t", 3).'<url>'. $this->_protectString($this->_feedImage['url']) .'</url>'."\n";
			$image.= str_repeat("\t", 3).'<title>'. $this->_protectString($this->_feedImage['title']) .'</title>'."\n";
			$image.= str_repeat("\t", 3).'<link>'. $this->_protectString($this->_feedImage['link']) .'</link>'."\n";

			if(!empty($this->_feedImage['width']))
				$image.= str_repeat("\t", 3).'<width>'. $this->_feedImage['width'] .'</width>'."\n";

			if(!empty($this->_feedImage['height']))
				$image.= str_repeat("\t", 3).'<height>'. $this->_feedImage['height'] .'</height>'."\n";

			if(!empty($this->_feedImage['description']))
				$image.= str_repeat("\t", 3).'<description>'. $this->_protectString($this->_feedImage['description']) .'</description>'."\n";

			$image.= str_repeat("\t", 2).'</image>'."\n";
		}

		// Return the image element
		return $image;
	}

	/****************************************************
	* @function : _generateItems
	* @aim : generate the RSS feed item elements
	* @access : private
	* @static : no
	* @param : void
	* @return : string $items
	***************************************************/
	private function _generateItems()
	{
		// Local data structures
		$items = '';
		$parentNode = 'item';

		// Generate items
		foreach($this->_feedItems as $item)
		{
			$items.= "\t\t".'<item>'."\n";
			$items.= $this->_generateTitle($parentNode, $item->getTitle());
			$items.= $this->_generateLink($parentNode, $item->getLink());
			$items.= $this->_generateDescription($parentNode, $item->getDescription());
			$items.= $this->_generatePubDate($parentNode, $item->getPubDate());
			$items.= $this->_generateCategories($parentNode, $item->getCategories());
			$items.= $this->_generateAuthor($item);
			$items.= $this->_generateComments($item);
			$items.= $this->_generateEnclosure($item);
			$items.= $this->_generateGuid($item);
			$items.= $this->_generateSource($item);
			$items.= "\t\t".'</item>'."\n";
		}

		// Return item elements
		return $items;
	}

	/****************************************************
	* @function : _generateLanguage
	* @aim : generate the RSS feed language element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $language
	***************************************************/
	private function _generateLanguage()
	{
		// Local data structures
		$language = '';

		if(!empty($this->_language))
		{
			$language = str_repeat("\t", 2).'<language>'. $this->_language .'</language>'."\n";
		}

		// Return the language element
		return $language;
	}

	/****************************************************
	* @function : _generateLastBuildDate
	* @aim : generate the RSS feed lastBuildDate element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $lastBuildDate
	***************************************************/
	private function _generateLastBuildDate()
	{
		// Local data structures
		$lastBuildDate = '';

		if(!empty($this->_lastBuildDate))
		{
			$lastBuildDate = str_repeat("\t", 2).'<lastBuildDate>'. $this->_lastBuildDate .'</lastBuildDate>'."\n";
		}

		// Return the lastBuildDate element
		return $lastBuildDate;
	}

	/****************************************************
	* @function : _generateLink
	* @aim : generate the feed / item link
	* @access : private
	* @static : no
	* @param : string $parentNode
	* @param : string $link
	* @return : string $xmlLink
	****************************************************/
	private function _generateLink($parentNode, $link)
	{
		// Local data structures
		$xmlLink = '';
		$indentNumber = self::_getIndentationNumber($parentNode);

		$xmlLink = str_repeat("\t", $indentNumber).'<link>'. $this->_protectString($link) .'</link>'."\n";

		// Return the generated string
		return $xmlLink;
	}

	/****************************************************
	* @function : _generateManagingEditor
	* @aim : generate the RSS feed managingEditor element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $managingEditor
	***************************************************/
	private function _generateManagingEditor()
	{
		// Local data structures
		$managingEditor = '';

		if(!empty($this->_managingEditor))
		{
			$managingEditor = str_repeat("\t", 2).'<managingEditor>'. $this->_managingEditor .'</managingEditor>'."\n";
		}

		// Return the managingEditor element
		return $managingEditor;
	}

	/****************************************************
	* @function : _generatePubDate
	* @aim : generate the RSS feed pubDate element
	* @access : private
	* @static : no
	* @param : string $parentNode
	* @param : string $pubDate
	* @return : string $xmlPubDate
	***************************************************/
	private function _generatePubDate($parentNode, $pubDate)
	{
		// Local data structures
		$xmlPubDate = '';
		$indentNumber = self::_getIndentationNumber($parentNode);

		if(!empty($pubDate))
		{
			$xmlPubDate = str_repeat("\t", $indentNumber).'<pubDate>'. $pubDate .'</pubDate>'."\n";
		}

		// Return the pubDate element
		return $xmlPubDate;
	}

	/****************************************************
	* @function : _generateRating
	* @aim : generate the RSS feed rating element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $rating
	***************************************************/
	private function _generateRating()
	{
		// Local data structures
		$rating = '';

		if(!empty($this->_rating))
		{
			$rating = str_repeat("\t", 2).'<rating>'. $this->_rating .'</rating>'."\n";
		}

		// Return the rating element
		return $rating;
	}

	/****************************************************
	* @function : _generateSkipDays
	* @aim : generate the RSS feed skipDay element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $skipDays
	***************************************************/
	private function _generateSkipDays()
	{
		// Local data structures
		$skipDays = '';
		$daysNumber = sizeof($this->_feedSkipDays);

		if($daysNumber > 0)
		{
			if(7 === $daysNumber)
			{
				throw new RSSFeedException("Your feed should't use the seven skip days", __CLASS__, __METHOD__);
			}
			else
			{
				$skipDays.= str_repeat("\t", 2).'<skipDays>'."\n";
				foreach($this->_feedSkipDays as $skipDay)
				{
					$skipDays.= str_repeat("\t", 3).'<day>'. $skipDay .'</day>'."\n";
				}
				$skipDays.= str_repeat("\t", 2).'</skipDays>'."\n";
			}
		}

		// Return skipDays elements
		return $skipDays;
	}

	/****************************************************
	* @function : _generateSkipHours
	* @aim : generate the RSS feed skipHour element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $skipHours
	***************************************************/
	private function _generateSkipHours()
	{
		// Local data structures
		$skipHours = '';

		if(sizeof($this->_feedSkipHours)>0)
		{
			sort($this->_feedSkipHours);
			$skipHours.= str_repeat("\t", 2).'<skipHours>'."\n";

			foreach($this->_feedSkipHours as $skipHour)
			{
				$skipHours.= str_repeat("\t", 3).'<hour>'. $skipHour .'</hour>'."\n";
			}

			$skipHours.= str_repeat("\t", 2).'</skipHours>'."\n";
		}

		// Return skipHours elements
		return $skipHours;
	}

	/****************************************************
	* @function : _generateSource
	* @aim : generate the item source element
	* @access : private
	* @static : no
	* @param : RSSFeedItem $item
	* @return : string $source
	****************************************************/
	private function _generateSource(RSSFeedItem $item)
	{
		// Local data structures
		$source = '';
		$arraySource = $item->getSource();

		if(sizeof($arraySource)>0)
		{
			$source.= str_repeat("\t", 3).'<source url="'. $arraySource['url'] .'">'. $this->_protectString($arraySource['content']) .'</source>'."\n";
		}

		// Return the generated string
		return $source;
	}

	/****************************************************
	* @function : _generateTimeToLive
	* @aim : generate the feed / item ttl
	* @access : private
	* @static : no
	* @param : void
	* @return : string $ttl
	****************************************************/
	private function _generateTimeToLive()
	{
		// Local data structures
		$ttl = str_repeat("\t", 2).'<ttl>'. $this->_protectString($this->_ttl) .'</ttl>'."\n";

		// Return the generated string
		return $ttl;
	}

	/****************************************************
	* @function : _generateLink
	* @aim : generate the feed / item title
	* @access : private
	* @static : no
	*	@param : string $parentNode
	* @param : string $title
	* @return : string $xmlTitle
	****************************************************/
	private function _generateTitle($parentNode, $title)
	{
		// Local data structures
		$xmlTitle = '';
		$indentNumber = self::_getIndentationNumber($parentNode);

		$xmlTitle = str_repeat("\t", $indentNumber).'<title>'. $this->_protectString($title) .'</title>'."\n";

		// Return the generated string
		return $xmlTitle;
	}

	/****************************************************
	* @function : _generateWebmaster
	* @aim : generate the RSS feed webMaster element
	* @access : private
	* @static : no
	* @param : void
	* @return : string $webMaster
	***************************************************/
	private function _generateWebMaster()
	{
		// Local data structures
		$webMaster = '';

		if(sizeof($this->_feedWebMaster)>0)
		{
			$webMaster = str_repeat("\t", 2).'<webMaster>'. $this->_feedWebMaster['email'];

			if(!empty($this->_feedWebMaster['name']))
			{
				$webMaster.= ' ('. $this->_protectString($this->_feedWebMaster['name']) .')';
			}

			$webMaster.= '</webMaster>'."\n";
		}

		// Return the managingEditor element
		return $webMaster;
	}

	/****************************************************
	* @function : _protectString
	* @aim : protect the content of an html element if
	*							 it is requested
	* @access : private
	* @static : no
	* @param :  void
	* @return : string $htmlContent
	***************************************************/
	private function _protectString($htmlContent)
	{
		if(true === self::$_protectString)
			$htmlContent = htmlspecialchars(strip_tags($htmlContent));

		// Return the html content
		return $htmlContent;
	}

	// END OF CLASS
 }

?>