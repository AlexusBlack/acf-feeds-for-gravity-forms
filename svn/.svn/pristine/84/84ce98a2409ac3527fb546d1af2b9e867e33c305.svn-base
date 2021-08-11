=== ACF Feeds for Gravity Forms ===
Contributors: alexusblack
Tags: acf, advanced custom fields, gravity forms, feed, integration, form, entry
Requires at least: 5.4.0
Tested up to: 5.8.0
Stable tag: 1.0.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Write Gravity Forms submission fields into ACF fields. Accumulate values over time.

== Description ==

Use this plugin to update an ACF field(s) when Gravity Form is submitted. You also can accumulate value in a certain field (only fields of type `Number` supported at the moment).

Features:

* Target a wide range of WP entities: page, post, custom post, user, term, taxonomy, widget, comment, options page, current page/post.
* Use GF merge tags in the ACF Target field
* Simply map ACF and GF fields in one-to-one, one-to-many or many-to-many relations
* Use operations on ACF fields to modify currently stored values
* Number fields support +, - and * math operations
* All text compatible fields support + operation to join strings
* ACF field targets support merge tags
* Implement complex logic with conditional feeds

Any GF field compatible with string/number values should work. Tested GF fields: Single line text, Paragraph Text, Drop Down, Number, Checkboxes, Radio Buttons, Website, Email.
Any ACF field compatible with string/number values should work. Tested ACF fields: Text, Text Area, Number, Range, Email, Url, Password, Select, Checkbox, Radio Button, Button Group, True / False

Example use cases:

* Count number of submissions
* Remember name/login/email of the last user who submitted the form
* Make a simple page like feature
* Save name of a last sold product

== Installation ==

1. Upload `gravity-forms-acf-feeds` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In Gravity Form select `ACF Feeds` settings
4. Create a new ACF Feed
5. Enter ACF target selector or leave it empty for the current/post page
6. Map you form fields to ACF fields of your target

== Frequently Asked Questions ==

= How I can target the page where my form is located? =

Leave the target field empty, empty field means current page/post.

= How can I add/subtract a number to an ACF field instead of overriding it? =

Add + or - before the ACF field name in the mapping settings.

= Can I target page/post/user dynamically? =

Yes, the `Target` field supports merge tags, so you can pass target ID from your form. It can come from a [dynamically populated GF field](https://docs.gravityforms.com/using-dynamic-population/) too.

== Screenshots ==

1. Complex feed example: dynamic target, add & subtract accumulators, conditional feed
2. Simple feed example: Likes counter accumulator. GF settings prevent multiple likes from a single user

== Changelog ==

= 1.0.1 =
* Support + (concatenation) operation for all string compatible ACF fields
* Support + (addition) operation for all number compatible ACF fields
* Allow merge tags in ACF field names. Can be used for dynamic operations or to change target ACF field based on the form data.
* Code refactoring

= 1.0.0 =
* Initial release with basic functionality
