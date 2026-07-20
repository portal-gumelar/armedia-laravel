<?php

namespace App\Services\Networking;

use Exception;
use Illuminate\Support\Facades\Log;

class TelnetClient
{
    protected $connection;
    protected $host;
    protected $port;
    protected $timeout;
    
    // Line ending expected by the OLT (usually \r\n or \n)
    protected $lineEnding = "\n"; 

    public function __construct(string $host, int $port = 23, int $timeout = 10)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * Connect to the Telnet server.
     */
    public function connect(): void
    {
        Log::info("TelnetClient: Connecting to {$this->host}:{$this->port}");
        
        $this->connection = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);

        if (! $this->connection) {
            throw new Exception("Telnet connection failed: $errstr ($errno)");
        }

        stream_set_timeout($this->connection, $this->timeout);
        
        Log::info("TelnetClient: Connected successfully.");
    }

    /**
     * Disconnect from the Telnet server.
     */
    public function disconnect(): void
    {
        if ($this->connection) {
            fclose($this->connection);
            $this->connection = null;
            Log::info("TelnetClient: Disconnected.");
        }
    }

    /**
     * Wait for a specific prompt to appear in the output stream.
     * Throws an exception if the prompt is not found within the timeout.
     */
    public function waitPrompt(string $prompt): string
    {
        $buffer = '';
        $start = time();

        while (!feof($this->connection)) {
            // Read chunks of data
            $chunk = fread($this->connection, 128);
            if ($chunk !== false) {
                $buffer .= $chunk;
            }

            // Check if the prompt exists in the current buffer
            if (strpos($buffer, $prompt) !== false) {
                return $buffer;
            }

            // Timeout safety
            if ((time() - $start) > $this->timeout) {
                throw new Exception("TelnetClient: Timeout waiting for prompt '{$prompt}'");
            }

            // Give the stream a tiny rest to prevent CPU spinning
            usleep(100000); 
        }

        return $buffer;
    }

    /**
     * Write a command to the socket, appending the configured line ending.
     */
    public function writeCommand(string $command): void
    {
        if (!$this->connection) {
            throw new Exception("TelnetClient: Not connected.");
        }

        fwrite($this->connection, $command . $this->lineEnding);
        Log::debug("TelnetClient: Sent -> {$command}");
    }

    /**
     * Set a custom line ending if the OLT requires "\r\n" instead of "\n".
     */
    public function setLineEnding(string $ending): self
    {
        $this->lineEnding = $ending;
        return $this;
    }
}
