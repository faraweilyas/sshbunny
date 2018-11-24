<?php

namespace SSHBunny;

use Exception;

trait SSHValidatorTrait
{
    protected $method;
    protected $sshAuthType;
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $publicKey;
    protected $privateKey;

    public $messageType;
    public $message;

    public $errorMessages 				= [
		'SSH_METHOD_NOT_VALID' 			=> "SSH Connection method is not valid, please set to any of the following 'local' or 'remote'",
		'SSH_AUTH_TYPE_NOT_SET' 		=> "SSH Authentication type is not set, please set to any of the following 'KEY', 'PASSWORD' or 'KEY_PASSWORD'",
		'SSH_AUTH_TYPE_NOT_VALID' 		=> "SSH Authentication type is not valid, please set to any of the following 'KEY', 'PASSWORD' or 'KEY_PASSWORD'",
		'AUTH_KEY_NOT_VALID' 			=> "Authentication key name is not valid, please set to any of the following 'public' or 'private'",
		'REQUIRED_AUTH_KEY' 			=> "HOST, PORT, USERNAME, PUBLIC_KEY and PRIVATE_KEY parameters must be specified!",
		'REQUIRED_AUTH_PASSWORD' 		=> "HOST, PORT, USERNAME and PASSWORD parameters must be specified!",
		'REQUIRED_AUTH_KEY_PASSWORD' 	=> "HOST, PORT, USERNAME, PASSWORD, PUBLIC_KEY and PRIVATE_KEY parameters must be specified!",
		'SSH_LIB_NOT_INSTALLED' 		=> "You don't have PHP libssh2 installed, please install <a target='_blank' href='http://php.net/manual/en/ssh2.installation.php'>PHP libssh2</a>",
		'SSH_CONNECTION_FAILED' 		=> "The SSH2 connection could not be established.",
		'COMMANDS_NOT_SPECIFIED' 		=> "There is nothing to exececute, no command(s) specified."
	];

    public function validateMethod ()
    {
        if (!in_array(strtolower($this->method), ["local", "remote"]))
            throw new Exception($this->errorMessages['SSH_METHOD_NOT_VALID']);
        return $this;
    }

    public function validateAuthType ()
    {
        if (empty($this->sshAuthType))
	    	$this->setError($this->errorMessages['SSH_AUTH_TYPE_NOT_SET']);
        if (!empty($this->sshAuthType) AND !in_array(strtoupper($this->sshAuthType), ["KEY", "PASSWORD", "KEY_PASSWORD"]))
            throw new Exception($this->errorMessages['SSH_AUTH_TYPE_NOT_VALID']);
        return $this;
    }

    public function validateProperty ($property)
    {
        if (!property_exists($this, $property))
            throw new Exception("Property: $property does not exist");
        return $this;
    }

    public function validateKeyName (string $keyName)
    {
        if (!in_array(strtolower($keyName), ["public", "private"]))
            throw new Exception($this->errorMessages['AUTH_KEY_NOT_VALID']);
        return $this;
    }

    public function checkRequiredAuthType ()
    {
		$this->validateAuthType();
		$host 		= $this->getProperty('host');
		$port 		= $this->getProperty('port');
		$username 	= $this->getProperty('username');
		$password 	= $this->getProperty('password');
		$keys 		= $this->getKeys();

        switch ($this->getAuthType())
        {
            case 'KEY':
                if (empty($host) || empty($port) || empty($username) || empty($keys->publicKey) || empty($keys->privateKey))
                    throw new Exception($this->errorMessages['REQUIRED_AUTH_KEY']);
                break;
            case 'PASSWORD':
                if (empty($host) || empty($port) || empty($username) || empty($password))
                    throw new Exception($this->errorMessages['REQUIRED_AUTH_PASSWORD']);
                break;
            case 'KEY_PASSWORD':
                if (empty($host) || empty($port) || empty($username) || empty($password) || empty($keys->publicKey) || empty($keys->privateKey))
                    throw new Exception($this->errorMessages['REQUIRED_AUTH_KEY_PASSWORD']);
                break;
            default:
                throw new Exception($this->errorMessages['SSH_AUTH_TYPE_NOT_VALID']);
                break;
        }
        return $this;
    }
    
    public function checkSSHLIB ()
    {
        if (!function_exists('ssh2_connect') || !function_exists('ssh2_auth_password') )
            throw new Exception($this->errorMessages['SSH_LIB_NOT_INSTALLED']);
        if (!function_exists('ssh2_auth_pubkey_file') || !function_exists('ssh2_exec'))
            throw new Exception($this->errorMessages['SSH_LIB_NOT_INSTALLED']);
        return $this;
    }
}
