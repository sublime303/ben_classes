<?php
/*
 This file is part of the PHP example/library code for communication with with
Brainboxes Ethernet-attached data acquisition and control products, and is
provided by Brainboxes Limited.  Examples in other programming languages are
also available.
Visit http://www.brainboxes.com to see our range of Brainboxes Ethernet-
attached data acquisition and control products, and to check for updates to
this code package.

This is free and unencumbered software released into the public domain.
*/
namespace Brainboxes\IO;

	class EDDevice
	{

		/**
		 * Create a TCP Connection to a Brainboxes EDDevice
		 * Use Brainboxes Boost.IO to find out the IP address of the Brainboxes EDDevice on your network
		 * @param string $ip The IP address of the EDDevice to connect to, defaults to 192.168.127.255, which will be the case if the device is on a network without DHCP and the connecting computer is on the same subnet
		 * @param int $port The TCP IP Port number, defaults to 9500
		 */
		public function __construct($ip = "192.168.127.255", $port = 9500)
		{
			$this->ip = $ip;
			$this->port = $port;
		}
		
		public function __destruct()
		{
			$this->disconnect();
		}
		
		/**
		 * Check if Conenction to the EDDevice is open
		 */
		public $isConnected = false;

		/**
		 * Open Connection to EDDevice, must be called before SendCommand
		 */
		public function connect()
		{
			if ($this->isConnected) return true;
			$this->log("Connecting...");
			
			$this->socket = fsockopen($this->ip, $this->port, $errno, $errstr);
			if ($this->socket) 
			{
				$this->log("Connected");
				$this->isConnected = true;
				stream_set_timeout($this->socket, 10000);
			} 
			else 
			{
				$this->isConnected = false;
				$message = "Unable to open socket: {$this->ip}:{$this->port} ERROR: $errno - $errstr";
				$this->log($message);
				throw new \Exception($message);
			}
			return $this->isConnected;
		}
		
		/**
		 * Disconnect from EDDevice, will be automatically called when class is disposed
		 */
		public function disconnect()
		{
			if (!$this->isConnected) return;
			$this->log("Disconnecting...");
			
			fclose($this->socket);
			
			$this->isConnected = false;
			$this->log("Disconnected");
		}

		/**
		 * Send an ASCII DCON command to an EDDevice
		 * @param string $command ASCII DCON command see Brainboxes reference manual for details
		 * @return string the response from the Brainboxes ED Device or null if there is no response
		 */
		public function sendCommand($command)
		{
			if (!$this->isConnected)
			{
				$message = "Device not connected, call connect() before calling sendCommand";
				$this->log($message);
				throw new \LogicException($message);
			}
			if(!is_string($command))
			{ 
				$message = "Invalid command {$command} : command must be an string";
				$this->log($message);
				throw new \InvalidArgumentException($message);
			}
			$this->log("<== " . $command);
			
			$asciiCommand = mb_convert_encoding($command.$this->newLine, $this->commsEncoding, mb_internal_encoding());
			fwrite($this->socket, $asciiCommand);
			
			if($this->commandDoesNotHaveResponse($command))
			{
				$this->log("no response for this command");
				return null;
			}
			
			$asciiResponse = fread($this->socket, 64);
			
			$info = stream_get_meta_data($this->socket);
			
			if ($info['timed_out']) {
				$message = "Connection timed out!";
				$this->log($message);
				throw new \Exception($message);
			}
			
			$response = mb_convert_encoding($asciiResponse, mb_internal_encoding(), $this->commsEncoding);
			$response = str_replace($this->newLine, "", $response);
			
			$this->log("==> " . $response);
			return $response;

		}

		/**
		 * Test whether the command should have an ASCII response from the ED Device or not
		 * @param string $command
		 * @return string
		 */
		protected function commandDoesNotHaveResponse($command)
		{
			return in_array($command, $this->commandsWithoutResponse);
		}
		
		/**
		 * write log messages to apache log if in debug mode
		 * @param string $message
		 */
		protected function log($message)
		{
			$this->debug && file_put_contents('php://stderr', "Brainboxes EDDevice: ".$message.PHP_EOL);
		}
		
		/**
		 * set to true to log debugging messages
		 */
		public $debug = false;

		/**
		 * network port
		 * @var int
		 */
		protected $port;
		/**
		 * IP address
		 * @var string
		 */
		protected $ip;
		
		/**
		 * TCP socket
		 * @var 
		 */
		protected $socket;
		
		/**
		 * ASCII character encoding is used to send and recieve data to/from the EDDevice
		 * ASCII = ISO-8859-1
		 * @var string
		 */
		protected $commsEncoding = "ASCII";

		/**
		 * New line character used by the ASCII protocol to signal end of message
		 * @var string
		 */
		protected $newLine = "\r";

		/**
		 * commands which have no response
		 * #** Synchronized Sampling Command
		 * ~** Host is OK Command
		 * Implementing classes will simply return null for these commands
		 * @var array
		 */
		protected $commandsWithoutResponse = array("#**","~**");
	}

?>