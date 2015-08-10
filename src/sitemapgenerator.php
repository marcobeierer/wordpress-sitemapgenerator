<?php
/*
 * @package    SitemapGenerator
 * @copyright  Copyright (C) 2015 Marco Beierer. All rights reserved.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

/*
Plugin Name: Sitemap Generator
Plugin URI: https://www.marcobeierer.com/wordpress-plugins/sitemap-generator
Description: An easy to use XML Sitemap Generator with support for image and video sitemaps for WordPress.
Version: 1.0.2
Author: Marco Beierer
Author URI: https://www.marcobeierer.com
License: GPL v3
Text Domain: Marco Beierer
*/

add_action('admin_menu', 'register_sitemap_generator_page');
function register_sitemap_generator_page() {
	add_menu_page('Sitemap Generator', 'Sitemap Generator', 'manage_options', 'sitemap-generator', 'sitemap_generator_page', '', 99); 
}

function sitemap_generator_page() {
?>
	<div class="wrap">
		<h2>Sitemap Generator</h2>
		<div class="card" id="sitemap-widget" ng-app="sitemapGeneratorApp" ng-strict-di>
			<h3>Generate a XML sitemap of your site</h3>
			<div ng-controller="SitemapController">
				<form name="sitemapForm">
					<div class="input-group">
						<span class="input-group-addon">
							<i class="glyphicon glyphicon-globe"></i>
						</span>
						<span class="input-group-btn">
							<button type="submit" class="button {{ generateClass }}" ng-click="generate()" ng-disabled="generateDisabled">Generate your sitemap</button>
							<a class="button {{ downloadClass }}" ng-click="download()" ng-disabled="downloadDisabled" download="sitemap.xml" ng-href="{{ href }}">Show the sitemap</a>
						</span>
					</div>
				</form>
				<p class="alert well-sm {{ messageClass }}">{{ message }} <span ng-if="pageCount > 0 && downloadDisabled">{{ pageCount }} pages already crawled.</span></p>
			</div>
		</div>
	</div>
	<div class="wrap">
		<div class="card">
			<p>You like this plugin? I would be happy if you could write a short review or vote for it in the <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/mb-sitemap-generator">WordPress Plugin Directory</a>!</p>
			<p>Any questions? Please have a look at the <a target="_blank" href="https://wordpress.org/plugins/mb-sitemap-generator/faq/">FAQ section</a> or ask your question in the <a target="_blank" href="https://wordpress.org/support/plugin/mb-sitemap-generator">support area</a>. I would be pleased to help you out!</p>
		</div>
	</div>
<?
}

add_action('admin_enqueue_scripts', 'load_sitemap_generator_admin_scripts');
function load_sitemap_generator_admin_scripts($hook) {

	if ($hook == 'toplevel_page_sitemap-generator') {

		$angularURL = plugins_url('js/angular.min.js', __FILE__);
		$sitemapGeneratorURL = plugins_url('js/sitemap.js?v=1', __FILE__);

		wp_enqueue_script('sitemap_generator_angularjs', $angularURL);
		wp_enqueue_script('sitemap_generator_sitemapgeneratorjs', $sitemapGeneratorURL);
	}
}

add_action('wp_ajax_sitemap_proxy', 'sitemap_proxy_callback');
function sitemap_proxy_callback() {

	$baseurl = get_site_url();
	$baseurl64 = strtr(base64_encode($baseurl), '+/', '-_');

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://api.marcobeierer.com/sitemap/v2/' . $baseurl64 . '?pdfs=1&origin_system=wordpress');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$token = get_option('sitemap-generator-token');
	if ($token != '') {
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: BEARER ' . $token));
	}

	$response = curl_exec($ch);

	$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

	curl_close($ch);

	if ($statusCode == 200 && $contentType == 'application/xml') {

		$reader = new XMLReader();
		$reader->xml($response, 'UTF-8');
		$reader->setParserProperty(XMLReader::VALIDATE, true);

		if ($reader->isValid()) { // TODO check if empty?

			$rootPath = get_home_path();
			if ($rootPath != '') {
				file_put_contents($rootPath . DIRECTORY_SEPARATOR . 'sitemap.xml', $response); // TODO handle and report error
			}
		}
	}

	if (function_exists('http_response_code')) {
		http_response_code($statusCode);
	}
	else { // fix for PHP version older than 5.4.0
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header($protocol . ' ' . $statusCode . ' ');
	}

	header("Content-Type: $contentType");

	echo $response;
	wp_die();
}

add_action('admin_menu', 'register_sitemap_generator_settings_page');
function register_sitemap_generator_settings_page() {
	add_submenu_page('sitemap-generator', 'Sitemap Generator Settings', 'Settings', 'manage_options', 'sitemap-generator-settings', 'sitemap_generator_settings_page');
	add_action('admin_init', 'register_sitemap_generator_settings');
}

function register_sitemap_generator_settings() {
	register_setting('sitemap-generator-settings-group', 'sitemap-generator-token');
}

function sitemap_generator_settings_page() {
?>
	<div class="wrap">
		<h2>Sitemap Generator Settings</h2>
		<div class="card">
			<form method="post" action="options.php">
				<?php settings_fields('sitemap-generator-settings-group'); ?>
				<?php do_settings_sections('sitemap-generator-settings-group'); ?>
				<h3>Your Token</h3>
				<p><textarea name="sitemap-generator-token" style="width: 100%; min-height: 350px;"><?php echo esc_attr(get_option('sitemap-generator-token')); ?></textarea></p>
				<p>The sitemap generator service allows you to create a sitemap with up to 500 URLs for free. If your website has more URLs or you like to integrate an image and video sitemap, you can buy a token to create a sitemap with up to 50000 URLs at the following website.</p>
				<p><a href="https://www.marcobeierer.com/tools/sitemap-generator-token">https://www.marcobeierer.com/tools/sitemap-generator-token</a></p>
				<?php submit_button(); ?>
			</form>
		</div>
	</div>
<?
}
