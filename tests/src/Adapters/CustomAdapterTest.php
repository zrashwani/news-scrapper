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
        
        //fallback case...
        $crawler2 = new Crawler($this->getHtmlContent('custom2.html'));
        $adapter2 = new Adapters\CustomAdapter($crawler2);
        $title2 = $adapter2->extractTitle($crawler2);
        $this->assertEquals("Default article title", $title2);
    }

    public function testExtractImage()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();
        
        $adapter->setImageSelector('//img[@id="main_image"]');
        $image = $adapter->extractImage($crawler);
        $this->assertEquals('http://php.net/images/to-top@2x.png', $image);
        
        $adapter->setImageSelector('img#main_image');
        $image2 = $adapter->extractImage($crawler);
        $this->assertEquals('http://php.net/images/to-top@2x.png', $image2);
        
        //fallback case
        $crawler2 = new Crawler($this->getHtmlContent('custom2.html'));
        $adapter2 = new Adapters\CustomAdapter();
        
        $image3 = $adapter2->extractImage($crawler2);
        $this->assertEquals('https://s.ytimg.com/yts/imgbin/www-hitchhiker-vflXXbNO2.png', $image3);
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

        $adapter->setPublishDateSelector('datetime.published_at');
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
        
        //fallback case...
        $crawler2 = new Crawler($this->getHtmlContent('custom2.html'));
        $adapter2 = new Adapters\CustomAdapter($crawler2);
        $keywords2 = $adapter2->extractKeywords($crawler2);
        
        $this->assertCount(2, $keywords2);
        $this->assertArraySubset(['meta_key1', 'meta_key2'], $keywords2);
    }

    public function testExtractAuthor()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();

        $adapter->setAuthorSelector('//div[@class="written_by"]');
        $author = $adapter->extractAuthor($crawler);
        $this->assertEquals('Zaid Rashwani', $author);
        
        $adapter->setAuthorSelector('.written_by');
        $author2 = $adapter->extractAuthor($crawler);
        $this->assertEquals('Zaid Rashwani', $author2);
        
        //fallback case...
        $crawler2 = new Crawler($this->getHtmlContent('custom2.html'));
        $adapter2 = new Adapters\CustomAdapter($crawler2);
        $author3 = $adapter2->extractAuthor($crawler2);
        $this->assertEquals('meta author', $author3);
    }

    public function testExtractBody()
    {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\CustomAdapter();
        
        $adapter->setBodySelector('//div[@class="article_content"]');
        $body = $adapter->extractBody($crawler);
        $this->assertContains('custom content', $body);
        $this->assertNotContains('side block', $body);
        
        $adapter->setBodySelector('.article_inner_content');
        $body2 = $adapter->extractBody($crawler);
        $this->assertContains('inner content', $body2);
        $this->assertNotContains('custom content', $body2);
    }
    
 
    protected function getHtmlContent($filename = 'custom.html')
    {
        return file_get_contents(__DIR__ . '/../../data/' . $filename);
    }
}
