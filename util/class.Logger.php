<?php
/**
 * Logging utility that can be used for writing log entries to text files.
 * 
 * @version 2009-08-15
 * @author Markus Karjalainen
 *
 */
class Logger {
	
	private $filename = null;
	
	private $DEBUG_PREFIX = ' [DEBUG] ';
	private $INFO_PREFIX = ' [INFO] ';
	private $ERROR_PREFIX = ' [ERROR] ';
	
	public function Logger($filename) {
		$this->filename = $filename;
	}
	
	public function setFilename($filename) {
		$this->filename = $filename;
	}
	
	public function getFilename($filename) {
		return $this->filename;
	}
	
	public function debug($string) {
		$this->write($this->DEBUG_PREFIX, $string);
	}
	
	public function info($string) {
		$this->write($this->INFO_PREFIX, $string);
	}
	
	public function error($string) {
		$this->write($this->ERROR_PREFIX, $string);
	}
	
	private function write($prefix, $string) {
		
		$handle = fopen($this->filename, 'a');
		
		fwrite($handle, date('Y-m-d H:i:s') . $prefix . trim($string) . "\n", 1024);
		
		fclose($handle);
	}
}
