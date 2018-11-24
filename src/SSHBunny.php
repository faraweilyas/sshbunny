<?php

namespace SSHBunny;

use Exception;

class SSHBunny extends SSHManager
{
    /**
    * Store connection resource
    * @var resource $connection
    */
    protected $connection;

    /**
    * Store authentication result
    * @var bool $authentication
    */
    protected $authentication;

    /**
    * Store command execution result
    * @var string $data
    */
    protected $data = "";

    /**
    * Constructor for initialization
    * @param string $method
    * @param string $authType
    * @param string $host
    * @param int $port
    * @param string $username
    * @return void
    */
    function __construct ($method='local', $authType=NULL, $host=NULL, $port=22, $username=NULL)
    {
        $this->setMethod($method);
        $this->setAuthType($authType);
        $this->setProperty('host', $host);
        $this->setProperty('port', $port);
        $this->setProperty('username', $username);
        return $this;
    }

    public function initialize ()
    {
        if ($this->getMethod() == "local")
            return $this;
        try {
            $this->checkRequiredAuthType();

            $this->checkSSHLIB();

            $this->connect();
        } catch (Exception $e) {
            $this->setError($e->getMessage())->flagError();
        }
        return $this;
    }

    public function connect ()
    {
        $this->connection = @ssh2_connect($this->getProperty('host'), $this->getProperty('port'));         
        if (!$this->connection) throw new Exception($this->errorMessages['SSH_CONNECTION_FAILED']);
        $this->setSuccess("SSH2 Connected!");

        switch (strtoupper($this->sshAuthType))
        {
            case 'KEY':
                $this->authentication = @ssh2_auth_pubkey_file($this->connection, $this->username, $this->publicKey, $this->privateKey);
                break;
            case 'PASSWORD':
                $this->authentication = @ssh2_auth_password($this->connection, $this->username, $this->password);
                break;
            case 'KEY_PASSWORD':
                $this->authentication = @ssh2_auth_pubkey_file($this->connection, $this->username, $this->publicKey, $this->privateKey, $this->password);
                break;
            default:
                throw new Exception($this->errorMessages['SSH_AUTH_TYPE_NOT_VALID']);
                break;
        }
        
        if (!$this->authentication) throw new Exception("Could not authenticate, rejected by server using: '{$this->username}'");

        return $this;
    }

    public function exec ()
    {
        try {
            $argumentCount = func_num_args();
            if (!$argumentCount)
                throw new Exception($this->errorMessages['COMMANDS_NOT_SPECIFIED']);

            $arguments  = func_get_args();
            $command    = ($argumentCount > 1) ? implode(" && ", $arguments) : $arguments[0];
            if ($this->getMethod() == "local")
                $this->executeLocal($command);
            else
                $this->executeRemote($command);
        } catch (Exception $e) {
            $this->setError($e->getMessage())->flagError();
        }
        return $this;
    }
    
    public function executeLocal ($commandToExecute)
    {
        $this->data .= @shell_exec($commandToExecute);
        return $this;
    }
    
    public function executeRemote ($commandToExecute)
    {
        $stream = @ssh2_exec($this->connection, $commandToExecute);
        if (!$stream)
            throw new Exception("Unable to execute the specified command: <b>{$command}</b>");

        stream_set_blocking($stream, TRUE);
        while ($buffer = fread($stream, 4096))
        {
            $this->data .= $buffer;
        }
        fclose($stream);
        return $this;
    }
    
    public function getData ($display=FALSE, $html=FALSE)
    {
        if ($display == TRUE)
            print ($html) ? nl2br($this->data) : $this->data;
        else
            return ($html) ? nl2br($this->data) : $this->data;
        return $this;
    }
    
    public function clearData ()
    {
        $this->data = "";
        return $this;
    }
    
    public function disconnect ()
    {
        if ($this->getMethod() == "remote" && is_resource($this->connection) && $this->authentication):
            $this->exec('echo "EXITING"', 'exit;');
            $this->connection = NULL;
            $this->setError("SSH2 Disconnected!");
        endif;
        return $this;
    }

    public function __destruct ()
    {
        $this->disconnect();
    }
}
