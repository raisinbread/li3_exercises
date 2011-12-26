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
	protected $_autoConfig = array('command');
	
	/**
	 * An Command instance used for I/O.
	 *
	 * @var lithium\console\Command
	 */
	protected $_command = null;
	
	/**
	 * Exercise initialization.
	 *
	 * @return void
	 */
	public function _init() {
		parent::_init();
		$this->loadSectionsFromScript();
	}
	
	protected function loadSectionsFromScript() {
		$info = Inspector::info(get_class($this));
		$script = dirname($info['file']) . '/scripts/' . $info['shortName'] . '.json';
		if(file_exists($script)) {
			$contents = json_decode(file_get_contents($script), 1);
			$this->_command->out(print_r($contents, 1));
		}
	}
}
