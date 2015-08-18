<?php

namespace Zrashwani\NewsScrapper\Adapters;

use \Symfony\Component\DomCrawler\Crawler;

/**
 * Adapter to extract news base on open graph protocol specifications
 * @link http://ogp.me/ open graph meta data specifications
 * @author Zeid Rashwani <zrashwani.com>
 */
class OpenGraphAdapter extends AbstractAdapter
{
    /**
     * extract title information from crawler object
     * @param Crawler $crawler
     * @return string
     */
    public function extractTitle(Crawler $crawler)
    {
        $ret = null;

        $crawler->filterXPath("//head/meta[@property='og:title']")
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->attr('content');
                }
            );

        //fallback in case document don't have og:title
        if (empty($ret) === true) {
            $crawler->filterXPath('//h1')
                ->each(
                    function ($node) use (&$ret) {
                            $ret = $node->text();
                    }
                );
        }
        
        if (empty($ret) === true) {
            $crawler->filterXPath('//head/title')
                ->each(
                    function ($node) use (&$ret) {
                            $ret = $node->text();
                    }
                );
        }
        
        return $ret;
    }

    /**
     * extract image url from crawler open graph
     * @todo check if image has good dimensions
     * @param Crawler $crawler
     * @return string
     */
    public function extractImage(Crawler $crawler)
    {
        $ret = null;

        $crawler->filterXPath("//head/meta[@property='og:image']")
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->attr('content');
                }
            );
        
        if (empty($ret) === true) { //todo: add image size restriction and priorities
            $crawler->filterXPath('//img')
                ->each(
                    function ($node) use (&$ret) {
                        if (empty($ret) === true) {
                            $ret = $node->attr('src');
                        }
                    }
                );
        }

        return $ret;
    }

    public function extractDescription(Crawler $crawler)
    {
        $ret = null;

        $crawler->filterXPath("//head/meta[@property='og:description']")
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->attr('content');
                }
            );

        return $ret;
    }

    /**
     * extract keywords out of crawler object
     * @param Crawler $crawler
     * @return array
     */
    public function extractKeywords(Crawler $crawler)
    {
        $ret = array();

        $crawler->filterXPath("//head/meta[@property='og:keywords']")
            ->each(
                function ($node) use (&$ret) {
                
                        $node_txt = trim($node->attr('content'));                        
                    if (!empty($node_txt)) {
                        $ret = explode(',', $node_txt);
                        
                    }
                }
            );

        return $ret;
    }

    public function extractBody(Crawler $crawler)
    {
        //No body can be extracted from open graph protocol
        return null;
    }

    public function extractPublishDate(Crawler $crawler)
    {
        $date_str = null;

        $crawler->filterXPath("//head/meta[@property='og:article:published_time']")
            ->each(
                function ($node) use (&$date_str) {
                        $date_str = $node->attr('content');
                }
            );
            
        if (!is_null($date_str)) {
            $ret = new \DateTime($date_str);
            return $ret->format(\DateTime::ISO8601);
        } else {
            return null;
        }
    }

    public function extractAuthor(Crawler $crawler)
    {
        $ret = null;
        $crawler->filterXPath("//head/meta[@property='og:article:author']")
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->attr('content');
                }
            );
                
        return $ret;
    }
}
