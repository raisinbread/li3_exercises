<?php
/**
 * li3_exercises
 *
 * @copyright     Copyright 2011, John David Anderson
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_exercises\extensions\exercise;

use lithium\analysis\Inspector;

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
	 * An Command instance used for I/O.
	 *
	 * @var lithium\console\Command
	 */
	protected $_command = null;
	
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
	 * Exercise init. Each method in the exercise prefixed with $this->_methodPrefix ('explain', 
	 * by default) is considered a step in the exercise. Steps are run() in the order they were
	 * originally defined.
	 *
	 * @return void
	 */
	public function _init() {
		parent::_init();
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
 		foreach($this->_steps as $step) {
 			$this->$step();
 		}
 	}
	
	/**
	 * Convenience wrapping for /lithium/console/Command class for easy 
	 * command-line input/output and formatting.
	 *
	 * @param string $name 
	 * @param string $arguments 
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		return $this->_command->invokeMethod($name, $arguments);
	}
}	
