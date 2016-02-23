[![Latest Stable Version](https://img.shields.io/packagist/v/wcm/git-php-hooks.svg?style=flat-square)](https://packagist.org/packages/wcm/git-php-hooks)
[![Latest Git Release](https://img.shields.io/github/tag/wecodemore/GitPHPHooks.svg?style=flat-square)](https://github.com/wecodemore/GitPHPHooks/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/wcm/git-php-hooks.svg?style=flat-square)](https://packagist.org/packages/wcm/git-php-hooks)
[![License](https://img.shields.io/packagist/l/wcm/git-php-hooks.svg?style=flat-square)](https://packagist.org/packages/wcm/git-php-hooks)
[![GitHub Stars](https://img.shields.io/github/stars/wecodemore/GitPHPHooks.svg?style=flat-square)](https://github.com/wecodemore/GitPHPHooks/stargazers)


![Git Logo](http://i.imgur.com/qzvwT4C.png)

# Git PHP Hooks

Write your git hooks in PHP, organize them on a per project base and attach them automatically.

## Git Hooks

> Hooks are little scripts you can place in `$GIT_DIR/hooks` directory to trigger action at certain points.

There're two types of git hooks:

 1. pre-push (runs client side)
 1. post-push (runs server side)

For more info on Git Hooks, please [take a look at the official docs](http://git-scm.com/docs/githooks) - they are quite good.

## How to

It's really easy:

 1. Add a folder to your project/repository. The name doesn't matter, as you have to specify
 it when triggering `GitPHPHooks`. The name in the following example is `'project-hooks'`. (Hint: Not having a name allows you to customize and organize it as you like. It also allows `git clone`ing into a project specific directory.)
 1. Open you `.git/hooks` directory and add a new Git hook file. For example: `pre-commit`
 (without file extension).
 1. Add a new PHP file to the newly created custom Git hooks folder (again, `'project-hooks'` in the example) that performs the task you want.

That's it.

All your Git hooks (inside `.git/hooks`) will have the same contents - only the target folder (`'project-hooks'`) name will (maybe) differ.

```php
#!/usr/bin/env php
<?php

include 'vendor/wcm/git-php-hooks/GitHooksLoader.php';
new \GitHooksLoader( __FILE__, 'project-hooks' );
```

**Explanation:**

 * The first line is a _hashbang_ to specify that we actually have a PHP file on the Command Line.
 * The 1st argument for `\GitHooksLoader()` is the name of the current file to make the
current hook identifyable for GitPHPHooks.
 * The 2nd argument is the target location where your
custom, pre-project Git PHP hook files for the current task are located.

## Naming convention

There's a naming convention that you ***must*** follow to properly attach PHP files to Git hooks.
Sorting files is also done by file name.

 1. If a Git hook name is found in the file name, it will get attached to _this specific_ hook
 and executed automatically. Example: `pre-commit_`
 1. If one of your hooking PHP files has a number attached, it will get added with this priority. Example: `_10`
 If it ain't got any `int` in the file name, it will get skipped. This is useful to temporarily
 disable files if you are testing the order or a new hook.
 1. The name in between the Git hook name and the priority is just an identifiyer for yourself. Example: `PHPUnit`

### Examples (and ready-to-use tasks)

Before jumping on examples, I suggest that you simply take a look at the
[GitPHPHooks Library repo](https://github.com/wecodemore/GitPHPHooksLibrary).
You will find a PHPLint and a PHP Mess Detector task and some others
(hint: I happily accept pull requests!).

A real world scenario (simplified version of the task that is available in the linked library)

> We want to run PHPLint before we commit

Add a new file named `pre-commit` in your `.git/hooks` directory. Then add a new directory in the
root folder of your project/repository, named i.e. `project-hooks`. In there, add a new PHP file
named `pre-commit_lint_10.php`. This file will automatically get added to your `pre-commit` hook
where you called the `\GitHooksLoader()` like shown above. It will get added with a priority
of 10. Then just put the following contents in your new file:

```php
#!/usr/bin/env php
<?php
$output = shell_exec( 'php -l' );
echo $output;
if ( $output === 1 )
	exit 1;
```

Of course, above code is a very poor example. For a more detailed one, please refer to the library
linked above. The GitPHPHooks Library runs two real world examples. To use **PHP Mess Detector** and **PHPLint**, I can just suggest using the library as those are currently built in. Again: If you have a custom one and want to share, just send a Pull Request.

## Grunt integration

It can easily be integrated with grunt via [`grunt-githooks`](https://github.com/wecodemore/grunt-githooks),
originally written by [@rhumaric](https://github.com/rhumaric/).

Setup your `grunt-githooks` task like this:

```javascript
php : {
	options      : {
		hashbang    : '#!/usr/bin/env php',
		startMarker : '\n<?php',
		template    : './templates/git-php-hooks.tmpl.hb'
	},
	'pre-push'   : 'none'
}
```

Then just add your hooked tasks to your project and use the following template:

```php
include 'vendor/wcm/git-php-hooks/GitHooksLoader.php';
new \GitHooksLoader( __FILE__, 'vendor/wcm/git-php-hooks-library/src' );
```

This example is assuming that you are using the
[GitPHPHooksLibrary](https://github.com/wecodemore/GitPHPHooksLibrary).
The template in this case would be located inside a `templates` directory in the root folder
of your project and be named `git-php-hooks.tmpl.hb`. It's important to set the hooks names
value to `none` as GitPHPHooks doesn't need a task name as it identifies tasks by the filename
by itself.

## Install

Add the repo to your stack. You can use Composer (w/o Satis as it's added to Packagist).
Simply add

```json
"wcm/git-php-hooks": "^1.0"
```

to your `composer.json` file. GitHub has a service hook added to this repo to auto-update whenever
this repo is updated. The `^1.0` version number will bring you all patches without breaking anything.
