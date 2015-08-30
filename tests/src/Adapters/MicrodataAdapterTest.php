<?php
namespace Zrashwani\NewsScrapper\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Adapters;

class MicrodataAdapterTest extends \PHPUnit_Framework_TestCase
{
    
    public function testExtractTitle()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $title = $adapter->extractTitle($crawler);
        $this->assertEquals('Test Headline', $title);
    }
    
    public function testExtractImage()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();        
        
        $image = $adapter->extractImage($crawler);
        $this->assertEquals('testimage.png', $image);
    }
    
    public function testExtractPublishDate()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $publish_date = $adapter->extractPublishDate($crawler);
        $expected_obj = new \DateTime('2015-10-10 20:00:00');
        $expected_date = $expected_obj->format(\DateTime::ISO8601);
        
        $this->assertEquals($expected_date, $publish_date);
        
        $crawler2 = new Crawler($this->getHtmlContent('microdata2.html'));
        $publish_date2 = $adapter->extractPublishDate($crawler2);
        
        $expected_obj2 = new \DateTime('2015-08-10 16:25:00');
        $expected_date2 = $expected_obj2->format(\DateTime::ISO8601);
        
        $this->assertEquals($expected_date2, $publish_date2);
    }
    
    public function testExtractDescription()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $description = $adapter->extractDescription($crawler);        
        $this->assertEquals('test description text', $description);
        
        $crawler2 = new Crawler($this->getHtmlContent('microdata2.html'));
        $description2 = $adapter->extractDescription($crawler2);
        $this->assertStringStartsWith('Description Here', $description2);        
    }
    
    public function testExtractKeywords()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $keywords = $adapter->extractKeywords($crawler);
        $this->assertCount(2, $keywords);
        $this->assertArraySubset(['keyword1'], $keywords);
        
        $crawler2 = new Crawler($this->getHtmlContent('microdata2.html'));
        $keywords2 = $adapter->extractKeywords($crawler2);
        $this->assertArraySubset(['keyword21'], $keywords2);
    }
    
    public function testExtractAuthor()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $author = $adapter->extractAuthor($crawler);        
        $this->assertEquals('Dummy Author', $author);
        
        $crawler2 = new Crawler($this->getHtmlContent('microdata2.html'));
        $author2 = $adapter->extractAuthor($crawler2);
        
        $this->assertEquals('zaid', $author2);
    }
    
    public function testExtractBody()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $body = $adapter->extractBody($crawler);        
        
        $this->assertContains('Testing body', $body, 'body text basic test');
        $this->assertNotContains('alert', $body, 'trimming javascript content');
        $this->assertNotContains('<script', $body, 'trimming javascript tags'); 
        $this->assertNotContains('<div', $body, 'trimming div tags'); 
        
        $crawler2 = new Crawler($this->getHtmlContent('microdata2.html'));
        $body2 = $adapter->extractBody($crawler2);
        
        $this->assertNotEmpty($body2);
        $this->assertContains("Here goes", $body2);
    }
    
    protected function getHtmlContent($filename = 'microdata.html')
    {
        return file_get_contents(__DIR__ . '/../../data/' . $filename);
    }
}
