<?php
namespace Zrashwani\NewsScrapper\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Adapters;

class JsonLDAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractTitle()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\JsonLDAdapter();
        
        $title = $adapter->extractTitle($crawler);
        $this->assertEquals("test jsonld article", $title);
    }
    
    public function testExtractImage()
    {
        $adapter = new Adapters\JsonLDAdapter();
     
        $crawler = new Crawler($this->getHtmlContent());
        $image = $adapter->extractImage($crawler);
        $this->assertEquals('http://jsonld-example.com/main.png', $image);
        
        $crawler2 = new Crawler($this->getHtmlContent('jsonld2.html'));
        $adapter->currentUrl = __DIR__ . '/../../data/jsonld2.html'; //TODO: amend test later
        $image2 = $adapter->extractImage($crawler2);
        $this->assertEquals('http://example2.com/blog/main.png', $image2);
    }
    
    public function testExtractPublishDate()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\JsonLDAdapter();
        
        $publish_date = $adapter->extractPublishDate($crawler);
        $expected_obj = new \DateTime('2015-08-20T13:00:00Z');
        $expected_date = $expected_obj->format(\DateTime::ISO8601);
        
        $this->assertEquals($expected_date, $publish_date);
        
        $crawler2 = new Crawler($this->getHtmlContent('jsonld2.html'));
        $adapter->currentUrl = __DIR__ . '/../../data/jsonld2.html'; //TODO: amend test later
        $publish_date2 = $adapter->extractPublishDate($crawler2);
        
        $expected_obj2 = new \DateTime('2015-09-15T13:00:00Z');
        $expected_date2 = $expected_obj2->format(\DateTime::ISO8601);
        
        $this->assertEquals($expected_date2, $publish_date2);
    }
    
    public function testExtractDescription()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\JsonLDAdapter();
        
        $description = $adapter->extractDescription($crawler);
        $this->assertEquals('summary of the article', $description);
        
    }
    
    public function testExtractKeywords()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\JsonLDAdapter();
        
        $keywords = $adapter->extractKeywords($crawler);
        $this->assertCount(4, $keywords);
        $this->assertArraySubset(["metadata","scraping"], $keywords);
        
        $crawler2 = new Crawler($this->getHtmlContent('jsonld2.html'));
        $adapter->currentUrl = __DIR__ . '/../../data/jsonld2.html'; //TODO: amend test later
        $keywords2 = $adapter->extractKeywords($crawler2);
        $this->assertEquals(['jsonld2'], $keywords2);
    }
    
    public function testExtractAuthor()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\JsonLDAdapter();
        
        $author = $adapter->extractAuthor($crawler);
        $this->assertEquals('zaid jsonld', $author);
        
        $crawler2 = new Crawler($this->getHtmlContent('jsonld2.html'));
        $adapter->currentUrl = __DIR__ . '/../../data/jsonld2.html'; //TODO: amend test later
        $author2 = $adapter->extractAuthor($crawler2);
        $this->assertEquals('Some creator', $author2);
    }
    
    public function testExtractBody()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\JsonLDAdapter();
        
        $body = $adapter->extractBody($crawler);
        $this->assertEmpty($body);
    }
    
    protected function getHtmlContent($filename = 'jsonld.html')
    {
        return file_get_contents(__DIR__ . '/../../data/' . $filename);
    }
}
