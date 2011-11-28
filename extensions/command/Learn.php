<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
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
	public function run($command = null) {
		if($command == null) {
			Libraries::paths(array('exercises' => '{:library}\extensions\exercises\{:name}'));
			$exercises = Libraries::locate('exercises');

			foreach ($exercises as $key => $exercise) {
				$library = strtok($exercise, '\\');

				if (!$key || strtok($exercises[$key - 1] , '\\') != $library) {
					$this->out("{:heading}EXERCISES{:end} {:blue}via {$library}{:end}");
				}
				$info = Inspector::info($exercise);
				$name = strtolower(Inflector::slug($info['shortName']));

				$this->out($this->_pad($name) , 'heading');
				$this->out($this->_pad($info['description']), 2);
			}
		}
	}

	protected function _pad($message, $level = 1) {
		$padding = str_repeat(' ', $level * 4);
		return $padding . str_replace("\n", "\n{$padding}", $message);
	}
}

?>

