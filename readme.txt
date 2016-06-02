=== MizzouMVC framework ===
Contributors: gilzow, metzenj, nicholsjc
Requires at least: 4.1
Tested up to: 4.5.2
Stable tag: 3.4.0
Framework for rapid build-out of custom sites

== Changelog ==
= 3.4.0 - Add new object RenderType. Available to Main controller and views.  Contains boolean properties for each
possible type of action (e.g. is_home, is_single, is_archive). Also contains property `current` which includes a string
of the current render action (e.g. home, single, archive).  Menu object in view has been enhanced and now includes property
`items` that is a nested array of all menu items for the given menu (e.g. Menu.Primary.items) that can be looped through
in order to build a custom menu structure.  Individual items are identical to that as returned from `wp_get_nav_menu_items`.
You can continue to access the formatted menu by either calling the menu directly (i.e. {{ Menu.Primary }} or by using the
`formatted` property (e.g. {{ Menu.Primary.formatted }}

== Copyright ==
2016 Curators of the University of Missouri