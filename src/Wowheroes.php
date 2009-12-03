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
 * Class for fetching data from wow-heroes.com
 *
 * @author Christiaan Baartse <christiaan@baartse.nl>
 */
class WowPhpTools_Wowheroes {

	public static $browser = "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.2) Gecko/20070319 Firefox/2.0.0.3";

	public static $urls = array(
		'guildroster'	=> 'http://xml.wow-heroes.com/xml-guild.php?z=eu&r=%s&g=%s', // Realm, Guildname
		// 'guildroster'	=> 'http://xml.wow-heroes.com/xml-guild.php?z=us&r=%s&g=%s', // Realm, Guildname
	);

	public static $timeout = 15;

	/**
	 * @var array Classes the way the armory handles them, alphabetically sorted
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

	/**
	 * @var array Races as the Armory maps them to ids
	 */
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
	 * Constructs the Url that points to the xml data
	 *
	 * @param string $realm
	 * @param string $guild
	 * @return string
	 */
	public function getGuildRosterUrl($realm, $guild) {
		if(!isset($this->_urls['guildroster'])) {
			throw new WowPhpTools_Exception("No guildroster url available");
		}
		return sprintf(
			$this->_urls['guildroster'],
			urlencode(ucwords($realm)),
			urlencode(ucwords($guild))
		);
	}

 	/**
 	 * Get the guildlisting from Wowheroes
 	 *
 	 * @throws WowPhpTools_Exception
 	 * @param string $realm
 	 * @param string $guild
 	 * @return SimpleXMLElement
 	 */
 	public function getGuild($realm, $guild) {
 		return $this->_requestXml($this->getGuildRosterUrl($realm, $guild, $zone));
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
		if(!isset($xml->guild)) {
			throw new WowPhpTools_Exception("Fetched Xml invalid");
		}

 		return $xml;
 	}
}
