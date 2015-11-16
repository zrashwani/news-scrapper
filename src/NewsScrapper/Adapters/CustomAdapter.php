<?php

namespace Zrashwani\NewsScrapper\Adapters;

use \Symfony\Component\DomCrawler\Crawler;

/**
 * Adapter to extract page data from un-structured HTML document
 * @author Zeid Rashwani <zrashwani.com>
 */
class CustomAdapter extends AbstractAdapter
{

    private $authorSelector;
    private $bodySelector;
    private $descriptionSelector;
    private $imageSelector;
    private $keywordsSelector;
    private $publishDateSelector;
    private $titleSelector;
    
    public function setAuthorSelector($selector)
    {
        $this->authorSelector = $selector;
        return $this;
    }
    
    public function setBodySelector($selector)
    {
        $this->bodySelector = $selector;
        return $this;
    }

    public function setDescriptionSelector($selector)
    {
        $this->descriptionSelector = $selector;
        return $this;
    }

    public function setImageSelector($selector)
    {
        $this->imageSelector = $selector;
        return $this;
    }

    public function setKeywordsSelector($selector)
    {
        $this->keywordsSelector = $selector;
        return $this;
    }

    public function setPublishDateSelector($selector)
    {
        $this->publishDateSelector = $selector;
        return $this;
    }
    
    public function setTitleSelector($selector)
    {
        $this->titleSelector = $selector;
        return $this;
    }
    
    public function extractAuthor(Crawler $crawler)
    {
        return $this->getElementText($crawler, $this->authorSelector);
    }

    public function extractBody(Crawler $crawler)
    {
        return $this->getElementText($crawler, $this->bodySelector);
    }

    public function extractDescription(Crawler $crawler)
    {
        return $this->getElementText($crawler, $this->descriptionSelector);
    }

    public function extractImage(Crawler $crawler)
    {
        $this->currentUrl = "http://example.com/";
        if (empty($this->imageSelector) === true) {
            return null;
        }
        
        $ret = null;
        $crawler->filterXPath($this->imageSelector)
            ->each(
                function (Crawler $node) use (&$ret) {
                    $ret = $node->attr('src');
                }
            );
        return $this->normalizeLink($ret);
    }

    public function extractKeywords(Crawler $crawler)
    {
        $ret =  $this->getElementText($crawler, $this->keywordsSelector);
        return $this->normalizeKeywords(explode(',', $ret));
    }

    public function extractPublishDate(Crawler $crawler)
    {
        return $this->getElementText($crawler, $this->publishDateSelector);
    }

    public function extractTitle(Crawler $crawler)
    {
        return $this->getElementText($crawler, $this->titleSelector);
    }

    protected function getElementText(Crawler $crawler, $selector)
    {
        
        if (empty($selector) === true) {
            return null;
        }
        
        $ret = null;
        $crawler->filterXPath($selector)
            ->each(
                function (Crawler $node) use (&$ret) {
                    $ret = $node->text();
                }
            );

        return $ret;
    }
}
