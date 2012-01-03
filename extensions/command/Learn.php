<?php
/**
 * li3_exercises
 *
 * @copyright     Copyright 2011, John David Anderson
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_exercises\extensions\command;

use lithium\core\Libraries;
use lithium\analysis\Inspector;
use lithium\util\Inflector;

/**
 * Starts an exercise, or lists the exercises available.
 */
class Learn extends \lithium\console\Command {
	
	/**
	 * List of exercises available to the system.
	 *
	 * @var array
	 */
	protected $_exercises = array();
	
	/**
	 * Exercise initialization.
	 *
	 * @return void
	 */
	public function _init() {
		parent::_init();
		Libraries::paths(array('exercises' => '{:library}\extensions\exercises\{:name}'));
		$exercises = Libraries::locate('exercises');
		foreach($exercises as $exercise) {
			$this->_exercises[$this->exerciseName($exercise)] = $exercise;
		}
	}
	
	/**
	 * Returns a sluggified exercise name based off the class name.
	 *
	 * @return string
	 */
	public function exerciseName($className) {
		$info = Inspector::info($className);
		return strtolower(Inflector::slug($info['shortName']));
	}
	
	/**
	 * Runs main console application logic. Returns a list of 
	 * available exercises by default. Otherwise runs the specified exercise.
	 *
	 * @param string $command 
	 * @return void
	 */
	public function run($command = null) {
		if($command == null) {
			foreach ($this->_exercises as $key => $exercise) {
				$library = strtok($exercise, '\\');
				$this->out("{:heading}EXERCISES{:end} {:blue}via {$library}{:end}");
				$info = Inspector::info($exercise);
				$this->out($this->_pad($key) , 'heading');
				$this->out($this->_pad($info['description']), 2);
				$this->stop();
			}
		}
		if(isset($this->_exercises[$command])) {
			$className = $this->_exercises[$command];
			$exercise = new $className(array('command' => $this));
			$exercise->run();
		} else {
			$this->out("{:error}The exercise {:end}{:blue}\"$command\"{:end}{:error} you specified cannot be found. Please supply a valid exercise name.{:end}");
			$this->out();
			$this->stop(1);
		}
	}

	/**
	 * Utility function used to align and pad plain text output.
	 *
	 * @param string $message Message to be padded.
	 * @param string $level Padding level.
	 * @return void
	 */
	protected function _pad($message, $level = 1) {
		$padding = str_repeat(' ', $level * 4);
		return $padding . str_replace("\n", "\n{$padding}", $message);
	}
}

?>