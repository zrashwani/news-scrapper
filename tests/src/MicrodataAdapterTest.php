<?php
namespace Zrashwani\NewsScrapper;

use Symfony\Component\DomCrawler\Crawler;

class MicrodataAdapterTest extends \PHPUnit_Framework_TestCase{
    
    public function testExtractTitle(){
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $title = $adapter->extractTitle($crawler);
        $this->assertEquals('Test Headline', $title);
    }
    
    public function testExtractImage(){
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();        
        
        $image = $adapter->extractImage($crawler);
        $this->assertEquals('testimage.png', $image);
    }
    
    public function testExtractPublishDate(){
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $publish_date = $adapter->extractPublishDate($crawler);
        $expected_obj = new \DateTime('2015-10-10 20:00:00');
        $expected_date = $expected_obj->format(\DateTime::ISO8601);
        
        $this->assertEquals($expected_date, $publish_date);
    }
    
    public function testExtractDescription(){
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $description = $adapter->extractDescription($crawler);        
        $this->assertEquals('test description text', $description);
    }
    
    public function testExtractKeywords(){
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $keywords = $adapter->extractKeywords($crawler);
        $this->assertCount(2, $keywords);
        $this->assertArraySubset(['keyword1'], $keywords);
    }
    
    public function testExtractAuthor(){
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $author = $adapter->extractAuthor($crawler);        
        $this->assertEquals('Dummy Author', $author);
    }
    
    public function testExtractBody(){
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\MicrodataAdapter();
        
        $body = $adapter->extractBody($crawler);        
        
        $this->assertTrue(strpos($body, 'Testing body') !== false, 'body text basic test');
        $this->assertTrue(strpos($body, 'alert') === false, 'trimming javascript content');
        $this->assertTrue(strpos($body, '<script') === false, 'trimming javascript tags');        
    }
    
    protected function getHtmlContent($filename = 'microdata.html'){
        return file_get_contents(__DIR__.'/../data/'.$filename);
    }
}
