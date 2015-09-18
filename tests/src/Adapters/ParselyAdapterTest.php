<?php
namespace Zrashwani\NewsScrapper\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Adapters;

class ParslyAdapterTest extends \PHPUnit_Framework_TestCase
{
    
    public function testExtractTitle()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\ParselyAdapter();
        
        $title = $adapter->extractTitle($crawler);
        $this->assertEquals("Zipf's Law of the Internet: Explaining Online Behavior", $title);
    }
    
    public function testExtractImage()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\ParselyAdapter();
        
        $image = $adapter->extractImage($crawler);
        $this->assertEquals('http://blog.parsely.com/inline_mra670hTvL1qz4rgp.png', $image);
    }
    
    public function testExtractPublishDate()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\ParselyAdapter();
        
        $publish_date = $adapter->extractPublishDate($crawler);
        $expected_obj = new \DateTime('2013-08-15T13:00:00Z');
        $expected_date = $expected_obj->format(\DateTime::ISO8601);
        
        $this->assertEquals($expected_date, $publish_date);
        
        //$crawler2 = new Crawler($this->getHtmlContent('parsely2.html'));
        //$publish_date2 = $adapter->extractPublishDate($crawler2);
        
        //$expected_obj2 = new \DateTime('2015-08-10 16:25:00');
        //$expected_date2 = $expected_obj2->format(\DateTime::ISO8601);
        
        //$this->assertEquals($expected_date2, $publish_date2);
    }
    
    public function testExtractDescription()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\ParselyAdapter();
        
        $description = $adapter->extractDescription($crawler);
        $this->assertEmpty($description);
        
    }
    
    public function testExtractKeywords()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\ParselyAdapter();
        
        $keywords = $adapter->extractKeywords($crawler);
        $this->assertCount(4, $keywords);
        $this->assertArraySubset(['statistics','zipf'], $keywords);
        
        //$crawler2 = new Crawler($this->getHtmlContent('parsely2.html'));
        //$keywords2 = $adapter->extractKeywords($crawler2);
        //$this->assertArraySubset(['keyword21'], $keywords2);
    }
    
    public function testExtractAuthor()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\ParselyAdapter();
        
        $author = $adapter->extractAuthor($crawler);
        $this->assertEquals('Alan Alexander Milne', $author);
        
        //$crawler2 = new Crawler($this->getHtmlContent('parsely2.html'));
        //$author2 = $adapter->extractAuthor($crawler2);
        
        //$this->assertEquals('zaid', $author2);
    }
    
    public function testExtractBody()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\ParselyAdapter();
        
        $body = $adapter->extractBody($crawler);
        $this->assertEmpty($body);
    }
    
    protected function getHtmlContent($filename = 'parsely.html')
    {
        return file_get_contents(__DIR__ . '/../../data/' . $filename);
    }
}
