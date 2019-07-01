<?php
/*
 * @package    SitemapGenerator
 * @copyright  Copyright (C) 2015 - 2019 Marco Beierer. All rights reserved.
 * @license    https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */
defined('ABSPATH') or die('Restricted access.');

/*
Plugin Name: Sitemap Generator
Plugin URI: https://www.marcobeierer.com/wordpress-plugins/sitemap-generator
Description: An easy to use XML Sitemap Generator with support for image and video sitemaps for WordPress.
Version: 1.6.2
Author: Marco Beierer
Author URI: https://www.marcobeierer.com
License: GPL v3
Text Domain: Marco Beierer
*/

add_action('admin_menu', 'register_sitemap_generator_page');
function register_sitemap_generator_page() {
	add_menu_page('Sitemap Generator', 'Sitemap Generator', 'manage_options', 'sitemap-generator', 'sitemap_generator_page', '', 132132001);
}

function sitemap_generator_page() {
	include_once('shared_functions.php'); ?>

	<div ng-app="sitemapGeneratorApp" ng-strict-di>
		<div ng-controller="SitemapController">
			<div class="wrap">
				<h2>Sitemap Generator</h2>

				<?php
					cURLCheck();
					localhostCheck();
				?>

				<div class="card" id="sitemap-widget">
					<h3>Generate a XML sitemap of your site</h3>
					<div>
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
						<p class="alert well-sm {{ messageClass }}"><span ng-bind-html="message | sanitize"></span> <span ng-if="pageCount > 0 && downloadDisabled">{{ pageCount }} URLs already processed.</span></p>
					</div>
				</div>
				<div class="card" ng-if="stats">
					<h4>Sitemap Stats</h4>
					<table>
						<tr>
							<td>Sitemap URL count:</td>
							<td>{{ stats.SitemapURLCount }}</td>
						</tr>
						<?php
							$token = get_option('sitemap-generator-token');
							if ($token != ''): 
						?>
						<tr>
							<td>Sitemap image count:</td>
							<td>{{ stats.SitemapImageCount }}</td>
						</tr>
						<tr>
							<td>Sitemap video count:</td>
							<td>{{ stats.SitemapVideoCount }}</td>
						</tr>
						<?php
							endif; 
						?>
					</table>
					<h4>Crawl Stats</h4>
					<table>
						<tr>
							<td>Crawled URLs count:</td>
							<td>{{ stats.CrawledResourcesCount }}</td>
						</tr>
						<tr>
							<td>Dead URLs count:</td>
							<td>{{ stats.DeadResourcesCount }}</td>
						</tr>
						<tr>
							<td>Timed out URLs count:</td>
							<td>{{ stats.TimedOutResourcesCount }}</td>
						</tr>
					</table>
					<p><i>Want to find out more about the dead and timed out URLs? Have a look at my <a href="https://wordpress.org/plugins/link-checker/">Link Checker</a> for WordPress.</i></p>
				</div>
				
				<?php if (get_option('sitemap-generator-token') == ''): ?>
				<div class="card">
					<h4>Sitemap Generator Professional</h4>
					<p>Your site has <strong>more than 500 URLs</strong> or you like to integrate an <strong>image sitemap</strong> or a <strong>video sitemap</strong>? Then have a look at the <a href="https://www.marcobeierer.com/wordpress-plugins/sitemap-generator-professional">Sitemap Generator Professional</a>.
				</div>
				<?php endif; ?>

				<div class="card">
					<h4>You like the Sitemap Generator?</h4>
					<p>I would be happy if you can write a review or vote for it in the <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/mb-sitemap-generator">WordPress Plugin Directory</a>!</p>
					<?php if (new DateTime() <= new DateTime('2015-08-31')): ?>
					<p>The <strong>first three reviewers in August 2015 get a token</strong> for the generation of sitemaps (including images and videos) with up to 2500 URLs and a lifetime of one year <strong>for free</strong>! The token is worth 40.- Euro. Just write a review and send me the address of your website by email to <a href="mailto:email@marcobeierer.com">email@marcobeierer.com</a> and I will send you the token.</p>
					<?php endif; ?>
				</div>

				<?php if (false && get_option('sitemap-generator-token') == ''): ?>
				<div class="card">
					<h4>Blogging about WordPress?</h4>
					<p>I offer a special starter package for you and your audience. Get a free token for the Sitemap Generator Professional for your blog and up to five tokens for a competition, public give-away or something else.</p>
					<p>Please find the <a href="https://www.marcobeierer.com/wordpress-plugins/blogger-package">details about the package on my website</a> or write me an email to <a href="mailto:email@marcobeierer.com">email@marcobeierer.com</a> if you have any questions.</p>
				</div>
				<?php endif; ?>

				<div class="card">
					<h4>Any questions?</h4>
					<p>Please have a look at the <a target="_blank" href="https://wordpress.org/plugins/mb-sitemap-generator/faq/">FAQ section</a> or ask your question in the <a target="_blank" href="https://wordpress.org/support/plugin/mb-sitemap-generator">support area</a>. I would be pleased to help you out!</p>
				</div>
			</div>
		</div>
	</div>
<?php
}

add_action('admin_enqueue_scripts', 'load_sitemap_generator_admin_scripts');
function load_sitemap_generator_admin_scripts($hook) {

	if ($hook == 'toplevel_page_sitemap-generator') {

		$angularURL = plugins_url('js/angular.min.js', __FILE__);
		$sitemapGeneratorVarsURL = plugins_url('js/sitemap-vars.js?v=1', __FILE__);
		$sitemapGeneratorURL = plugins_url('js/sitemap.js?v=6', __FILE__);

		wp_enqueue_script('sitemap_generator_angularjs', $angularURL);
		wp_enqueue_script('sitemap_generator_sitemapgeneratorvarsjs', $sitemapGeneratorVarsURL);
		wp_enqueue_script('sitemap_generator_sitemapgeneratorjs', $sitemapGeneratorURL);
	}
}

add_action('wp_ajax_sitemap_proxy', 'sitemap_proxy_callback');
function sitemap_proxy_callback() {
	$baseurl = get_home_url();
	$baseurl64 = strtr(base64_encode($baseurl), '+/', '-_');

	$ch = curl_init();

	$requestURL = sprintf('https://api.marcobeierer.com/sitemap/v2/%s?pdfs=1&origin_system=wordpress&max_fetchers=%d&ignore_embedded_content=%d&reference_count_threshold=%d&query_params_to_remove=%s&disable_cookies=%d', 
		$baseurl64, get_option('sitemap-generator-max-fetchers', 3), 
		get_option('sitemap-generator-ignore-embedded-content', 0),
		get_option('sitemap-generator-reference-count-threshold', -1),
		urlencode(get_option('sitemap-generator-query-params-to-remove', '')),
		get_option('sitemap-generator-disable-cookies', 0)
	);

	curl_setopt($ch, CURLOPT_URL, $requestURL);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

	$token = get_option('sitemap-generator-token');
	if ($token != '') {
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: BEARER ' . $token));
	}

	$response = curl_exec($ch);

	if ($response === false) {
		$errorMessage = curl_error($ch);

		$responseHeader = '';
		$responseBody = json_encode($errorMessage);

		$contentType = 'application/json';
		$statusCode = 504; // gateway timeout

		header('X-CURL-Error: 1');
	} else {
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

		$responseHeader = substr($response, 0, $headerSize);
		$responseBody = substr($response, $headerSize); // returns json string if error, or xml if sitemap

		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		$statusCode = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
	}

	curl_close($ch);

	if ($statusCode == 200 && $contentType == 'application/xml') {
		$matches = array();
		preg_match('/\r\nX-Limit-Reached: (.*)\r\n/', $responseHeader, $matches);
		if (isset($matches[1])) {
			header("X-Limit-Reached: $matches[1]");
		}

		$matches = array();
		preg_match('/\r\nX-Stats: (.*)\r\n/', $responseHeader, $matches);
		if (isset($matches[1])) {
			header("X-Stats: $matches[1]");
		}

		$reader = new XMLReader();
		$reader->xml($responseBody, 'UTF-8');
		$reader->setParserProperty(XMLReader::VALIDATE, true);

		if ($reader->isValid()) { // TODO check if empty?
			$rootPath = get_home_path();
			if ($rootPath != '') {
				$success = file_put_contents($rootPath . DIRECTORY_SEPARATOR . 'sitemap.xml', $responseBody); // TODO handle and report error
				if ($success === false) {
					$statusCode = 500;
					$responseBody = json_encode('could not write sitemap to disk');
					header('X-Write-Error: 1');
				}
			}
		}
	}

	header("Content-Type: $contentType");
	header('Cache-Control: no-store');

	// recheck status code because it could have been set to 500 in condition above
	// NOTE the following cannot be used for if statusCode != 200 because wp_die calls _ajax_wp_die_handler and there the status code is overwritten and always set to 200
	if ($statusCode == 200 && $contentType == 'application/xml') {
		echo $responseBody;
		wp_die('', '', $statusCode);
	} 
	else {
		wp_send_json(json_decode($responseBody), $statusCode);
	}
}

add_action('admin_menu', 'register_sitemap_generator_settings_page');
function register_sitemap_generator_settings_page() {
	add_submenu_page('sitemap-generator', 'Sitemap Generator Settings', 'Settings', 'manage_options', 'sitemap-generator-settings', 'sitemap_generator_settings_page');
	add_action('admin_init', 'register_sitemap_generator_settings');
}

function register_sitemap_generator_settings() {
	register_setting('sitemap-generator-settings-group', 'sitemap-generator-token');
	register_setting('sitemap-generator-settings-group', 'sitemap-generator-max-fetchers', 'intval');
	register_setting('sitemap-generator-settings-group', 'sitemap-generator-ignore-embedded-content', 'intval');
	register_setting('sitemap-generator-settings-group', 'sitemap-generator-reference-count-threshold', 'intval');
	register_setting('sitemap-generator-settings-group', 'sitemap-generator-query-params-to-remove'); // string is default
	register_setting('sitemap-generator-settings-group', 'sitemap-generator-disable-cookies', 'intval');
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
				<p>The Sitemap Generator service allows you to create a sitemap with up to 500 URLs for free. If your website has more URLs or you like to integrate an image and video sitemap, you can buy a token for the <a href="https://www.marcobeierer.com/wordpress-plugins/sitemap-generator-professional">Sitemap Generator Professional</a> to create a sitemap with up to 50000 URLs.</p>

				<h3>Concurrent Connections</h3>
				<p>
					<select name="sitemap-generator-max-fetchers" style="width: 100%;">
					<?php for ($i = 1; $i <= 10; $i++) { ?>
						<option <?php if ((int) get_option('sitemap-generator-max-fetchers', 3) === $i) { ?>selected<?php } ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
					</select>
				</p>
				<p>Number of the maximal concurrent connections. The default value is three concurrent connections, but some hosters do not allow three concurrent connections or an installed plugin may use that much resources on each request that the limitations of your hosting is reached with three concurrent connections. With this option you can limit the number of concurrent connections used to access your website and make the Sitemap Generator work under these circumstances. You can also increase the number of concurrent connections if your server can handle it.</p>

				<h3>Ignore Embedded Content</h3>
				<p>
					<select name="sitemap-generator-ignore-embedded-content" style="width: 100%;">
						<option <?php if ((int) get_option('sitemap-generator-ignore-embedded-content', 0) === 0) { ?>selected<?php } ?> value="0">No</option>
						<option <?php if ((int) get_option('sitemap-generator-ignore-embedded-content', 0) === 1) { ?>selected<?php } ?> value="1">Yes</option>
					</select>
				</p>
				<p>Do not add embedded content, like for example images, to the sitemap. This option is only useful if you are using a valid token. Without a token, embedded content is never added to  the sitemap.</p>

				<h3>Reference Count Threshold</h3>
				<p>
					<select name="sitemap-generator-reference-count-threshold" style="width: 100%;">
					<option <?php if ((int) get_option('sitemap-generator-reference-count-threshold', -1) === -1) { ?>selected<?php } ?> value="-1">No Threshold</option>
					<?php for ($i = 0; $i <= 100; $i++) { ?>
						<option <?php if ((int) get_option('sitemap-generator-reference-count-threshold', -1) === $i) { ?>selected<?php } ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
					<?php } ?>
					</select>
				</p>
				<p>With the reference count threshold you can define that images and videos that are embedded on more than the selected number of HTML pages are excluded from the sitemap.</p>
				<p>This option is for example useful if you have an image in the header or footer of your website that you do not like to include in the sitemap.</p>
				<p>If no threshold is selected, all images and videos are included in the sitemap, even if they are embedded on all HTML pages. If you set the threshold to 0, no images and videos are included in the sitemap. A value of for example 5 means that all images and videos that are embedded on more than 5 HTML pages are excluded from the sitemap. However, if the excluded image or video is also embedded on the frontpage, it is assigned to the frontpage and included once in the sitemap. Images that are linked (not embedded) are always added to the sitemap, independent of this option.</p>
				<p>The selected value does not affect the URL limit because the images and videos have to be crawled even if they get excluded finally.</p>

				<h3>Query Params To Remove</h3>
				<p>
					<input style="width: 100%;" type="text" name="sitemap-generator-query-params-to-remove" value="<?php echo get_option('sitemap-generator-query-params-to-remove', ''); ?>" />
				</p>
				<p>An ampersend (&amp;) separated list of query parameters that are removed from each URL before the URL is processed. This could be useful if you have for example timestamps in your URLs.</p>
				<p>If you like to remove the query parameters <em>q</em> and <em>timestamp</em> from all URLs, a valid value would for example be:</p>
				<pre>q&amp;timestamp</pre>

				<h3>Disable Cookies</h3>
				<p>
					<select name="sitemap-generator-disable-cookies" style="width: 100%;">
						<option <?php if ((int) get_option('sitemap-generator-disable-cookies', 0) === 0) { ?>selected<?php } ?> value="0">No</option>
						<option <?php if ((int) get_option('sitemap-generator-disable-cookies', 0) === 1) { ?>selected<?php } ?> value="1">Yes</option>
					</select>
				</p>
				<p>The Sitemap Generator crawler has cookie support enabled by default. Thus the cookies that are set by your website are used for all subsequent requests. After each run, all cookies get deleted.</p>
				<p>Normally you like to have cookie support enabled because human visitors usually have cookies enabled and with cookie support, the Sitemap Generator can behave like a human visitor. However, if you have reasons to disable cookie support, you do it with this option.</p>

				<?php submit_button(); ?>
			</form>
		</div>
	</div>
<?php
}
