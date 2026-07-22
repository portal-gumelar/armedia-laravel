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

    /**
     * Get Netwatch Status
     * Returns array of [ 'host' => '1.1.1.1', 'status' => 'up|down' ]
     */
    public function getNetwatchStatus(): array
    {
        if (!$this->ssh) {
            throw new Exception("MikrotikSshService: Not connected.");
        }

        // Fetch netwatch output in terse format for easy parsing
        // RouterOS 7 uses 'print terse' or similar. We can parse it line by line.
        $output = $this->exec('/tool netwatch print terse');
        
        $results = [];
        $lines = explode("\n", trim($output));
        foreach ($lines as $line) {
            // example line: 0   host=10.152.6.30 timeout=1s interval=1m status=up since=jul/21/2026 23:10:00
            if (preg_match('/host=([\d\.]+)/', $line, $mHost)) {
                $status = 'down';
                if (preg_match('/status=up/', $line)) {
                    $status = 'up';
                }
                
                $results[] = [
                    'host' => $mHost[1],
                    'status' => $status,
                ];
            }
        }

        return $results;
    }

    /**
     * Get recent login failures from Mikrotik log
     * Returns array of IPs that failed to login
     */
    public function getLoginFailures(): array
    {
        if (!$this->ssh) {
            throw new Exception("MikrotikSshService: Not connected.");
        }

        // Fetch logs containing "login failure"
        $output = $this->exec('/log print terse where message~"login failure"');
        
        $failedIps = [];
        $lines = explode("\n", trim($output));
        foreach ($lines as $line) {
            // example line: 0 time=10:15:20 topics=system,error,critical message="login failure for user root from 192.168.1.100 via ssh"
            if (preg_match('/from\s+([\d\.]+)/', $line, $mIp)) {
                $failedIps[] = $mIp[1];
            }
        }

        // Return array of IP => count
        return array_count_values($failedIps);
    }

    /**
     * Add IP to MikroTik Firewall Address List (Blacklist)
     */
    public function blacklistIp(string $ip, string $list = 'blacklist', string $timeout = '1d'): void
    {
        if (!$this->ssh) {
            throw new Exception("MikrotikSshService: Not connected.");
        }

        $command = "/ip firewall address-list add list={$list} address={$ip} comment=\"Auto-Blacklist Bruteforce\" timeout={$timeout}";
        $this->exec($command);
        Log::info("MikrotikSshService: IP {$ip} added to {$list}.");
    }
}
