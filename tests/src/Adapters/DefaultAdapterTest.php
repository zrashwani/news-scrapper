<?php

namespace Zrashwani\NewsScrapper\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Adapters;

class DefaultAdapterTest extends \PHPUnit_Framework_TestCase {

    public function testExtractTitle() {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $title = $adapter->extractTitle($crawler);
        $this->assertEquals('Default title', $title);
    }

    public function testExtractImage() {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $image = $adapter->extractImage($crawler);
        $this->assertEquals('default.png', $image);
    }

    public function testExtractDescription() {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $description = $adapter->extractDescription($crawler);
        $this->assertContains('default description', $description);
    }

    public function testExtractPublishDate() {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $publish_date = $adapter->extractPublishDate($crawler);
        $expected_obj = new \DateTime('2010-01-01');
        $expected_date = $expected_obj->format(\DateTime::ISO8601);

        $this->assertEquals($expected_date, $publish_date);
    }

    public function testExtractKeywords() {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $keywords = $adapter->extractKeywords($crawler);
        $this->assertCount(3, $keywords);
        $this->assertArraySubset(['php','default'], $keywords);
    }

    public function testExtractAuthor() {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $author = $adapter->extractAuthor($crawler);        
        $this->assertEquals('Mr. HTML', $author);
    }

    public function testExtractBody() {
        $crawler = new Crawler($this->getHtmlContent());
        $adapter = new Adapters\DefaultAdapter();

        $body = $adapter->extractBody($crawler);        
        $this->assertContains('article body here', $body);
    }

    protected function getHtmlContent($filename = 'default.html') {
        return file_get_contents(__DIR__ . '/../../data/' . $filename);
    }

}
