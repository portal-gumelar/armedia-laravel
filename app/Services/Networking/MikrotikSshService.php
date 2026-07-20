<?php

namespace App\Services\Networking;

use Exception;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\Log;

class MikrotikSshService
{
    protected string $host;
    protected int $port;
    protected string $username;
    protected string $password;
    protected ?SSH2 $ssh = null;

    public function __construct(string $host, string $username, string $password, int $port = 22)
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }

    /**
     * Connect and login to Mikrotik via SSH
     */
    public function connect(): void
    {
        Log::info("MikrotikSshService: Connecting to {$this->host}:{$this->port}");
        
        $this->ssh = new SSH2($this->host, $this->port);
        
        if (!$this->ssh->login($this->username, $this->password)) {
            throw new Exception("MikrotikSshService: Login failed for user '{$this->username}'.");
        }
        
        Log::info("MikrotikSshService: Connected successfully.");
    }

    /**
     * Execute a command and return the string output
     */
    public function exec(string $command): string
    {
        if (!$this->ssh) {
            $this->connect();
        }

        Log::debug("MikrotikSshService: Executing -> {$command}");
        $output = $this->ssh->exec($command);
        return $output;
    }

    /**
     * Disconnect from SSH
     */
    public function disconnect(): void
    {
        if ($this->ssh) {
            $this->ssh->disconnect();
            $this->ssh = null;
            Log::info("MikrotikSshService: Disconnected.");
        }
    }
}
