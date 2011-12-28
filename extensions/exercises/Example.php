<?php
/**
 * li3_exercises
 *
 * @copyright     Copyright 2011, John David Anderson
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_exercises\extensions\exercises;

use li3_exercises\extensions\exercise\Exercise;

/**
 *  Example exercise.
 */
class Example extends Exercise {
	public function explainIntro() {
		$this->header('Welcome!');
	}
}
