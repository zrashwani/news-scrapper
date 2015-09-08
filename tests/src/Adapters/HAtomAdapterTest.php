<?php

namespace Zrashwani\NewsScrapper\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Adapters;

class HAtomAdapterTest extends \PHPUnit_Framework_TestCase
{

    public function testExtractTitle() 
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\HAtomAdapter();

        $title = $adapter->extractTitle($crawler);
        $this->assertEquals('Microformats are amazing', $title);
    }

    public function testExtractImage() 
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\HAtomAdapter();
        $adapter->currentUrl = 'http://example.com';

        $image = $adapter->extractImage($crawler);
        $this->assertEquals('http://example.com/hatom.png', $image);
    }

    public function testExtractDescription() 
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\HAtomAdapter();

        $description = $adapter->extractDescription($crawler);
        $this->assertContains('using microformats', $description);
    }

    public function testExtractPublishDate() 
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\HAtomAdapter();

        $publish_date = $adapter->extractPublishDate($crawler);
        $expected_obj = new \DateTime('2013-06-13 12:00:00');
        $expected_date = $expected_obj->format(\DateTime::ISO8601);

        $this->assertEquals($expected_date, $publish_date);
    }

    public function testExtractKeywords() 
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\HAtomAdapter();

        $keywords = $adapter->extractKeywords($crawler);
        $this->assertCount(2, $keywords);
        $this->assertArraySubset(['php','hatom'], $keywords);
    }

    public function testExtractAuthor() 
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\HAtomAdapter();

        $author = $adapter->extractAuthor($crawler);        
        $this->assertEquals('W. Developer', $author);
    }

    public function testExtractBody() 
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\HAtomAdapter();

        $body = $adapter->extractBody($crawler);        
        $this->assertContains('Blah blah blah', $body);
    }

    protected function getHtmlContent($filename = 'hatom.html') 
    {
        return file_get_contents(__DIR__ . '/../../data/' . $filename);
    }

}
