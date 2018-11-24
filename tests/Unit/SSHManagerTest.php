<?php

namespace Tests\Unit;

use Exception;
use SSHBunny\SSHManager;
use PHPUnit\Framework\TestCase;

class SSHManagerTest extends TestCase
{
    protected $sshManager;

    public function setUp ()
    {
    	$this->sshManager = $this->getMockForAbstractClass(SSHManager::class);
    }

	public function test_can_set_connection_method ()
	{
		$this->sshManager->setMethod('local');

        $this->assertEquals("local", $this->sshManager->getMethod());
	}

	public function test_cant_set_connection_method ()
	{
        $this->expectException(Exception::class);

		$this->sshManager->setMethod('test');
	}

	public function test_can_get_connection_method ()
	{
		$this->sshManager->setMethod('remote');

        $this->assertEquals("remote", $this->sshManager->getMethod());
	}

	public function test_can_set_authentication_type ()
	{
		$this->sshManager->setAuthType('KEY');

        $this->assertEquals("KEY", $this->sshManager->getAuthType());
	}

	public function test_cant_set_authentication_type ()
	{
        $this->expectException(Exception::class);

		$this->sshManager->setAuthType('keys');
	}

	public function test_can_get_authentication_type ()
	{
		$this->sshManager->setAuthType('key_password');

        $this->assertEquals("KEY_PASSWORD", $this->sshManager->getAuthType());
	}

	public function test_can_set_property ()
	{
		$this->sshManager->setProperty('host', '192.12.123.3');
		$this->sshManager->setProperty('port', 22);
		$this->sshManager->setProperty('username', 'sshbunny');
		$this->sshManager->setProperty('password', 'sshbunny');

        $this->assertEquals("192.12.123.3", $this->sshManager->getProperty('host'));
        $this->assertEquals(22, $this->sshManager->getProperty('port'));
        $this->assertInternalType('int', $this->sshManager->getProperty('port'));
        $this->assertEquals("sshbunny", $this->sshManager->getProperty('username'));
        $this->assertEquals("sshbunny", $this->sshManager->getProperty('password'));
	}

	public function test_cant_set_property ()
	{
        $this->expectException(Exception::class);

		$this->sshManager->setProperty('hosts', '192.12.123.3');
	}

	public function test_can_get_property ()
	{
		$this->sshManager->setProperty('port', 22);

        $this->assertEquals(22, $this->sshManager->getProperty('port'));
	}

	public function test_can_set_and_get_key ()
	{
		$sshManager = $this->sshManager->setKey('public', 'test_pub.pub');

		$this->assertInstanceOf(SSHManager::class, $sshManager);
		$this->assertEquals("test_pub.pub", $sshManager->getKey('public'));
	}

	public function test_cant_set_key ()
	{
		$this->expectException(Exception::class);

		$this->sshManager->setKey('publics', 'test_pub.pub');
	}

	public function test_cant_get_key ()
	{
		$this->expectException(Exception::class);

		$this->sshManager->getKey('publics');
	}

	public function test_can_set_and_get_keys ()
	{
		$sshManager = $this->sshManager->setKeys('test_key.pub', 'test_key');

		$this->assertInstanceOf(SSHManager::class, $sshManager);
		$this->assertInternalType('object', $sshManager->getKeys());
		$this->assertEquals("test_key.pub", $sshManager->getKeys()->publicKey);
		$this->assertEquals("test_key", $sshManager->getKeys()->privateKey);
	}

	public function test_cant_get_keys ()
	{
		$this->assertInternalType('object', $this->sshManager->getKeys());
		$this->assertNull($this->sshManager->getKeys()->publicKey);
		$this->assertNull($this->sshManager->getKeys()->privateKey);
	}

	public function test_can_set_and_get_message ()
	{
		$sshManager = $this->sshManager->setMessage('error', 'Some error message');

		$this->assertInstanceOf(SSHManager::class, $sshManager);
		$this->assertInternalType('object', $sshManager->getMessage());
		$this->assertEquals("error", $sshManager->getMessage()->messageType);
		$this->assertEquals("Some error message", $sshManager->getMessage()->message);
	}

	public function test_cant_set_message ()
	{
		$sshManager = $this->sshManager->setMessage('errorsd', 'Success message');

		$this->assertEquals("error", $sshManager->getMessage()->messageType);
		$this->assertEquals("Success message", $sshManager->getMessage()->message);
	}

	public function test_cant_get_message ()
	{
		$this->assertNull($this->sshManager->getMessage()->messageType);
		$this->assertNull($this->sshManager->getMessage()->message);
	}
}
