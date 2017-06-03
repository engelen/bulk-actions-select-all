=== Plugin Name ===
Contributors: engelen
Tags: admin,bulk actions,bulk,posts,terms,categories,tags,trash,untrash,delete,bulk delete,select all,edit posts,untrash all posts,delete all posts, trash all posts
Requires at least: 3.5
Tested up to: 4.8
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds an option to the admin posts and terms overview pages to select all items (instead of just the ones on the current page) to apply bulk actions. "Trash", "Restore", "Delete", and custom bulk actions are supported. Supports both CPTs and custom taxonomies.

== Description ==

**Bulk Actions: Select All** adds an option to the posts and taxonomy terms overviews in the WordPress Admin to select "All Entries". By default, WordPress only allows you to select the posts/terms on the current page.

= Supported content types =
The plugin works for posts, pages and custom post types. Furthermore, it supports categories, tags and custom taxonomies. At this point, it doesn't support comments and other object types besides post types and taxonomies.

== Installation ==

For automatic installation, all you have to do is install and activate the plugin from the Plugins screen in your WordPress admin panel!

For manual installation, please download the plugin and follow these steps:

1. Upload the folder `bulk-actions-select-all` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. An option to select all posts/terms now appears when you click the "Select all" checkbox in the posts/terms overview header
1. After clicking "Select all [x] entries", you can apply any bulk action you want, and it's applied on "all" posts/terms!

== Screenshots ==

1. When clicking on the "Select all" checkbox in the top left of your posts/terms table, a notice appears to select all posts/terms

2. The plugin allows to you to apply normal “Bulk Actions”, such as deleting posts, to all available posts

3. Bulk Actions Selects All also supports categories, tags and custom taxonomies

== Changelog ==

= 1.1.1 =
* Added support for custom bulk actions
* Added feedback notification for feature suggestions etc.
* Fixed colspan issue when dynamically toggling columns' visibility

= 1.1 =
* Added support for bulk selection of terms
* Commenting additions to functions and methods

= 1.0.1 =
* Fix "Select all" row background color

= 1.0 =
* Initial release