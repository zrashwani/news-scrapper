<?php

namespace Zrashwani\NewsScrapper;

class SelectorTest extends \PHPUnit_Framework_TestCase
{
    public function testIsCss()
    {
        $this->assertTrue(Selector::isCSS('.img'));
        $this->assertFalse(Selector::isCSS('//div[@id="main_image"]'));
    }
    
    public function testIsXpath()
    {
        $this->assertTrue(Selector::isXPath('//div[@id="main_image"]'));
        $this->assertFalse(Selector::isXPath('.img'));
    }
}
