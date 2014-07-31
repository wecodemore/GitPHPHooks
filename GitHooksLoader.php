<?php

/**
 * Class GitHooksLoader
 * @package WCM\GitPHPHooks
 */
class GitHooksLoader
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

		$this->prepare();
		$this->load();
	}

	/**
	 * Grabs all files in a dir,
	 * filters them to only include PHP files
	 * where the file name contains the current hook name
	 * and sorts them by the first integer found in the file name.
	 */
	public function prepare()
	{
		$this->fetched = $this->fetch( $this->location );
		$this->fetched->rewind();

		$this->filtered = $this->filter( $this->fetched );
		$this->filtered->rewind();

		$this->sorted = $this->sort( $this->filtered );
		$this->sorted->rewind();
	}

	/**
	 * Loads the pre sorted files
	 */
	public function load()
	{
		foreach ( $this->sorted as $it )
			include_once $this->sorted->current();
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
	 * Filters the file list to only include PHP files
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
	 * and adds them to a `\SplPriorityQueue`.
	 * Files without a priority are ignored to allow easy disabling.
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