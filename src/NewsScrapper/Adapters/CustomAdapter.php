<?php

namespace Zrashwani\NewsScrapper\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use Zrashwani\NewsScrapper\Selector;

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

    /**
     * adapter used to fill in the missing selectors data by default values
     * @var DefaultAdapter $fallbackAdapter
     */
    private $fallbackAdapter;

    public function __construct()
    {
        $this->fallbackAdapter = new DefaultAdapter();
    }

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
        $ret = $this->getElementText($crawler, $this->authorSelector);
        if (empty($ret) === true) {
            $ret = $this->fallbackAdapter->extractAuthor($crawler);
        }
        return $ret;
    }

    public function extractBody(Crawler $crawler)
    {
        $ret = $this->getElementText($crawler, $this->bodySelector);
        return $this->normalizeHtml($ret);
    }

    public function extractDescription(Crawler $crawler)
    {
        $ret = $this->getElementText($crawler, $this->descriptionSelector);
        if (empty($ret) === true) {
            $ret = $this->fallbackAdapter->extractDescription($crawler);
        }
        return $ret;
    }

    public function extractImage(Crawler $crawler)
    {

        if (empty($this->imageSelector) === false) {
            $ret = $this->getSrcByImgSelector($crawler, $this->imageSelector);
        }
        if (empty($ret) === true) {
            $ret = $this->fallbackAdapter->extractImage($crawler);
        }

        if (empty($ret) === false) {
            return $this->normalizeLink($ret);
        } else {
            return null;
        }
    }

    public function extractKeywords(Crawler $crawler)
    {
        $ret = $this->getElementText($crawler, $this->keywordsSelector);
        if (empty($ret) === true) {
            return $this->fallbackAdapter->extractKeywords($crawler);
        } else {
            return $this->normalizeKeywords(explode(',', $ret));
        }
    }

    public function extractPublishDate(Crawler $crawler)
    {
        $ret = $this->getElementText($crawler, $this->publishDateSelector);
        if (empty($ret) === true) {
            $ret = $this->fallbackAdapter->extractPublishDate($crawler);
        }
        return $ret;
    }

    public function extractTitle(Crawler $crawler)
    {
        $ret = $this->getElementText($crawler, $this->titleSelector);
        if (empty($ret) === true) {
            $ret = $this->fallbackAdapter->extractTitle($crawler);
        }
        return $ret;
    }

    /**
     * getting text of element by selector (css selector or xpath )
     * @param Crawler $crawler
     * @param string $selector
     * @param \Closure $extractClosure callback function to be used for extraction
     * @return string
     */
    protected function getElementText(Crawler $crawler, $selector, $extractClosure = null)
    {

        if (empty($selector) === true) {
            return null;
        }

        $ret = null;
        if ($extractClosure === null) {
            $extractClosure = function (Crawler $node) use (&$ret) {
                $ret = $node->html();
            };
        }
        if (Selector::isCSS($selector)) {
            $crawler->filter($selector)
                    ->each($extractClosure);
        } else {
            $crawler->filterXPath($selector)
                    ->each($extractClosure);
        }

        return $ret;
    }
}
