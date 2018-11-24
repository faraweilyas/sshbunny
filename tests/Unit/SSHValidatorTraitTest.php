<?php

namespace Tests\Unit;

use Exception;
use SSHBunny\SSHManager;
use SSHBunny\SSHValidatorTrait;
use PHPUnit\Framework\TestCase;

class SSHValidatorTraitTest extends TestCase
{
    protected $sshValidator;

    protected $sshManager;

    public function setUp ()
    {
    	$this->sshValidator = $this->getMockForTrait(SSHValidatorTrait::class);
    	$this->sshManager = $this->getMockForAbstractClass(SSHManager::class);
    }

	public function test_can_validate_connection_method ()
	{
        $this->expectException(Exception::class);

		$this->sshValidator->validateMethod();
	}

	public function test_can_validate_authentication_type_not_set ()
	{
		$this->sshManager->validateAuthType();
		$errorMessage = $this->sshManager->errorMessages['SSH_AUTH_TYPE_NOT_SET'];

        $this->assertEquals("error", $this->sshManager->getMessage()->messageType);
        $this->assertEquals($errorMessage, $this->sshManager->getMessage()->message);
	}

	public function test_can_validate_authentication_type_invalid ()
	{
        $this->expectException(Exception::class);

		$this->sshManager->setAuthType('keys');
		$this->sshManager->validateAuthType();
	}

	public function test_can_validate_property ()
	{
        $this->expectException(Exception::class);

		$this->sshValidator->validateProperty("ports");
	}
	
	public function test_can_validate_key_name ()
	{
        $this->expectException(Exception::class);

		$this->sshValidator->validateKeyName('publick');
	}
	
	public function test_can_check_required_auth_type_to_fail ()
	{
        $this->expectException(Exception::class);

		$this->sshManager->checkRequiredAuthType();
	}
	
	public function test_can_check_required_auth_type_to_pass_key ()
	{
		$this->sshManager->setAuthType('KEY');
		$this->sshManager->setProperty('host', '192.12.123.3');
		$this->sshManager->setProperty('port', 22);
		$this->sshManager->setProperty('username', 'sshbunny');
		$this->sshManager->setKeys('test_key.pub', 'test_key');
		$this->sshManager->checkRequiredAuthType();

		$this->assertInternalType('object', $this->sshManager);
		$this->assertInstanceOf(SSHManager::class, $this->sshManager);
	}
	
	public function test_can_check_required_auth_type_to_fail_key ()
	{
        $this->expectException(Exception::class);

		$this->sshManager->setAuthType('KEY');
		$this->sshManager->setProperty('host', '192.12.123.3');
		$this->sshManager->setProperty('port', 22);
		$this->sshManager->setProperty('username', 'sshbunny');
		$this->sshManager->checkRequiredAuthType();
	}
	
	public function test_can_check_required_auth_type_to_pass_password ()
	{
		$this->sshManager->setAuthType('PASSWORD');
		$this->sshManager->setProperty('host', '192.12.123.3');
		$this->sshManager->setProperty('port', 22);
		$this->sshManager->setProperty('username', 'sshbunny');
		$this->sshManager->setProperty('password', 'sshbunny');
		$this->sshManager->checkRequiredAuthType();

		$this->assertInternalType('object', $this->sshManager);
		$this->assertInstanceOf(SSHManager::class, $this->sshManager);
	}
	
	public function test_can_check_required_auth_type_to_fail_password ()
	{
        $this->expectException(Exception::class);

		$this->sshManager->setAuthType('PASSWORD');
		$this->sshManager->setProperty('host', '192.12.123.3');
		$this->sshManager->setProperty('port', 22);
		$this->sshManager->setProperty('username', 'sshbunny');
		$this->sshManager->checkRequiredAuthType();
	}
	
	public function test_can_check_required_auth_type_to_pass_key_password ()
	{
		$this->sshManager->setAuthType('KEY_PASSWORD');
		$this->sshManager->setProperty('host', '192.12.123.3');
		$this->sshManager->setProperty('port', 22);
		$this->sshManager->setProperty('username', 'sshbunny');
		$this->sshManager->setProperty('password', 'sshbunny');
		$this->sshManager->setKeys('test_key.pub', 'test_key');
		$this->sshManager->checkRequiredAuthType();

		$this->assertInternalType('object', $this->sshManager);
		$this->assertInstanceOf(SSHManager::class, $this->sshManager);
	}
	
	public function test_can_check_required_auth_type_to_fail_key_password ()
	{
        $this->expectException(Exception::class);

		$this->sshManager->setAuthType('KEY_PASSWORD');
		$this->sshManager->setProperty('host', '192.12.123.3');
		$this->sshManager->setProperty('port', 22);
		$this->sshManager->setProperty('username', 'sshbunny');
		$this->sshManager->setProperty('password', 'sshbunny');
		$this->sshManager->checkRequiredAuthType();
	}
}
