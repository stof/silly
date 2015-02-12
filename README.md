# Silly

Silly CLI micro-framework based on Symfony Console.

[![Build Status](https://img.shields.io/travis/mnapoli/silly.svg?style=flat-square)](https://travis-ci.org/mnapoli/silly)
[![Coverage Status](https://img.shields.io/coveralls/mnapoli/silly/master.svg?style=flat-square)](https://coveralls.io/r/mnapoli/silly?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/mnapoli/silly.svg?style=flat-square)](https://scrutinizer-ci.com/g/mnapoli/silly/?branch=master)

## Installation

```bash
$ composer require mnapoli/silly
```

## Usage

To define a command:

```php
$app = new Silly\Application();

$app->command('greet name? --yell', function ($name, $yell, OutputInterface $output) {
    if ($name) {
        $text = 'Hello, '.$name;
    } else {
        $text = 'Hello';
    }

    if ($yell) {
        $text = strtoupper($text);
    }

    $output->writeln($text);
});

$app->run();
```

`Silly\Application` extends `Symfony\Console\Application` and can be used wherever Symfony's Application can.

### Command definition

Commands are defined using a string expression. The expression must start with the command name, optionally using `:` as a namespace separator, for example:

```
greet
demo:greet
```

#### Arguments

A command can take arguments:

- required argument: `greet name`
- optional argument: `greet name?`
- array argument with 0-n values: `greet name*`
- array argument with at least one value: `greet name+`

#### Options

A command can take options:

- an simple flag: `greet --yell`
- an option with an mandatory value: `greet --iterations=`
- an option with an optional value: `greet --iterations[=]`
- an option that can be used multiple times (the value would be an array): `greet [--iterations=]*`
- an option with a shortcut: `greet -y|--yell`

Options are always **optional** (duh). If an option is required, then it should be an argument.

### Command callable

A command can be [any callable](http://php.net/manual/en/language.types.callable.php):

```php
// Closure
$app->command('foo', function () {
    // ...
});

// An object implementing __invoke()
$app->command('foo', new InvokableObject());

// An object method
$app->command('foo', [$object, 'method']);

// A static class method
$app->command('foo', ['MyClass', 'method']);

// A function
$app->command('foo', ['someFunction']);
```

The callable can take as parameters the arguments and options defined in the expression:

```php
$app->command('greet name --yell', function ($name, $yell) {});
```

When running `bin/console greet john --yell`:

- `$name` will be `'john'`
- `$yell` will be `true`

You can also ask for the `$input` and `$output` parameters to get the traditional Symfony `InputInterface` and `OutputInterface` objects:

```php
$app->command('greet name', function (InputInterface $input, OutputInterface $output) {});
```

Finally, you can mix all that. Parameters are detected by their name.

```php
$app->command('greet name', function ($name, InputInterface $input, OutputInterface $output) {});
```

## Learn more

Silly is just an implementation over the Symfony Console. Read [its documentation](http://symfony.com/doc/current/components/console/introduction.html) to learn everything you can do with it.

## Contributing

See the [CONTRIBUTING](CONTRIBUTING.md) file.