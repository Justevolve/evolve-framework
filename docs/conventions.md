# Conventions

Apart from sticking to the [official WordPress documentation](https://codex.wordpress.org/Main_Page) and its [coding standards](http://make.wordpress.org/core/handbook/coding-standards/), the code contained within EF follows a series of conventions that should ensure that maintenance and compatibility between components are as smooth as possible.

As mentioned in the [requirements section](install.md), the code within the framework is compatible with PHP 5.2.4, which is also the minimum PHP version required by WordPress.

Here are the conventions used throughout the framework:

## Constants

All of the framework constants are defined in the `evolve-framework.php` file, which is located in the plugin folder.

Constants are all caps and share the `EV_` prefix.

## Classes

All of the PHP classes declared in the framework share the `Ev_` prefix.

## Functions

All of the PHP functions declared in the framework share the `ev_` prefix.

## CSS: `class` attributes and `id`s

Both on frontend and WordPress admin, CSS `class` attributes and `id`s are prefixed with `ev-`.

## Filters

Filter names all start with `ev` and are composed following a particular convention that allows to express the specificity of a filter in a given context: `prefix[subject:value]`.

For example, the following filter manages the addition of fields in a meta box called `test` shown in page editing screens:

`ev[post_type:page][metabox:test]`,

but you could also do the same for pages with a specific page template associated to them:

`ev[post_type:page][template:template-test.php][metabox:test]`.