<?php

namespace Zrashwani\NewsScrapper\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Adapters;

class OpenGraphAdapterTest extends \PHPUnit_Framework_TestCase
{

    public function testExtractTitle()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\OpenGraphAdapter();

        $title = $adapter->extractTitle($crawler);
        $this->assertEquals('OG Title', $title);
        
        $crawler2 = new Crawler($this->getHtmlContent('opengraph2.html'));
        $title2 = $adapter->extractTitle($crawler2);
        
        $this->assertEquals('OG Fallback Title', $title2);
    }

    public function testExtractImage()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\OpenGraphAdapter();

        $image = $adapter->extractImage($crawler);
        $this->assertEquals('http://ogtest.com/og.jpg', $image);
        
        $crawler2 = new Crawler($this->getHtmlContent('opengraph2.html'));
        $adapter->currentUrl = 'https://github.com';
        
        $image2 = $adapter->extractImage($crawler2);
        
        $this->assertEquals('https://github.com/logo.png', $image2); //emulating real image path
    }

    public function testExtractDescription()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\OpenGraphAdapter();

        $description = $adapter->extractDescription($crawler);
        $this->assertContains('description of og testing', $description);
    }

    public function testExtractPublishDate()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\OpenGraphAdapter();

        $publish_date = $adapter->extractPublishDate($crawler);
        $expected_obj = new \DateTime('2015-8-20 00:00:00');
        $expected_date = $expected_obj->format(\DateTime::ISO8601);

        $this->assertEquals($expected_date, $publish_date);
        
        $crawler2 = new Crawler($this->getHtmlContent('opengraph2.html'));
        $publish_date2 = $adapter->extractPublishDate($crawler2);
        
        $this->assertNull($publish_date2);
    }

    public function testExtractKeywords()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\OpenGraphAdapter();

        $keywords = $adapter->extractKeywords($crawler);
        $this->assertCount(4, $keywords);
        $this->assertArraySubset(['php'], $keywords); //TODO: revise spaces
    }

    public function testExtractAuthor()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\OpenGraphAdapter();

        $author = $adapter->extractAuthor($crawler);
        $this->assertEquals('OG Author', $author);
        
        $crawler2 = new Crawler($this->getHtmlContent('opengraph2.html'));
        $author2 = $adapter->extractAuthor($crawler2);
        
        $this->assertNull($author2);
    }

    public function testExtractBody()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\OpenGraphAdapter();

        $body = $adapter->extractBody($crawler);
        $this->assertNull($body);
    }

    protected function getHtmlContent($filename = 'opengraph.html')
    {
        return file_get_contents(__DIR__ . '/../../data/' . $filename);
    }
}
