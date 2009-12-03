<?php
require_once 'PHPUnit/Framework.php';
require_once 'src/Armory.php';

/**
 * Test class for WowPhpTools_Armory.
 * Generated by PHPUnit on 2009-12-01 at 23:41:39.
 */
class WowPhpTools_ArmoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var WowPhpTools_Armory
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new WowPhpTools_Armory;
    }

    /**
     * @dataProvider providerGetCharacterUrl
     */
    public function testGetCharacterUrl($realm, $name, $expected) {
		$this->assertEquals(
			$this->object->getCharacterUrl($realm, $name),
			$expected
		);
    }

	public function providerGetCharacterUrl() {
		return array(
			array('testRealm', 'christiaan', 'http://eu.wowarmory.com/character-sheet.xml?r=TestRealm&n=Christiaan'),
			array('outland', 'crushzilla', 'http://eu.wowarmory.com/character-sheet.xml?r=Outland&n=Crushzilla'),
			array('outland', 'lacoön', 'http://eu.wowarmory.com/character-sheet.xml?r=Outland&n=Laco%C3%B6n'),
		);
	}

	public function testGetCharacterUrlCustomUrl() {
		$object = new WowPhpTools_Armory(array('character' => 'http://www.wowarmory.com/character-sheet.xml?r=%s&n=%s'));
		$this->assertEquals(
			$object->getCharacterUrl('outland', 'crushzilla'),
			'http://www.wowarmory.com/character-sheet.xml?r=Outland&n=Crushzilla'
		);

		$object = new WowPhpTools_Armory(array('character' => 'http://christiaan.baartse.nl/?name=%2$s&realm=%1$s'));
		$this->assertEquals(
			$object->getCharacterUrl('outland', 'lacoön'),
			'http://christiaan.baartse.nl/?name=Laco%C3%B6n&realm=Outland'
		);
	}

	/**
	 * @expectedException WowPhpTools_Exception
	 */
	public function testGetCharacterUrlException() {
		$object = new WowPhpTools_Armory(array('guildroster' => 'http://eu.wowarmory.com/guild-info.xml?r=%s&n=%s'));
		$object->getCharacterUrl('testRealm', 'christiaan');
	}

    /**
     * @dataProvider providerGetGuildRosterUrl
     */
    public function testGetGuildRosterUrl($realm, $guild, $expected) {
		$this->assertEquals(
			$this->object->getGuildRosterUrl($realm, $guild),
			$expected
		);
    }

	public function providerGetGuildRosterUrl() {
		return array(
			array('outland', 'mysth', 'http://eu.wowarmory.com/guild-info.xml?r=Outland&n=Mysth'),
			array('ravencrest', 'flying hellfish', 'http://eu.wowarmory.com/guild-info.xml?r=Ravencrest&n=Flying+Hellfish'),
			array('testRealm', 'Christiaan Baartse', 'http://eu.wowarmory.com/guild-info.xml?r=TestRealm&n=Christiaan+Baartse'),
		);
	}

	public function testGetGuildRosterUrlCustomUrl() {
		$object = new WowPhpTools_Armory(array('guildroster' => 'http://www.wowarmory.com/guild-info.xml?r=%s&n=%s'));
		$this->assertEquals(
			$object->getGuildRosterUrl('ravencrest', 'flying hellfish'),
			'http://www.wowarmory.com/guild-info.xml?r=Ravencrest&n=Flying+Hellfish'
		);

		$object = new WowPhpTools_Armory(array('guildroster' => 'http://christiaan.baartse.nl/?guild=%2$s&realm=%1$s'));
		$this->assertEquals(
			$object->getGuildRosterUrl('ravencrest', 'flying hellfish'),
			'http://christiaan.baartse.nl/?guild=Flying+Hellfish&realm=Ravencrest'
		);
	}

	/**
	 * @expectedException WowPhpTools_Exception
	 */
	public function testGetGuildRosterUrlException() {
		$object = new WowPhpTools_Armory(array('character' => 'http://eu.wowarmory.com/guild-info.xml?r=%s&n=%s'));
		$object->getGuildRosterUrl('outland', 'mysth');
	}

    public function testGetCharacter()
    {
		if(!function_exists('curl_init')) {
			$this->markTestSkipped('curl extension not available');
		}
		$xml = $this->object->getCharacter('outland', 'crushzilla');
		$this->assertTrue($xml instanceof SimpleXMLElement);
		$this->assertEquals((string) $xml->tabInfo['tab'], 'character');
    }

	/**
	 * @expectedException WowPhpTools_Exception
	 */
	public function testGetCharacterExceptionFetchingfailed() {
		$object = new WowPhpTools_Armory(array('character' => 'http://some.nonexistand.domain.name/?name=%2$s&realm=%1$s'));
		$object->getCharacter('outland', 'crushzilla');
	}

	/**
	 * @expectedException WowPhpTools_Exception
	 */
	public function testGetCharacterExceptionMalformed() {
		$object = new WowPhpTools_Armory(array('character' => 'http://christiaan.baartse.nl/?name=%2$s&realm=%1$s'));
		$object->getCharacter('outland', 'crushzilla');
	}

	/**
	 * @expectedException WowPhpTools_Exception
	 */
	public function testGetCharacterExceptionInvalid() {
		// How are we going to test this...
		$this->markTestIncomplete();
	}

    public function testGetGuildRoster()
    {
		if(!function_exists('curl_init')) {
			$this->markTestSkipped('curl extension not available');
		}
		$xml = $this->object->getGuildRoster('outland', 'mysth');
		$this->assertTrue($xml instanceof SimpleXMLElement);
		$this->assertEquals((string) $xml->tabInfo['tab'], 'guild');
    }

	/**
	 * @expectedException WowPhpTools_Exception
	 */
	public function testGetGuildRosterExceptionFetchingfailed() {
		$object = new WowPhpTools_Armory(array('guildroster' => 'http://some.nonexistand.domain.name/?guild=%2$s&realm=%1$s'));
		$object->getGuildRoster('outland', 'mysth');
	}

	/**
	 * @expectedException WowPhpTools_Exception
	 */
	public function testGetGuildRosterExceptionMalformed() {
		$object = new WowPhpTools_Armory(array('guildroster' => 'http://christiaan.baartse.nl/?guild=%2$s&realm=%1$s'));
		$object->getGuildRoster('outland', 'mysth');
	}

	/**
	 * @expectedException WowPhpTools_Exception
	 */
	public function testGetGuildRosterExceptionInvalid() {
		// How are we going to test this...
		$this->markTestIncomplete();
	}
}
