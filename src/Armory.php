<?php
/*
 * Copyright 2009 Christiaan Baartse <christiaan@baartse.nl>
 *
 * This file is part of WowPhpTools <http://github.com/christiaan/WowPhpTools>
 *
 * WowPhpTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'Exception.php';

/**
 * Easy Armory querying
 *
 * @author Christiaan Baartse <christiaan@baartse.nl>
 */
class WowPhpTools_Armory {
	
	public static $browser = "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.2) Gecko/20070319 Firefox/2.0.0.3";

	public static $urls = array(
		'guildroster'	=> 'http://eu.wowarmory.com/guild-info.xml?r=%s&n=%s', // Realm Guild Page
		'character'		=> 'http://eu.wowarmory.com/character-sheet.xml?r=%s&n=%s', // Realm Name
		// 'guildroster'	=> 'http://www.wowarmory.com/guild-info.xml?r=%s&n=%s', // Realm Guild Page
		// 'character'		=> 'http://www.wowarmory.com/character-sheet.xml?r=%s&n=%s', // Realm Name
	);

	public static $timeout = 15;

	/**
	 * Classes the way the armory handles them alphabetically sorted
	 * @var array
	 */
	public static $classes = array(
		6 => array(
			'name' => 'Death Knight',
			'color' => 'C41F3B',
		),
		11 => array(
			'name' => 'Druid',
			'color' => 'FF7D0A',
		),
		3 => array(
			'name' => 'Hunter',
			'color' => 'ABD473',
		),
		8 => array(
			'name' => 'Mage',
			'color' => '69CCF0',
		),
		2 => array(
			'name' => 'Paladin',
			'color' => 'F58CBA',
		),
		5 => array(
			'name' => 'Priest',
			'color' => 'FFFFFF',
		),
		4 => array(
			'name' => 'Rogue',
			'color' => 'FFF569',
		),
		7 => array(
			'name' => 'Shaman',
			'color' => '2459FF',
		),
		9 => array(
			'name' => 'Warlock',
			'color' => '9482C9',
		),
		1 => array(
			'name' => 'Warrior',
			'color' => 'C79C6E',
		),
	);

	public static $races = array(
		1 => 'Human',
		3 => 'Dwarf',
		4 => 'Night Elf',
		7 => 'Gnome',
		11 => 'Dreanei',
		2 => 'Orc',
		5 => 'Undead',
		6 => 'Tauren',
		8 => 'Troll',
		10 => 'Blood Elf',
	);

	protected $_browser;
	protected $_urls;
	protected $_timeout;

	/**
	 * All params are optional, if null the static defaults are used
	 * 
	 * @param array $urls optional
	 * @param string $browser optional
	 * @param int $timeout optional
	 */
	public function  __construct(array $urls = null, $browser = null, $timeout = null) {
		$this->_urls = null !== $urls ? $urls : self::$urls;
		$this->_browser = null !== $browser ? $browser : self::$browser;
		$this->_timeout = null !== $timeout ? $timeout : self::$timeout;
	}

	/**
	 * Constructs the url to the character profile page on the armory
	 *
	 * @param string $realm
	 * @param string $name
	 * @return string
	 */
	public function getCharacterUrl($realm, $name) {
 		if(!isset($this->_urls['character'])) {
			throw new WowPhpTools_Exception("No character url available");
 		}
	 	return sprintf($this->_urls['character'], urlencode(ucwords($realm)), urlencode(ucwords($name)));
 	}

	/**
	 * Constructs the url to a guildpage on the armory
	 * 
	 * @throws WowPhpTools_Exception
	 * @param string $realm
	 * @param string $guild
	 * @return string
	 */
	public function getGuildRosterUrl($realm,  $guild) {
		if(!isset($this->_urls['guildroster'])) {
			throw new WowPhpTools_Exception("No guildroster url available");
		}
 		return sprintf($this->_urls['guildroster'], urlencode(ucwords($realm)), urlencode(ucwords($guild)));
	}

	/**
	 * Get character data
	 * 
	 * @throws WowPhpTools_Exception
	 * @param string $realm
	 * @param string $name
	 * @return SimpleXMLElement 
	 */
	public function getCharacter($realm, $name) {
 		return $this->_requestXml($this->getCharacterUrl($realm, $name));
 	}

	/**
	 * Get guildroster xml
	 * 
	 * @throws WowPhpTools_Exception
	 * @param string $realm
	 * @param string $guild
	 * @return SimpleXMLElement
	 */
 	public function getGuildRoster($realm, $guild) {
 		return $this->_requestXml($this->getGuildRosterUrl($realm, $guild));
 	}

	/**
	 * Executes the curl call to fetch the xml data and validates it
	 *
	 * @throws WowPhpTools_Exception
	 * @param string $url
	 * @return SimpleXMLElement
	 */
	protected function _requestXml($url) {
 		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => $this->_timeout,
			CURLOPT_USERAGENT =>  $this->_browser
		));

 		$xml = curl_exec($ch);
 		curl_close($ch);

		if(false === $xml) {
			throw new WowPhpTools_Exception("Fetching Xml failed");
		}

		if(false === ($xml = @simplexml_load_string($xml))) {
			throw new WowPhpTools_Exception("Fetched Xml is malformed");
		}
		if(!isset($xml->tabInfo)) {
			throw new WowPhpTools_Exception("Fetched Xml invalid");
		}

 		return $xml;
 	}
}
