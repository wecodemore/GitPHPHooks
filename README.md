# Git PHP Hooks

Write your git hooks in PHP, organize them on a per project base and attach them automatically.

## Git Hooks

There're two types of git hooks:

 1. pre-push (runs client side)
 1. post-push (runs server side)

For more info on Git Hooks, please consult your favorite search engine.

## How to

It's really easy:

 1. Add a folder to your project/repository. The name doesn't matter as you have to specify
 it when triggering `GitPHPHooks`. Not having a name allows you to customize and organize it as you like.
 1. Open you `.git/hooks` directory and add a new git hook file. For example: `pre-commit`
 (without file extension).
 1. Add a new PHP file to the newly created custom git hooks folder that performs the task you want.

That's it.

All your git hooks will have the same contents - only the target folder name will (maybe) differ.

	#!/usr/bin/evn php
	<?php

	include 'vendor/wcm/git-php-hooks/GitPHPHooks.php';
	new \GitHooksLoader( __FILE__, 'git-hooks' );

The first line is a _hashbang_ to specify that we actually have a PHP file on the Command Line.
The first argument for `\GitPHPHooks\Loader()` is the name of the current file to make the
current hook identifyable for GitPHPHooks. The second argument is the target location where your
custom, pre-project Git PHP hook files for the current task are located.

## Naming convention

There's a naming convention that you _must_ follow to properly attach PHP files to git hooks.
Sorting files is also done by file name.

 1. If a git hook name is found in the file name, it will get attached to this hook
 and executed automatically.
 1. If one of your hooking PHP files has a number attached, it will get added with this priority.
 If it ain't got any `int` in the file name, it will get skipped. This is useful to temporarily
 disable files if you are testing the order or a new hook.

### Example

A real world scenario

> We want to run PHPLint before we commit

Add a new file named `pre-commit` in your `.git/hooks` directory. Then add a new directory in the
root folder of your project/repository, named i.e. `git-hooks`. In there, add a new PHP file
named `pre-commit_lint_10.php`. This file will automatically get added to your `pre-commit` hook
where you called the `\GitPHPHooks\Loader()` like shown above. It will get added with a priority
of 10. Then just put the following contents in your new file:

	#!/usr/bin/php
	<?php
	$output = shell_exec( 'php -l' );
	echo $output;
	exit 1;

Of course, above code is a very poor example. For a more detailed one, please take a look
at [this GitHub Gist](https://github.com/sumocoders/snippets/blob/master/git/hooks/lint) by Sumocoders.

## Install

Add the repo to your stack. You can use Composer (w/o Satis as it's added to Packagist).
