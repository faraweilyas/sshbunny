<?php

namespace Tests\Unit;

use Exception;
use SSHBunny\SSHBunny;
use PHPUnit\Framework\TestCase;

class SSHBunnyTest extends TestCase
{
	public function test_can_create_new_sshbunny_object ()
	{
		$this->assertInstanceOf(SSHBunny::class, new SSHBunny());
	}
}
