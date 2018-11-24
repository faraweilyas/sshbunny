<?php

namespace SSHBunny;

use Exception;

abstract class SSHManager
{
    use SSHValidatorTrait;

    /**
    * Store debug state
    * @var bool $debug
    */
    public $debug = FALSE;

    /**
    * initilize ssh.
    * @return this
    */
    abstract public function initialize ();

    /**
    * Connect to server.
    * @return this
    */
    abstract public function connect ();

    /**
    * Execute commands on server.
    * @return this
    */
    abstract public function exec ();

    /**
    * Disconnect from server.
    * @return this
    */
    abstract public function disconnect ();

    public function debug ($debug=TRUE)
    {
        $this->debug = $debug;
        return $this;
    }

    public function setMethod ($method)
    {
        $this->method = $method;
        $this->validateMethod();
        return $this;
    }

    public function getMethod ()
    {
        return strtolower($this->method);
    }

    public function setAuthType ($sshAuthType)
    {
        $this->sshAuthType = $sshAuthType;
        $this->validateAuthType();
        return $this;
    }

    public function getAuthType ()
    {
        return strtoupper($this->sshAuthType);
    }

    public function setProperty ($property, $value)
    {
        // host port username password
        $this->validateProperty($property);
        $this->$property = $value;
        return $this;
    }

    public function getProperty ($property)
    {
        $this->validateProperty($property);
        return $this->$property;
    }

    public function setKey ($keyName, $key)
    {
        $this->validateKeyName($keyName);
        $keyName = ($keyName == 'public') ? "publicKey" : "privateKey";
        $this->$keyName = $key;
        return $this;
    }

    public function getKey ($keyName)
    {
        $this->validateKeyName($keyName);
        $keyName = ($keyName == 'public') ? "publicKey" : "privateKey";
        return $this->$keyName;
    }

    public function setKeys ($publicKey, $privateKey)
    {
        $this->publicKey    = $publicKey;
        $this->privateKey   = $privateKey;
        return $this;
    }

    public function getKeys ()
    {
        return (object) ['publicKey' => $this->publicKey, 'privateKey' => $this->privateKey];
    }

    public function setMessage ($messageType, $message)
    {
        $this->messageType  = (strtolower($messageType) == 'success') ? 'success' : 'error';
        $this->message      = $message;
        return $this;
    }
    
    public function setError ($message)
    {
        return $this->setMessage('error', $message);
    }
    
    public function setSuccess ($message)
    {
        return $this->setMessage('success', $message);
    }
    
    public function getMessage ()
    {
        return (object) ['messageType' => $this->messageType, 'message' => $this->message];
    }
    
    public function flagError ()
    {
        if ($this->getMessage()->messageType == 'error')
            $this->debug()->displayMessage();
        return $this;
    }
    
    public function displayMessage ()
    {
        $message        = $this->getMessage()->message;
        $messageColor   = ($this->getMessage()->messageType == 'success') ? 'purple' : 'red';
        if ($this->debug == TRUE)
            die ("<pre style='color:{$messageColor}'>{$message}</pre>");
        else
            print "<pre style='color:{$messageColor}'>{$message}</pre>";
        return $this;
    }
}
