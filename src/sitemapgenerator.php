<?php
/*
 * @package    SitemapGenerator
 * @copyright  Copyright (C) 2015 Marco Beierer. All rights reserved.
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU/AGPL
 */

/*
Plugin Name: Sitemap Generator
Plugin URI: https://www.marcobeierer.com/tools/sitemap-generator#wordpress
Description: A easy to use Sitemap Generator for WordPress
Version: 1.0.0-beta.1
Author: Marco Beierer
Author URI: https://www.marcobeierer.com
License: AGPL
Text Domain: Marco Beierer
*/

add_action('admin_menu', 'register_sitemap_generator_page');

function register_sitemap_generator_page(){
	add_menu_page( 'Generate Sitemap', 'Generate Sitemap', 'manage_options', 'sitemap-generator', 'sitemap_generator_page', '', 99); 
}

function sitemap_generator_page(){

	$PLG_AJAX_SITEMAPGENERATOR_SUCCESS="The generation of the sitemap was successfull. The sitemap was saved as sitemap.xml in the WordPress root folder.";
	$PLG_AJAX_SITEMAPGENERATOR_ERROR="An error occurred. Please try it again or contact the developer of the extension.";
	$PLG_AJAX_SITEMAPGENERATOR_ERROR_NOT_AUTHORISED="You are not authorised to generate a sitemap.";

	$baseurl = get_site_url();
	$baseurl64 = strtr(base64_encode($baseurl), '+/', '-_');

	$subsequentRequest = false; // TODO implement a nicer solution
	do {
		if ($subsequentRequest) {
			usleep(250000); // 250ms
		} else {
			$subsequentRequest = true;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://api.marcobeierer.com/sitemap/v2/' . $baseurl64 . '?pdfs=1&wordpress=1');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);

		$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

		curl_close($ch);
	}
	while($statusCode == 200 && $contentType != 'application/xml');

	if ($statusCode != 200) {
		echo $PLG_AJAX_SITEMAPGENERATOR_ERROR; // TODO better error message
		return;
	}

	$reader = new XMLReader();
	$reader->xml($response, 'UTF-8');
	$reader->setParserProperty(XMLReader::VALIDATE, true);

	if ($reader->isValid()) { // TODO check if empty?

		$rootPath = get_home_path();
		if ($rootPath != '') {
			file_put_contents($rootPath . DIRECTORY_SEPARATOR . 'sitemap.xml', $response); // TODO handle error
			echo $PLG_AJAX_SITEMAPGENERATOR_SUCCESS;
			return;
		}
	}

	echo $PLG_AJAX_SITEMAPGENERATOR_ERROR; // TODO better error message
	return;
}
