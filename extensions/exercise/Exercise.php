<?php
/**
 * li3_exercises
 *
 * @copyright     Copyright 2011, John David Anderson
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_exercises\extensions\exercise;

use lithium\analysis\Inspector;
use lithium\test\Unit;

/**
 * Parent exercise class. Contains logic needed to run the exercise
 * with its accompanying tests.
 */
class Exercise extends \lithium\core\Object {
	
	/**
	 * Class properties to be auto-configged. 
	 *
	 * @see lithium\core\Object
	 * @var string
	 */
	protected $_autoConfig = array('command', 'methodPrefix');
	
	/**
	 * Command instance used for I/O.
	 *
	 * @var lithium\console\Command
	 */
	protected $_command = null;
	
	/**
	 * Unit instance for assertion.
	 *
	 * @var lithium\test\Unit
	 */
	protected $_unit = null;
	
	/**
	 * Prefix for methods used in exercise.
	 *
	 * @var string
	 */
	protected $_methodPrefix = 'explain';
	
	/**
	 * A list of steps to be completed for this exercise.
	 *
	 * @var array
	 */
	protected $_steps = array();
	
	/**
	 * Index for the current step.
	 *
	 * @var int
	 */
	protected $_currentStep = 0;
	
	/**
	 * Exercise init. Each method in the exercise prefixed with $this->_methodPrefix ('explain', 
	 * by default) is considered a step in the exercise. Steps are run() in the order they were
	 * originally defined.
	 *
	 * @return void
	 */
	public function _init() {
		parent::_init();
		$this->_initUnit();
		$this->_initSteps();
	}
	
	/**
	 * Test unit init. Used for assertions in exercises.
	 *
	 * @return void
	 */
	protected function _initUnit() {
		$this->_unit = new Unit();
	}
	
	/**
	 * Creates step list for this exercise.
	 * 
	 * @return void
	 */
	protected function _initSteps() {
		$methods = Inspector::methods($this)->to('array');
		foreach($methods as $method) {
			if(substr($method['name'], 0, strlen($this->_methodPrefix)) === $this->_methodPrefix) {
				$this->_steps[] = $method['name'];
			}
		}
	}
	
	/**
	 * Runs the steps in this exercise.
	 *
	 * @return void
	 */
 	public function run() {
		$this->_moveToLatestStep();
		foreach($this->_steps as $step) {
			$success = false;
			while(!$success) {
				$this->_initUnit();
				$this->$step();
				$results = $this->_unit->results();
				if($this->stepSuccess($results)) {
					$this->_logLastCompletedStep($step);
					$this->_command->clear();
					$success = true;
				} else {
					$this->_command->clear();
					$this->_printErrors($results);
				}
			}
		}
		$this->header("Exercise complete.");
		$response = $this->in("{:cyan}Run again?{:end}", 
		array(
			'choices' => array('y', 'n'),
			'default' => 'n'
		));
		if($response == 'y') {
			$this->_command->clear();
			$this->_clearLogFile();
			$this->_initSteps();
			return $this->run();
		}
 	}
	
	/**
	 * Prints out errors found in 
	 *
	 * @param string $results 
	 * @return void
	 * @author John Anderson
	 */
	protected function _printErrors($results) {
		
		foreach($results as $result) {
			if($result['result'] === 'fail') {
				$this->_command->hr();
				$this->_command->error('{:error}' . $result['message'] . '{:end}');
			}
		}
		$this->_command->hr();
		$this->_command->out();
	}
	
	/**
	 * Returns the name of the log file for this exercise.
	 *
	 * @return void
	 * @author John Anderson
	 */
	protected function _getLogFileName() {
		$tmpDir = LITHIUM_APP_PATH . '/resources/tmp';
		$filename = $tmpDir . '/' . \lithium\util\Inflector::slug(__CLASS__) . '.log';
		return $filename;
	}
	
	/**
	 * Removes the log file for this exercise.
	 *
	 * @return void
	 * @author John Anderson
	 */
	protected function _clearLogFile() {
		unlink($this->_getLogFileName());
	}
	
	/**
	 * Returns the name of the last completed step, or boolean false if none found.
	 *
	 * @return void
	 * @author John Anderson
	 */
	protected function _getLastCompletedStep() {
		$filename = $this->_getLogFileName();
		if(file_exists($filename)) {
			$contents = unserialize(file_get_contents($filename));
			if(isset($contents['step'])) {
				return($contents['step']);
			}
		}
		return false;
	}
	
	/**
	 * Moves to the last completed step, if one is found.
	 *
	 * @author John Anderson
	 */
	protected function _moveToLatestStep() {
		$lastStep = $this->_getLastCompletedStep();
		if($lastStep !== false) {
			foreach($this->_steps as $number => $name) {
				unset($this->_steps[$number]);
				if($name == $lastStep) {
					return;
				}
			}
		}
	}
	
	/**
	 * Logs last completed step in temporary directory.
	 *
	 * @return void
	 */
	protected function _logLastCompletedStep($step) {
		$data = array(
			'time' => time(),
			'step' => $step
		);
		file_put_contents($this->_getLogFileName(), serialize($data));
	}
	
	/**
	 * Verifies step results.
	 *
	 * @author John Anderson
	 */
	protected function stepSuccess($results) {
		$success = true;
		foreach($results as $result) {
			$success = $success && ($result['result'] == 'pass');
		}
		return $success;
	}
	
	/**
	 * Filler output for when you just want the user to work. Ignores input,
	 * but halts exercise while the user works and waits for input submission.
	 *
	 * @param string $message 
	 * @return void
	 * @author John Anderson
	 */
	public function pause($message = '') {
		$fillerMessages = array(
			"Please press ENTER when you're ready.",
			"Go ahead and press ENTER when you're done.",
			"Press ENTER to continue.",
			"When you're done, press ENTER.",
			"Please press ENTER when to move on."
		);
		if($message === '') {
			$message = $fillerMessages[array_rand($fillerMessages)];
		}
		$this->out();
		$this->in("{:cyan}" . $message . "{:end}");
	}
	
	/**
	 * Convenience wrapping for /lithium/console/Command and 
	 * lihtium\test\Unit class for easy assertions and
	 * command-line input/output and formatting.
	 *
	 * @param string $name 
	 * @param string $arguments 
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		if(method_exists($this->_command, $name)) {
			return $this->_command->invokeMethod($name, $arguments);
		} else {
			return $this->_unit->invokeMethod($name, $arguments);
		}
	}
}	
