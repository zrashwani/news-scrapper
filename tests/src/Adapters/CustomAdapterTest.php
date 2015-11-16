<?php

namespace Zrashwani\NewsScrapper\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Adapters;

class CustomAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractTitle()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();
        
        $adapter->setTitleSelector("//div[@class='article_title']");
        $title = $adapter->extractTitle($crawler);
        
        $this->assertEquals('Article Title', $title);
    }

    public function testExtractImage()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();
        
        $adapter->setImageSelector('//img[@id="main_image"]');
        $image = $adapter->extractImage($crawler);
        $this->assertEquals('http://www.example.com/image_path.png', $image);
        
        $adapter->setImageSelector(null);
        $image2 = $adapter->extractImage($crawler);
        $this->assertNull($image2);
    }

    public function testExtractDescription()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();
        $adapter->setDescriptionSelector("");

        $description = $adapter->extractDescription($crawler);
        $this->assertNull($description);
    }

    public function testExtractPublishDate()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();

        $publish_date = $adapter->extractPublishDate($crawler);

        $this->assertNull($publish_date);
    }

    public function testExtractKeywords()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();

        $adapter->setKeywordsSelector('//div[@class="tags"]');
        $keywords = $adapter->extractKeywords($crawler);
        
        $this->assertCount(3, $keywords);
        $this->assertArraySubset(['tag1','tag2'], $keywords);
    }

    public function testExtractAuthor()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();

        $adapter->setAuthorSelector('//div[@class="written_by"]');
        $author = $adapter->extractAuthor($crawler);
        
        $this->assertEquals('Zaid Rashwani', $author);
    }

    public function testExtractBody()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();
        $adapter->setBodySelector('//div[@class="article_content"]');

        $body = $adapter->extractBody($crawler);
        $this->assertContains('custom content', $body);
        
        $this->assertNotContains('side block', $body);
    }
    
 
    protected function getHtmlContent($filename = 'custom.html')
    {
        return file_get_contents(__DIR__ . '/../../data/' . $filename);
    }
}
