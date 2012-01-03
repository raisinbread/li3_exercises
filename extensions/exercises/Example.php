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
		$this->header('Introduction');
		
		$this->out("This is an example exercise that shows you how to *create* another exercise! Not too meta for you?");
		$this->pause();
	}
	
	public function explainCreateFile() {
		$this->header("Creating the Exercise File");
		
		$this->out("The first thing you need to do is create an exercise file. Let's create a new exercise called `Blog.` Do this by creating a new file in {:green}app/extensions/exercises/Blog.php{:end}.\n");
		$this->out("You'll need to do it in another terminal session or something.");
		$this->pause();
		
		$this->assertTrue(file_exists(LITHIUM_APP_PATH . '/extensions/exercises/Blog.php'), "I can't seem to see the file yet. Make sure you've created a new file at {:green}app/extensions/exercises/Blog.php{:end}.");
	}
	
	public function explainCreateClass() {
		$this->header("Creating an Exercise");
		
		$this->out("Now, let's create a class inside that file. Make sure it has the right namespace, and name the class {:purple}Blog{:end}. Make sure that it extends the {:purple}\li3_exercises\exercise\Exercise{:end} class.");
		$this->pause();
		
		require_once(LITHIUM_APP_PATH . '/extensions/exercises/Blog.php');
		$classExists = class_exists('\li3_exercises\extensions\exercises\Blog');
		$this->assertTrue($classExists, "I can't find that class. Make sure it's got the right namespace (li3_exercises\extensions\exercises)!");
		
		if($classExists) {
			$blog = new \li3_exercises\extensions\exercises\Blog();
			$this->assertTrue(is_a($blog, '\li3_exercises\extensions\exercise\Exercise'), "Oops! Make sure that the new Blog class extends {:green}li3_exercises\extensions\exercise\Exercise{:end}.");
		}
	}

	public function explainExplainMethods() {
		$this->header("The Explain Methods");
		
		$this->out("The next step is to create your first `explain` method. When an exercise is run, each user-defined method that begins with 'explain' is run in the order it was defined.\n");
		$this->out("Let's start by defining a new method called `explainIntro`.");
		$this->pause();
		
		$methods = \lithium\analysis\Inspector::methods('\li3_exercises\extensions\exercises\Blog', 'extents');
		$this->assertTrue(isset($methods['explainIntro']), "Hmmm. I can't seem to find a method defined on your new Blog class named '{:purple}explainIntro{:end}'. {:error}Make sure it has public visibility!{:end}");
	}
	
	public function explainCommandUsage() {
		$this->header("Exercises are Commands");
		
		$this->out("Now that you've got a method, your first step is letting your student know what's going on. Each instance of `Exercise` can also act much like a command. You can send output to your student by using the `header`, `out`, and other similar methods of the \lithium\console\Command class.\n");
		$this->out("It's common to start each step with a header. Add a header call to your `explainIntro` method, followed by another call to `out`. This will serve as your introduction.");
		$this->pause();
		$intro = $this->_getIntroOutput();
		
		$this->assertTrue(strlen($intro) > 1, 'Your `explainIntro` method didn\'t seem to have any output. Try calling {:purple}$this->out(){:end} or {:purple}$this->header(){:end}.');
		$this->assertTrue(strstr($intro, '------------------------------------') !== false, 'Did you forget to call {:purple}$this->header(){:end}{:error}?{:end}');
	}
	
	public function explainUnitUsage() {
		
	}
	
	protected function _getIntroOutput() {
		$tmpFile = LITHIUM_APP_PATH . '/resources/tmp/' . uniqid() . '.tmp';
		$output = fopen($tmpFile, 'x');
		$response = new \lithium\console\Response(compact('output'));
		$command = new \lithium\console\Command(compact('response'));
		$blog = new \li3_exercises\extensions\exercises\Blog(compact('command'));
		$blog->explainIntro();
		$intro = file_get_contents($tmpFile);
		unlink($tmpFile);
		return $intro;
	}
}
