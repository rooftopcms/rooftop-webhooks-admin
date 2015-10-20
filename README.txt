=== Plugin Name ===
Contributors: rooftopcms
Tags: rooftop, api, headless, webhooks
Requires at least: 4.3
Tested up to: 4.3
Stable tag: 4.3
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

rooftop-webhooks-admin adds an admin interface to maintain a collection of webhook endpoints

== Description ==

rooftop-webhooks-admin provides an admin UI for adding, editing and removing
webhook URL which are stored in Redis.
The companion plugins, rooftop-change-notifier and rooftop-queue-runner, push
events on to the queue and dispatch them to your endpoints respectively.

== Installation ==

rooftop-webhooks-admin is a Composer plugin, so you can include it in your Composer.json.

Otherwise you can install manually:

1. Upload the `rooftop-webhooks-admin` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. There is no step 3 :-)

== Frequently Asked Questions ==

= Can this be used without Rooftop CMS? =

Yes, it's a Wordpress plugin you're welcome to use outside the context of Rooftop CMS. We haven't tested it, though.

== Changelog ==

= 0.0.1 =
* Initial release

== What's Rooftop CMS? ==

Rooftop CMS is a hosted, API-first WordPress CMS for developers and content creators. Use WordPress as your content management system, and build your website or application in the language best suited to the job.

https://www.rooftopcms.com
