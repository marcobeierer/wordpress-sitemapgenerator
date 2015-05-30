=== Sitemap Generator ===
Contributors: mbsec
Tags: sitemap, site map, sitemap.xml, xml sitemap, seo, google, yahoo, bing, baidu, yandex
Requires at least: 4.2
Tested up to: 4.2.2
Stable tag: 1.0.0-beta.3
License: AGPL v3
License URI: https://www.gnu.org/licenses/agpl-3.0.html

An easy to use XML sitemap generator for WordPress.

== Description ==
The [sitemap generator](https://www.marcobeierer.com/tools/sitemap-generator#wordpress) uses an external service to crawl your website and create a XML sitemap of your website. The generator works thus for every plugin out of the box. The computation costs for your website is also very low because the crawler acts like a normal visitor, who visits all pages once.

= Features =
* Simple setup.
* Works out of the box with all WordPress plugins.
* Low computation costs for your webserver.

= Technical Features =
* Respects your robots.txt file (also the crawl-delay directive)
* Adds PDF files to the sitemap

= Upcoming Technical Features =
* Support for noindex meta elements
* Generation of image sitemaps

= Is the service free of charge? =
The sitemap generator service allows you to create a sitemap with up to 500 pages for free. If your website has more pages, you could buy a token to create a sitemap with up to 15000 pages at the following website. The wordpress plugin itself is free of charge, but nearly useless without the external service.

[https://www.marcobeierer.com/tools/sitemap-generator-token](https://www.marcobeierer.com/tools/sitemap-generator-token)

= Limitations =
By default the sitemap generator indexes the first 500 pages of your website. If your website has more pages, please see the section 'Is the service free of charge?'.

= Warnings =
If you already have an existing sitemap.xml in your WordPress root directory, this file would be overwritten. It is thus recommended to backup your existing sitemap.xml file before using the sitemap generator. I also have not tested the generator on Windows webspace. You should also access the sitemap.xml after the generation finished and check if everything is fine.

= Pre-Installation Verification Test =
If you like to test if the sitemap generator works fine with your website before you will install the plugin, you could use the [online sitemap generator](https://www.marcobeierer.com/tools/sitemap-generator#generator) on my website, which uses to same technology as the plugin to generate the sitemaps.  

== Installation ==
1. Upload the 'mb-sitemap-generator' folder to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access the generator with the "Sitemap Generator" button in the sidebar and use the "Generate your sitemap" button to start the generation process. The sitemap will be saved as sitemap.xml in your WordPress root directory. **Be aware that an existing sitemap.xml file would be overwritten without asking.**
4. Use the "Show the sitemap" button to download your sitemap and check if the generated sitemap is complete.

== Screenshots ==

1. The user interface of the sitemap generator.

== Changelog ==

= 1.0.0-beta.1 =
*Release Date - 9th May, 2015*

* Initial release.

= 1.0.0-beta.2 =
*Release Date - 16th May, 2015*

* Improved the user interface.

= 1.0.0-beta.3 =
*Release Date - 30th May, 2015*

* Implemented support for authorization tokens.
