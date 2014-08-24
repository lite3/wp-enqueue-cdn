=== WP Enqueue CDN ===
Contributors: lite3
Donate link: https://me.alipay.com/lite3
Tags: optimization, style, script, resources, plugins, query-string
Requires at least: 3.0.0
Tested up to: 3.9.1
Stable tag: 1.5.1

WP Resources URL Optimization is a Wordpress plugin optimized browser cache, it will greatly enhance the website page display speed and reduce the pressure of the server to handle static files.

== Description ==

WP Resources URL Optimization is a Wordpress plugin optimized browser cache,
it will greatly enhance the website page display speed and reduce the pressure of the server to handle static files.
Default wp added after the static files the query string(version number) to ensure that the static files are modified 
immediately after the performance to the browser (front-end), this way there is a drawback: 
the browser will request the server regardless of whether the file is modified,
If the file has been modified it will download the new file, 
if the file has not been modified http status code 304 is returned to inform the browser reads the local cache. 
The goal of the plugin: do not have to re-initiate the request to the server, 
and directly read the browser cache when the file has not been modified.


Related Links:

* <a href="http://www.litefeel.com/wp-resources-url-optimization/" title="WP Resources URL Optimization Plugin for WordPress">Plugin Homepage</a>
* <a href="http://www.litefeel.com/" title="Author For WP Resources URL Optimization Plugin">Author Homepage</a>
* <a href="https://github.com/lite3/wp-resources-url-optimization" title="on GitHub">on GitHub</a>

== Installation ==

You can find, download and install WP Resources URL Optimization directly from the **Plugins** section in WordPress.

If you want to install manually, download and unzip the `wp-resources-url-optimization.zip` file and upload to the `/wp-content/plugins/wp-resources-url-optimization/` directory. Then activate the plugin through the **Plugins** section in WordPress.

== Frequently Asked Questions ==

= The plugin will affect what files? =

The plug-in will modify the css and js file url used in the css sprite technology, the picture address are also taken to modify, while ensuring css work properly.

= Why is the plugin is enabled, js and css url did not change? =

Please ensure that js and css files added by use wp_enqueue_script and wp_enqueue_style method.

= My css file has css sprite technology, the picture will load properly? =

Yes, The plug has been considered the problem of the sprite.

= When I want to modify some files, which files I need to modify? =

Please modify the original file, not automatically generated files.

= When I want to add some files, Do i need to add a version number? =

No. The plugin will automatically generate a new files when the file modified. 

== Changelog ==
= 1.5.1 =
* Tested up to: 3.9.1
= 1.5.0 =
* New: add config for only remove query string.
= 1.4.3 =
* Compatibility check for 3.6, nothing new, just bump version to tell everyone this plugin still works.
= 1.4.2 =
* Fixed: Displays an warning when the resources is not file.
= 1.4 =
* Fixed: Displays an error when you first install the plugin.
= 1.3 =
* New: Automatically delete the older version of the backup file.
= 1.2.2 =
* Fixed: ignore log trace.
= 1.2.1 =
* Fixed: Fix wrong css sprite url.
= 1.2 =
* Fixed: Fix Not working when WordPress Address and Site Address are not the same.
* Modify: Move all back files to '{WordPress Address}/wp-content/wp-resources-back/' directory.
= 1.1 =
* Fixed: Fix 'File_exists' of warning  when the css sprite is relative path.
= 1.0 =
* Initial public release.
