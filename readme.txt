=== BuddyPress Groups Extras ===
Contributors: slaFFik
Tags: buddypress, groups, ajax, meta, custom, fields, extend, admin, privacy
Requires at least: 3.4 and BP 1.6
Tested up to: 3.6 and BP 1.8.1
Stable tag: 3.6.5

After activating your groups will have ability to create any custom fields they want. Also extra page will appear with chosen content.

== Description ==

BuddyPress Groups doesn't have much predefined content inside. Forums and activity are created (or not) by users. But most of the time group creators (aka administrators) have much more to say or explain to other members of community.

BuddyPress Groups Extras will give ability to them to create extra content.

= General =
* Choose groups you want to allow custom fields and pages
* Define who will have access to managing fields or pages in groups (groups admins or site admins or both)
* Create predefined Set of Fields that can be imported to all groups on a site OR can be imported on per group basis
* Get tutorials on how to use the plugin (simplified and advanced variants) right in your admin area

= Groups Custom Fields =
* Create custom fields using various type (radios, checkboxes, dropdown select, textarea and text)
* Edit fields data on Edit Group Details page in Group Admin area
* Display / hide page, where all groups fields will be displayed (and rename it too)
* Reorder fields

= Groups Custom Pages =
* Create group pages (for group FAQ or wiki, or events, or descriptions or whatever you want)
* Edit pages data in Group Admin area using WP RichEditor (with embedding content that WordPress supports!)
* Display / hide page, where all groups pages will be displayed (and rename it too)
* Reorder pages

And even more in PRO version:

* include groups pages and fields into global groups search
* have a flexible access management to allow members see groups extra content only when they have rights
* several groups top levels pages
* group blog (collaborative or not) posts
* group polls
* and much more.

Make your groups full of possibilities!

== Installation ==

1. Upload plugin folder `/buddypress-groups-extras/` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to BP Groups Extras under Settings menu and make customisations as needed.

== Frequently Asked Questions ==

= Why don't I see Fields/Pages in group navigation after plugin activation and enabling it for that particular group? =
Please go to group admin area and define Fields and Pages navigation labels and check them to Show. This is done to prevent displaying empty pages with no content.

= How can I change the look and feel of a plugin output in groups? =
= How can I redefine templates? =
Starting from BPGE v3.6 you can now change the html that is used to display any content in front-end (in groups) right from your theme folder. This is useful if you want to change the way pages and fields are displayed, but it works for "Admin -> Extras" management pages as well.

To do this you need to create a folder called `bpge` in the root of your theme. So for example if you use Frisco theme, create a folder like this: `wp-content/themes/frisco-for-buddypress/bpge`. Then copy required file (that you want to modify) from this plugin folder `wp-content/plugins/buddypress-groups-extras/views/front` to that created in a theme. That is it - now the plugin uses the template from your theme.

== Screenshots ==

1. Admin Page
2. Custom fields on Edit Group Details page
3. Extra Fields management
4. Adding new custom field
5. Adding new group page
6. New Tutorials page in admin area


== Upgrade Notice ==

= 3.6.5 (11.09.2013) =
* Added error message when saving required fields as empty on group "Admin -> Details" page
* Added some social network love in admin area
* Updated i18n files

== Changelog ==

= 3.6.5 (11.09.2013) =
* Added error message when saving required fields as empty on group "Admin -> Details" page
* Added some social love in admin area
* Updated i18n files

= 3.6.4 (02.09.2013) =
* Improved the list of groups pages in wp-admin area (easily access groups and pages from there using direct links)
* Improved groups data deletion
* Fixed importing set of fields into groups
* Added ability to define whether imported fields will be visible or not in the groups (display option)

= 3.6.3 (24.08.2013) =
* Several minor changes to improve the way plugin works with PRO extensions

= 3.6.2 (14.08.2013) =
* Fix 404 error on saving data in admin area if WordPress installed in subdirectory
* Updated German translation
* Added French translation

= 3.6.1 (11.07.2013) =
* Enhancement: Now group custom pages and fields fully inherites group privacy options for current logged in user (they will not appear even in navigation)
* New: ability to enable rich editor for custom fields (textarea's)
* Updated German translation (props <a href="http://www.per4mance.com/">Thorsten Wollenhöfe</a>)

= 3.6 (03.07.2013) =
* Improved css for some parts of views
* New feature: you can now redefine templates that are used to display plugin data in groups right in your theme

= 3.5.10 (21.06.2013) =
* Fixed issue with access to some parts of Extras in groups admin area

= 3.5.9 (17.06.2013) =
* Fixed display order of groups top level navigation items (created by other plugins as well)
* Fixed default 1st nav item logic

= 3.5.8 (15.06.2013) =
* Introducing Pro feature - Search

= 3.5.7 (11.06.2013) =
* Updated German translation (props <a href="http://www.per4mance.com/">Thorsten Wollenhöfe</a>)

= 3.5.6 (11.06.2013) =
* Preparing for search in group pages and fields (PRO feature)
* Several code fixes and improvements
* Donation button
* readme.txt update

= 3.5.5 (08.06.2013) =
* Fixed issues with saving data in admin area when WP installed in subdirectory
* Fixed admin area placement on WordPress MultiSite

= 3.5.4 (03.06.2013) =
* Fixed admin area styles (submin button disappeared)
* Fixed groups pages/fields visibility issues
* Fixed doubling options from General tab on site General Settings page

= 3.5.3 (01.06.2013) =
* Fully rewrote plugin admin area (again). It looks the same, but code improvements are huge.
* Preparing for the PRO
* Fixed some display problems with special chars in fields/pages titles
* Fixed ajax problems from the previous version
* Added features pointer in plugin admin area

= 3.5.2 (25.05.2013) =
* Added Tutorials page in admin are with useful info on how to use the plugin and its data (basic example, getting any group fields and pages data to display elsewhere)

= 3.5.1 (22.05.2013) =
* Added More page in admin area to collect votes for new features

= 3.5 (22.05.2013) =
* Completely new admin area - tabbed and better looking
* New admin area option: User Access
* New admin area option: Apply set of fields to all groups
* Lots of other code improvements

= 3.4 (10.04.2013) =
* New admin area option: delete or preserve all plugin's data on its deactivation
* Fields DB managing fully rewritten - now in a better WordPress way (supports caching!)
* Added import fields from the previous version of a plugin
* Added Italian translation (props <a href="https://github.com/luccame">Luca Camellini</a>)
* Lots of other code improvements

= 3.3.3 (26.03.2013) =
* Added German translation (props <a href="http://www.per4mance.com/">Thorsten Wollenhöfe</a>)

= 3.3.2 (23.03.2013) =
* Fixed issue with renaming "Groups" Component into anything else (like Movies)

= 3.3.1 (22.03.2013) =
* Fixed group home page logic
* Fixed pages creation/saving issues
* Other minor cleanups (like WP 3.5 better support)

= 3.3 (19.03.2013) =
* Fixed lots of notices

= 3.2.2 (28.03.2012) =
* Admin area fixes (for WP Multisite mainly)

= 3.2.1 (22.03.2012) =
* Added ability to change groups pages slug on Edit page
* Fixed a bug with group menu display after deactivating BPGE functionality for it (hackish)
* Some other minor changes

= 3.1 (19.03.2012) =
* Fixed a major bug in displaying group pages with the same title but in different groups
* Some other minor fixes

= 3.0.1 =
* Fixed a bug with updating group data without fields

= 3.0 =
* Major update
* Default set of Fields that can be imported by group admins
* Create custom pages for each group (custom post type is used) and display them (for FAQ, or Wiki, or whatever)
* Reorder everything (group navigation links, fields and groups pages order)

= 2.0 =
* Was released by mistake

= 1.0 =
* Initial realease
