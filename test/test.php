<?php 

require '../vendor/autoload.php';

ini_set('max_execution_time', 400);
error_reporting(E_ALL);

$scrap_client = new Zrashwani\NewsScrapper\Client('Microdata');

$url = "http://news.investors.com/071815-762206-aapl-apple-earnings-preview-june-quarter-fiscal-q3.htm?ven=yahoocp&src=aurlled&ven=yahoo&ref=yfp";
dump($scrap_client->getLinkData($url));
die();

