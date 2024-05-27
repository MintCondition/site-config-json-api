=== Site Config JSON API ===
Contributors: Your Name
Tags: example, plugin
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 0.2.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Allows connection to an API endpoint delivering SiteConfiguration via JSON. 
IMPORTANT NOTE: This exposes a good bit of your site configuration to anyone with an endpoint. The only 
authentication is through the built in API key system. It is NOT meant for production sites or ones that 
you care about securing. 

Why would you want to use this? Well, this will allow you te generate JSON describing a lot about your site,
for example, it gives info on CPTs and ACF meta fields attached to those CPTs. This can be handy when working
with something like ChatGPT or another system. Instead of having to remember exactly what post and field Keys
you need when asking for a code block, you can just hit the API endpoint and get a current list of them all. 
Feed that to ChatGPT and ask your questions. "Please modify the main query for the Events posts so that it 
sorts by the Event Start Date and only shows events in the future." ChatGPT will understand that Events uses
the post key "mysite_evts" and the field it needs to work with is "evt_start_date". 

There are other reasons you might use this as well. 

== Installation ==
If you can't figure out how to install a WP Plugin, you should NOT use this one.

== Changelog ==
== 0.2.3 =
Style changes
Cleanup a few items

= 0.2.0 =
Added API Key system to validate and secure the site configuration.
Added API Keys page to the WP Admin Menu. Limited to 5 Keys.

= 0.1.1 =
Removed some testing code.

= 0.1.0 =
* Added core functionality to expose endpoint.

= 0.0.1 =
* Creation.