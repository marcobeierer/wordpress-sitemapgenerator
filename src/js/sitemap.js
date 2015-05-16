'use strict';

var sitemapGeneratorApp = angular.module('sitemapGeneratorApp', []);
var blob;
var language = jQuery('html').attr('lang');

sitemapGeneratorApp.config(['$compileProvider',
	function($compileProvider) {
		$compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|blob):/);
	}
]);

sitemapGeneratorApp.controller('SitemapController', ['$scope', '$http', '$timeout',
	function ($scope, $http, $timeout) {

		$scope.downloadDisabled = true;
		$scope.generateDisabled = false;

		if (language == 'de' || language == 'de-DE') {
			$scope.message = "Die Generierung der Sitemap wurde noch nicht gestartet.";
		} else {
			$scope.message = "The generation of the sitemap was not started yet.";
		}

		$scope.messageClass = "alert-info";
		$scope.generateClass = "button-primary";
		$scope.downloadClass = "buttton-default";

		$scope.generate = function() {

			if ($scope.sitemapForm.$valid) {

				$scope.downloadDisabled = true;
				$scope.generateDisabled = true;
				$scope.pageCount = 0;

				if (language == 'de' || language == 'de-DE') {
					$scope.message = "Die Sitmap wird generiert. Bitte haben Sie einen Moment Geduld.";
				} else {
					$scope.message = "The sitemap is being generated. Please wait a moment.";
				}
				$scope.messageClass = "alert-warning";
				$scope.generateClass = "button-primary";
				$scope.downloadClass = "button-default";
				
				var poller = function() {

					$http.get('admin-ajax.php?action=sitemap_proxy').
						success(function(data, status, headers, config) {

							if (headers('Content-Type') == 'application/xml') {

								blob = new Blob([ data ], { type : 'application/xml' });
								$scope.href = (window.URL || window.webkitURL).createObjectURL( blob );

								$scope.downloadDisabled = false;
								$scope.generateDisabled = false;

								if (language == 'de' || language == 'de-DE') {
									$scope.message = "Ihre Sitemap wurde erfolgreich erstellt und im WordPress-Hauptverzeichnis gespeichert.";
								} else {
									$scope.message = "The generation of the sitemap was successfull. The sitemap was saved as sitemap.xml in the WordPress root folder.";
								}

								$scope.messageClass = "alert-success";
								$scope.generateClass = "button-default";
								$scope.downloadClass = "button-primary";
							}
							else {
								$scope.pageCount = data.page_count;
								$timeout(poller, 1000);
							}
						}).
						error(function(data, status, headers, config) {

							// TODO handle status 401 unauthorized

							$scope.generateDisabled = false;

							if (status == 401) {
								$scope.message = "Im Hauptverzeichnis Ihrer Website wurde die Datei allow-sitemap-generator.html nicht gefunden. Bitte legen Sie diese an und probieren es erneut."; // TODO remove this message
							} else {
								if (language == 'de' || language == 'de-DE') {
									$scope.message = "Ihre Sitemap konnte leider nicht erstellt werden. Bitte probieren Sie es erneut.";
								} else {
									$scope.message = "The creation of your sitemap failed. Please try it again.";
								}
							}
							$scope.messageClass = "alert-danger";
						});
				}
				poller();
			}
		}

		$scope.download = function() {
			if (window.navigator.msSaveOrOpenBlob && blob) { 
				window.navigator.msSaveOrOpenBlob(blob, 'sitemap.xml');
			}
		}
	}
]);
