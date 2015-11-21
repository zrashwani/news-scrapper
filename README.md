# News Scrapper
This library extract article/news information  from a webpage including:
title, main image, description, author, keywords, publish date and body (if possible)...

This library supports scrapping using standard structured meta data, like:
[Microdata][schemaorgspec], [hAtom Microformat][hatomspec] ..etc,  along with custom selectors that can be specified to support unstructured webpages.

News-Scrapper requires PHP >= 5.4

[![Build Status](https://travis-ci.org/zrashwani/news-scrapper.svg?branch=master)](https://travis-ci.org/zrashwani/news-scrapper)
[![Code Climate](https://codeclimate.com/repos/55fc7240e30ba0202900a918/badges/b41e6756dff9d9c0e01b/gpa.svg)](https://codeclimate.com/repos/55fc7240e30ba0202900a918/feed)
[![codecov.io](http://codecov.io/github/zrashwani/news-scrapper/coverage.svg?branch=master)](http://codecov.io/github/zrashwani/news-scrapper?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/89dcd1ed-b9e4-4e56-8db7-aef687e8d89a/mini.png)](https://insight.sensiolabs.com/projects/89dcd1ed-b9e4-4e56-8db7-aef687e8d89a)

## How to Install
You can install this library with [Composer][composer]. Drop this into your `composer.json`
manifest file:

    {
        "require": {
            "zrashwani/news-scrapper": "dev-master"
        }
    }
	
Then run `composer install`.

## How to Use

Here's a quick how to scrap news data from a webpage:	

```<?php
    require 'vendor/autoload.php';

    // Initiate scrapper
    $scrap_client = new \Zrashwani\NewsScrapper\Client();    
	print_r($scrap_client->getLinkData($url));
```
By default, scrapper tries to guess the best structured data adapter and apply it.

### Scrapping Structured data	
-------------------------------------------
You can select a specific adapter to be used for extracting the data as following:

```php   
    $url = "http://example.com/your-news-uri";
    //use microdata standard for scrapping
    $scrap_client = new \Zrashwani\NewsScrapper\Client('Microdata'); 
    print_r($scrap_client->getLinkData($url));	
```
	
Here is the list of supported structured data adapters or scrapping modes:
* [Microdata][schemaorgspec]
* [HAtom][hatomspec]
* [OpenGraph][ogspec]
* [JsonLD][jsonld]
* [Parsely][parsely]
* Default

### Scrapping Unstructured data
-------------------------------------------
If the webpage doesn't follow any standard structured data, you can still scrap news information by specifying xpath or css selector for different article parts like: title, description, image and body. as following:
```php
$scrapClient = new \Zrashwani\NewsScrapper\Client('Custom');

/*@var $adapter \Zrashwani\NewsScrapper\Adapters\CustomAdapter */
$adapter = $scrapClient->getAdapter();
$adapter        
        ->setTitleSelector('.single-post h1') //selectors can be either css or xpath
        ->setImageSelector(".sidebar img")
        ->setAuthorSelector('//a[@rel="author"]')
        ->setPublishDateSelector('//span[@class="published_data"]')
        ->setBodySelector('//div[@class="contents"]');        

$newsData = ($scrapClient->getLinkData("http://example.com/your-news-uri"));
print_r($newsData);
```
Custom scrapping adapter `CustomAdapter` supports method chaining for setting the selectors.
If any selector is not specified it will use default selectors based on `DefaultAdapter` (which is html adapter that depends of standard meta tags).

### Scrapping Group of Links
-------------------------------------------
To scrap group of news article from certain page containing news links, `scrapLinkGroup` method can be used

```php
$listingPageUrl = 'https://www.readability.com/topreads/'; //url containing news listing
$linksSelector = '.entry-title a'; //css or xpath selector for news links inside listing page
$numberOfArticles = 3; //number of links to scrap, use null to get all matching selector

$scrapClient = new \Zrashwani\NewsScrapper\Client();
$newsGroupData = $scrapClient->scrapLinkGroup($listingPageUrl, $linksSelector,$numberOfArticles);                
foreach($newsGroupData as $singleNews){
    print_r($singleNews);
}
```
## How to Contribute

1. Fork this repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch

It is very important to separate new features or improvements into separate feature branches,
and to send a pull request for each branch. This allows me to review and pull in new features
or improvements individually.

All pull requests must adhere to the [PSR-2 standard][psr2].

## System Requirements

* PHP 5.4.0+


## License

MIT Public License

[schemaorgspec]: http://schema.org/Article
[psr2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[hatomspec]: http://microformats.org/wiki/hatom
[ogspec]: http://ogp.me/
[htmlmetaspec]: http://www.w3.org/TR/html5/document-metadata.html#standard-metadata-names
[composer]: http://getcomposer.org/
[jsonld]: http://json-ld.org/
[parsely]: https://www.parsely.com/docs/integration/metadata/ppage.html
