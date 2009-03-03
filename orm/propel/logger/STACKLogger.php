<?php
/**
 * This is a logger class that stacks log into a string.
 *
 * To use it, add the following code into your php file
 * include("../propel/logger/STACKLogger.php");
 * $logger = new STACKLogger();
 * Propel::setLogger($logger);
 *
 * Then execute the code with your propel objetcs and display the propel logs with :
 * echo ($logger->getDisplay());
 *
 * @package    propel.logger
 */

class STACKLogger {

	private $displayMessage;

	public function emergency($m) {
		$this->log($m, Propel::LOG_EMERG);
	}

	public function alert($m) {
		$this->log($m, Propel::LOG_ALERT);
	}

	public function crit($m) {
		$this->log($m, Propel::LOG_CRIT);
	}

	public function err($m) {
		$this->log($m, Propel::LOG_ERR);
	}

	public function warning($m) {
		$this->log($m, Propel::LOG_WARNING);
	}

	public function notice($m) {
		$this->log($m, Propel::LOG_NOTICE);
	}

	public function info($m) {
		$this->log($m, Propel::LOG_INFO);
	}

	public function debug($m) {
		$this->log($m, Propel::LOG_DEBUG);
	}

	public function log($m, $priority) {
		$this->display($m, $this->priorityToColor($priority));
	}

	private function display($message, $color) {
		//don't stack more than 10000 char
		if (strlen($this->displayMessage) < 10000) {
			$this->displayMessage .= "<font color='".$color."'>".$message."</font><br/>";
		}
	}

	public function getDisplay() {
		$tempDisplayMessage = $this->displayMessage;
		$this->displayMessage = "";
		return $tempDisplayMessage;
	}

	private function priorityToColor($priority) {
		switch($priority) {
			case Propel::LOG_EMERG:
				case Propel::LOG_ALERT:
					case Propel::LOG_CRIT:
						case Propel::LOG_ERR:
							return 'red';
							break;
							case Propel::LOG_WARNING:
								return 'orange';
								break;
								case Propel::LOG_NOTICE:
									return 'green';
									break;
									case Propel::LOG_INFO:
										return 'blue';
										break;
										case Propel::LOG_DEBUG:
											return 'grey';
											break;
										}
									}
								}

								?>
