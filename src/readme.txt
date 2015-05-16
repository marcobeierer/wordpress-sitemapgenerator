=== Sitemap Generator ===
Contributors: mbsec
Tags: sitemap, site map
Requires at least: 4.2
Tested up to: 4.2.2
Stable tag: 1.0.0-beta.1
License: AGPL v3
License URI: https://www.gnu.org/licenses/agpl-3.0.html

An easy to use sitemap generator for WordPress.

== Description ==
The [sitemap generator](https://www.marcobeierer.com/tools/sitemap-generator#wordpress) uses an external service to crawl your website and create a sitemap of your website. The generator works thus for every plugin out of the box. The computation costs for your website is also very low because the crawler acts like a normal visitor, who visits all pages once.

= Features =
* Simple setup.
* Works out of the box with all WordPress plugins.
* Low computations costs for your webserver.

= Is the service free of charge? =
Currently yes, but just during the beta phase. Afterwards the service costs about 1 Euro per 500 pages per month. The wordpress plugin itself is free of charge, but nearly useless without the external service.

= Limitations =
By default the sitemap generator indexes the first 500 pages of your website. If you create a file called *allow-sitemap-generator.html* in your WordPress root directory, the sitemap generator indexes up to 2500 pages. Please contact me if your website is larger. The limitations only apply during the beta phase.

= Warnings =
If you already have an existing sitemap.xml in your WordPress root directory, this file would be overwritten. It is thus recommended to backup your existing sitemap.xml file before using the sitemap generator. I also have not tested the generator on Windows webspace. You should also access the sitemap.xml after the generation finished and check if everything is fine.

== Installation ==
1. Upload the 'mb-sitemap-generator' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the "Generate Sitemap" button the create a sitemap. Be aware that the page loads and is not shown until the generation of the sitemap is done. The sitemap will be saved as sitemap.xml in your WordPress root directory. **Be aware that an existing sitemap.xml file would be overwritten without asking.**
4. Access http(s)://www.example.com/sitemap.xml and check if the generated sitemap is complete.

== Changelog ==

= 1.0.0-beta.1 =
*Release Date - 9th May, 2015*

* Initial release