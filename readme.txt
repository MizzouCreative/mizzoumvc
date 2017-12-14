=== MizzouMVC framework ===
Contributors: gilzow, metzenj, nicholsjc
Requires at least: 4.1
Tested up to: 4.8.1
Stable tag: 3.6.3
License: GPLv2 or later
Tags: Framework, MVC, theme development

MVC-based framework for rapid theme development

== Description ==
An MVC-based* (see FAQ) framework for rapidly building custom themes.  Please note that this is a framework for *DEVELOPING* themes; it doesn't do much until you use it to build your own custom theme.

The framework overrides WordPress’ normal templating system and instead uses it for routing. Knowledge of/experience with WordPress’ template hierarchy is **crucial**.   [Wordpress template hierarchy](https://developer.wordpress.org/files/2014/10/template-hierarchy.png)

What used to be “template” files are now your controllers.  The plugin ships with a default set of controllers: header, footer, index, archive, page, single, search, 404 and attachment.  Themes can either extend any of these defaults, or extend the base controller (Main) and override the defaults.  A child theme then can do the same to a controller in the parent theme.  Views work in a similar fashion: theme views will override the default views, child theme views will override parent theme views.  The plugin ships with a default collection of Models that covers 80% of cases, but themes and child themes can extend/override them as needed.

All models and controllers in a theme or child theme should be namespaced.  If you have a parent-child theme set up, ‘parent’ and ‘child’ should be added to their respective namespaces so the loader knows where to locate the requested class (e.g project\parent\models and project\child\models).


== Installation ==
Pretty standard:

1. Upload the plugin files to the `/wp-content/plugins/mizzoumvc` directory
1. Activate the plugin through the 'Plugins' screen in WordPress
1. After that you'll be able to update the plugin from within the wordpress admin console

Please note that this is a framework for *DEVELOPING* themes; it doesn't do much until you use it to build your own custom theme.

== Upgrade Notice ==
In All Theme Settings --> Site Wide, add two new Custom Fields
* header_title_anchor - set value to whatever you want to have included as the last piece of text in the <title> element (or empty if you dont want it); framework assumes empty
* use_framework_stylesheet - set to 'yes' if you want to use the stylesheet from the framework instead of your style.css file; framework assumes 'no'

== Changelog ==
= 3.6.3 =
* RenderType object now includes is_front_page (front_page in view)
* Loader class now assures a trailing slash for plugin/theme paths before using
* WpBase class now checks to see if it is passed a post type prefix (pre v3.6.0) and handles appropriately
* VIEW_CACHE_LOCATION constant renamed to MIZZOUMVC_VIEW_CACHE_LOCATION to match constant naming convention
* Added MIZZOUMVC_DISABLE_VIEW_CACHE to _completely_ disable cache generation in the Twig view engine
    * Setting WP_DEBUG to true now instructs the Twig engine to recompile the requested view(s) but does not affect caching
    * Setting MIZZOUMVC_DISABLE_VIEW_CACHE to true will completely disable Twig caching

= 3.6.1 =
* People model wasn't compatible with the changes to WpBase

= 3.6.0 =
* Framework now supports framework plugins
* Added a basic archive controller
* Can now access members of Site via object notation (->) or as an array
* WpBase (and Children) now accepts a FQ namespace class that it will use to return new posts instances via retrieveContent and convertPost(s).
* WpBase (and children) now require an instance of the Loader class to be passed in to facilitiate the above

= 3.5.3 =
Bug fix related to incorrect permissions being set on cache directory

= 3.5.2 =
Bug fix related to Director role not working correctly in certain multisite situations

= 3.5.1 =
* Changed label for theme settings area from "All Settings" to "All Theme Settings"
* Added ability to use internal search OR external search for both search and 404 since not everyone has a GSA.
* new markup in the default views + updated css
* new site-wide options (see config.ini for further explanation)
  * header_title_anchor - the final text piece included in the <title> element.  Previously, this was hard-coded to "University of Missouri"
  * use_framework_stylesheet - Should the framework fallback to using the include stylesheet in the framework instead of what is in the theme

= 3.4.0 =
* Add new object RenderType. Available to Main controller and views.  Contains boolean properties for each possible type of action (e.g. is_home, is_single, is_archive). Also contains property `current` which includes a string of the current render action (e.g. home, single, archive).  Menu object in view has been enhanced and now includes property `items` that is a nested array of all menu items for the given menu (e.g. Menu.Primary.items) that can be looped through in order to build a custom menu structure.  Individual items are identical to that as returned from `wp_get_nav_menu_items`. You can continue to access the formatted menu by either calling the menu directly (i.e. {{ Menu.Primary }} or by using the `formatted` property (e.g. {{ Menu.Primary.formatted }}

== Frequently Asked Questions ==
= I activated this plugin but it doesn't do anything (or nothing happened, or everthing still looks the same)!!! =
You're right!  This is a framework for building out custom themes quickly and efficiently.  The plugin by itself doesn't do much until a compatible theme is installed.

= Why? =
Code reuse. We had a lot of similar code in every custom theme we built which led to a boilerplate theme, with the actual site running as a child theme. But then we had situations where we actually needed true parent->child themes, thus the framework was born.  In addition, separation of concerns.  Front-end developers can focus on the views and the back-end devs can concentrate on the models and controllers.

= This framework doesn't really follow the MVC pattern! What gives? =
No, no it doesn't.  We don't do any create, read, update, etc. in wordpress.  Everything is really just index(). "MVC" has become similar to "kleenex" and "xerox" in that people use it (and understand it) to mean the separation of business logic from presentation.  It's probably closest to MVP/Pasive view or Presentation Model, but even those don't match perfectly. Either way, the purpose is separation of concerns, keeping business logic out of the display.

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

= The theme settings area is UGLY. Why didn't you make it prettier/formatted? =
Because I'm a back-end developer and don't care what it looks like. =P  Mostly because it takes time, and the settings from one build-out to the next are almost always different.  The way it is now is SUPER fast: add a custom field key and value and it's immediately available in your view. When we need to make the settings area look nicer, or use something nicer than a text field for data entry, we use ACF (+ Pro) and target a specific settings group. It does tie us to the ACF plugin, but only for the formatting; the data retrieval is completely independent of the plugin.

== Roadmap ==
= Automated Upgrade Routine =


= Introduction of a Front Controller =
I don't like that you have to instantiate your controller class after defining it, but I haven't figured out a solid way to send the route information from wordpress and pass it to a single front controller (short of using the global space *blargh*.)

= Framework settings menu area =
Framework settings are pulled in via framework-settings.ini file in the theme, with defaults hard-coded.  I don't like it, but it's fast and convenient, just not pretty or easy for end-users.

= Override of default views =
The ability to swap out the default view files for your own, thereby giving you the option for a "grandparent" (or default) set of views.  For now you're stuck with ours.

= Move global space functions into static class =

= Introduction of alternative templating engines =
We really like Twig, but maybe you don't. I'd like to build in the option to choose a different templating engine.

= Ability to have more view ancestry =
Right now you can have a child theme, a parent theme and then all of the stuff that ships with this framework.  But there's no reason we can't add some additional ancestry between the parent theme and the framework.  I just haven't had a situation yet where I needed to, but

= Auto Detection of ACF Pro usage =
We use ACF Pro, but some of those features require special, specific processing on the display side of things. We have a model to handle this situation, but I'd like to automate the inclusion of the model when it detects the presence of ACF Pro

== Screenshots ==
none, yet
