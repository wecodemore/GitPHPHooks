#!/usr/bin/php
<?php

namespace GitPHPHooks;

class Loader
{
	/** @type string */
	private $hook;

	/** @type string */
	private $location;

	/** @type \FilesystemIterator */
	private $fetched;

	/** @type \RegexIterator */
	private $filtered;

	/** @type \SplPriorityQueue */
	private $sorted;

	/**
	 * Set the hook name, the location and trigger the process
	 * @param string $hook
	 * @param string $location
	 */
	public function __construct( $hook, $location )
	{
		$this->setHook( $hook );
		$this->setLocation( $location );

		$this->process();
	}

	/**
	 * Grabs all files in a dir,
	 * filters them to only include PHP files
	 * where the file name contains the current hook name
	 * and sorts them by the first integer found in the file name.
	 */
	public function process()
	{
		$this->fetched = $this->fetch( $this->location );
		$this->fetched->rewind();

		$this->filtered = $this->filter( $this->fetched );
		$this->filtered->rewind();

		$this->sorted = $this->sort( $this->filtered );
		$this->sorted->rewind();

		foreach ( $this->sorted as $it )
			echo "FILTER: ".$this->sorted->current().PHP_EOL;
	}

	/**
	 * Sets the current git hook name
	 * @param string $file
	 * @return $this
	 */
	public function setHook( $file )
	{
		$this->hook = pathinfo(
			$file,
			PATHINFO_FILENAME
		);
	}

	/**
	 * Sets the folder/location where the PHP Git Hook files are located
	 * @param $location
	 */
	public function setLocation( $location )
	{
		$this->location = $location;
	}

	/**
	 * Fetches all files from the target folder
	 * @param  string $location
	 * @return \FilesystemIterator
	 */
	public function fetch( $location )
	{
		return new \FilesystemIterator(
			$location,
			FilesystemIterator::SKIP_DOTS
			| FilesystemIterator::FOLLOW_SYMLINKS
		);
	}

	/**
	 * Filters them to only include PHP files
	 * where the file name contains the current hook name
	 * @param  \Iterator $files
	 * @return \RegexIterator
	 */
	public function filter( \Iterator $files )
	{
		return new \RegexIterator(
			$files,
			'/('.$this->hook.')[\w-]*?\.php/i'
		);
	}

	/**
	 * Sorts files by the first `int` in the file name
	 * and adds them to a `\SplPriorityQueue
	 * @param  \Iterator $files
	 * @return \SplPriorityQueue
	 */
	public function sort( \Iterator $files )
	{
		$sorted = new \SplPriorityQueue();
		foreach ( $files as $file )
		{
			preg_match(
				'/\d+/',
				$files->current(),
				$priority
			);
			if ( empty( $priority ) )
				continue;

			$sorted->insert(
				$files->current(),
				$priority[0]
			);
		}

		return $sorted;
	}
}