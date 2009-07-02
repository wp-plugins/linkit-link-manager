=== LinkIt Link Manager ===
Contributors: zach
Donate link: http://www.codezach.com
Tags: links, manager
Requires at least: 2.7.1
Tested up to: 2.7.1
Stable tag: trunk

Automatically insert links via regular expression and associative text.

== Description ==

Automatically insert links via regular expression and associative text. Useful for normalizing links which appear multiple times on a site.

== Installation ==

1. Upload the plugin directory to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to Settings->LinkIt Options, insert links using the wizard.

**Usage** 
Go to Settings->LinkIt Options. Add/Edit/Remove links from the links using the link grid. The regular
expression field is what matches against the content of posts/pages. All matches are replaced with the address
in the link field using the display name, target, and link template provided.

**In Your Posts/Pages**
Insert text representing the link which matches the regular expression.

== Frequently Asked Questions ==

= Will this thing mangle my titles? =

It won't mangle titles.

= Will it mistakenly insert links randomly? =

I recommend using some special characters to denote the links - in the usage
example I used brackets ('[' and ']') to denote the links. Make sure you test your regular expressions
to avoid inserting links errantly.

== Screenshots ==
