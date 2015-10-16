<?php

namespace Zrashwani\NewsScrapper\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Adapters;

class DefaultAdapterTest extends \PHPUnit_Framework_TestCase
{

    public function testExtractTitle()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $title = $adapter->extractTitle($crawler);
        $this->assertEquals('Default title', $title);
    }

    public function testExtractImage()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();
        
        $image = $adapter->extractImage($crawler);
        $this->assertEquals('http://www.google.com/images/srpr/logo11w.png', $image);
    }

    public function testExtractDescription()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $description = $adapter->extractDescription($crawler);
        $this->assertContains('default description', $description);
    }

    public function testExtractPublishDate()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $publish_date = $adapter->extractPublishDate($crawler);
        $expected_obj = new \DateTime('2010-01-01');
        $expected_date = $expected_obj->format(\DateTime::ISO8601);

        $this->assertEquals($expected_date, $publish_date);
    }

    public function testExtractKeywords()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $keywords = $adapter->extractKeywords($crawler);
        $this->assertCount(3, $keywords);
        $this->assertArraySubset(['php','default'], $keywords);
    }

    public function testExtractAuthor()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $author = $adapter->extractAuthor($crawler);
        $this->assertEquals('Mr. HTML', $author);
    }

    public function testExtractBody()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $body = $adapter->extractBody($crawler);
        $this->assertContains('article body here', $body);
        
        $this->assertNotContains('side block', $body);
    }
    
    public function testNormalizeLink()
    {
        $adapter = new Adapters\DefaultAdapter();
        $adapter->currentUrl = 'http://example.com/subfolder/';
        
        //relative url is normalized to absolute one
        $url1 = $adapter->normalizeLink("/another-url");
        $this->assertEquals('http://example.com/another-url', $url1);
        
        //this url remains not changed
        $url2 = $adapter->normalizeLink("http://example2.com/whatever");
        $this->assertEquals("http://example2.com/whatever", $url2);
        
        $url3 = $adapter->normalizeLink('in-sub');
        $this->assertEquals('http://example.com/in-sub', $url3);
        
        $adapter->currentUrl = 'https://securedurl.com/';
        $url4 = $adapter->normalizeLink("//example3.com");
        $this->assertEquals('https://example3.com', $url4);
        
        $adapter->currentUrl = 'http://example5.com';
        $url5 = $adapter->normalizeLink("img.png");
        $this->assertEquals('http://example5.com/img.png', $url5);
        
        $adapter->currentUrl = __DIR__ . '/../../data/jsonld2.html';
        $url6 = $adapter->normalizeLink('jsonld.js');
        $this->assertEquals(__DIR__ . '/../../data/jsonld.js', $url6);
    }
    
    public function testNormalizeBodyLinks()
    {
        $adapter = new Adapters\DefaultAdapter();
        $adapter->currentUrl = 'http://example.com';
        
        $html = $this->getHtmlContent();
        $html_normalized = $adapter->normalizeBodyLinks($html);
        
        $this->assertContains("http://example.com/relative-url", $html_normalized);
        $this->assertContains("http://example.com/another-sub/url", $html_normalized);
        
        $html_normalized2 = $adapter->normalizeBodyLinks('');
        $this->assertEmpty($html_normalized2);
    }

    protected function getHtmlContent($filename = 'default.html')
    {
        return file_get_contents(__DIR__ . '/../../data/' . $filename);
    }
}
