=== Lyon Site Activity ===
Contributors: wheatoncollege
Donate link: https://wheatoncollege.edu/giving/
Tags: Logging, Site Activity, Admin  
Requires at least: 3.5 
Tested up to: 5.3.2
Requires PHP: 5.4.0
Stable tag: 1.0.0
License: GPLv3  
License URI: http://www.gnu.org/licenses/gpl-3.0.html  

== Description ==
A simple, lightweight plugin that gives site administrators an at-a-glance view of recent content edits.

A single screen under the **Tools** menu  shows tabular data for the latest created, latest modified, and/or latest trashed post, pages, or custom post types.  You can also find the latest created taxonomies and custom taxonomies, along with latest added media elements (currently only PDF application types are supported.)  Dynamic navigation control is found in a sticky element at the top of the page, allowing fast access to special areas of interest.

A single screen under the **Settings** menu will allow you to show only those posts, custom post types, taxonomies, or custom taxonomies that you are interested in seeing.  Please note that while custom post type and custom taxonomies you create will be listed here, it does not mean that they are fully supported at this time.  They should work as desired, but custom post types and custom taxonomies can be tricky.

== Background ==

This plugin was designed to fulfill a need of Wheaton College, Norton MA. We wanted to track _some_ editor activity without storing records in the database. The guiding principle was that the code would be lightweight and _read only_.

One use case is to review recent edits to ensure user compliance with existing guidelines.

Another is to review if there have been any recent edits at all.

This plugin supplements our paid site monitoring software. The idea is to catch issues early, providing a teaching opportunity for web editors that are less experienced.

== Installation ==

Install the plugin as you would any others.

== Frequently Asked Questions ==

= Does this plugin add any database tables to store activity? =
No, this plugin does not alter the database in any way.  It queries the database for recent activity and shows it in a table format.  It queries the database every time the Site Activity page is viewed, and ONLY when that page is viewed.

== Screenshots ==

1. Access the tables under the "Tools" menu
2. Tabular view of activity
3. Change plugin settings under the "Settings" menu
4. Check off boxes of what you wish to see

== Changelog ==

= 2.0.2 =
- Added version information to the settings page as well

= 2.0.1 =
- Fixed warning for in_array call
- Added array of custom post types to ignore from known plugin sources
- Made hyperlinks open in new window/tab
- Added machine name to Post/Taxonomy label for settings page to make it easier to distinguish duplicate label uses

= 2.0.0 =
* Initial Release of Plugin