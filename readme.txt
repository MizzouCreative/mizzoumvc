=== MizzouMVC framework ===
Contributors: gilzow, metzenj, nicholsjc
Requires at least: 4.1
Tested up to: 4.5.2
Stable tag: 3.4.0
License: GPLv2 or later
Tags: Framework, MVC, theme development

MVC-based framework for rapid theme development

== Description ==
An MVC-based framework for rapidly building custom themes.  Please note that this is a framework for *DEVELOPING* themes; it doesn't do much until you use it to build your own custom theme.

The framework overrides WordPress’ normal templating system and instead uses it for routing. Knowledge of/experience with WordPress’ template hierarchy is **crucial**.   [Wordpress template hierarchy](https://developer.wordpress.org/files/2014/10/template-hierarchy.png)

What used to be “template” files are now your controllers.  The plugin ships with a default set of controllers: header, footer, index, archive, page, single, search, 404 and attachment.  Themes can either extend any of these defaults, or extend the base controller (Main) and override the defaults.  A child theme then can do the same to a controller in the parent theme.  Views work in a similar fashion: theme views will override the default views, child theme views will override parent theme views.  The plugin ships with a default collection of Models that covers 80% of cases, but themes and child themes can extend/override them as needed.

All models and controllers in a theme or child theme should be namespaced.  If you have a parent-child theme set up, ‘parent’ and ‘child’ should be added to their respective namespaces so the loader knows where to locate the requested class (e.g project\parent\models and project\child\models).


== Installation ==
Pretty standard:

1. Upload the plugin files to the `/wp-content/plugins/mizzoumvc` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

Please note that this is a framework for *DEVELOPING* themes; it doesn't do much until you use it to build your own custom theme.

== Changelog ==
= 3.4.0 =
* Add new object RenderType. Available to Main controller and views.  Contains boolean properties for each possible type of action (e.g. is_home, is_single, is_archive). Also contains property `current` which includes a string of the current render action (e.g. home, single, archive).  Menu object in view has been enhanced and now includes property `items` that is a nested array of all menu items for the given menu (e.g. Menu.Primary.items) that can be looped through in order to build a custom menu structure.  Individual items are identical to that as returned from `wp_get_nav_menu_items`. You can continue to access the formatted menu by either calling the menu directly (i.e. {{ Menu.Primary }} or by using the `formatted` property (e.g. {{ Menu.Primary.formatted }}

== Frequently Asked Questions ==
= I activated this plugin but it doesn't do anything (or nothing happened, or everthing still looks the same)!!! =
You're right!  This is a framework for building out custom themes quickly and efficiently.  The plugin by itself doesn't do much until a compatible theme is installed.

= Why? =
Code reuse. We had a lot of similar code in every custom theme we built which led to a boilerplate theme, with the actual site running as a child theme. But then we had situations where we actually needed true parent->child themes, thus the framework was born.  In addition, separation of concerns.  Front-end developers can focus on the views and the back-end devs can concentrate on the models and controllers.

= What will I need in order to use this framework? =
A compatible theme (see our starter theme) and familiarity with [Twig](http://twig.sensiolabs.org/).

= Are there some sites built on this framework I can see? =
Absolutely!

* https://admissions.missouri.edu/
* https://education.missouri.edu/
* https://bondlsc.missouri.edu/
* https://news.missouri.edu/
* https://truman.missouri.edu/
* https://ipp.missouri.edu/
* https://cellmu.missouri.edu/

== Upgrade Notice ==
none

== Screenshots ==
none, yet
