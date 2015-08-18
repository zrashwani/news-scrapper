<?php

namespace Zrashwani\NewsScrapper;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Adapters;

class ClientTest extends \PHPUnit_Framework_TestCase {
    
    public function testConstructor(){
        $client = new Client();
        $this->assertInstanceOf(Client::class, $client);
    }
    
    public function testGetAdapter(){
        $client = new Client('Microdata');
        $adapter = $client->getAdapter();
        
        $this->assertInstanceOf(Adapters\MicrodataAdapter::class, $adapter);
    }
    
    public function testSetAdapter(){
        $client = new Client();
        
        $ret = $client->setAdapter('HAtom');
        $this->assertInstanceOf(Client::class, $ret);
        $this->assertInstanceOf($client->getAdapter(), Adapters\HAtomAdapter::class);
    }
}