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
                function(Crawler $node) use (&$ret) {
                        $ret = $node->attr('content');
                }
            );

        //fallback in case document don't have og:title
        if (empty($ret) === true) {
            $crawler->filterXPath('//h1')
                ->each(
                    function(Crawler $node) use (&$ret) {
                            $ret = $node->text();
                    }
                );
        }
        
        if (empty($ret) === true) {
            $crawler->filterXPath('//head/title')
                ->each(
                    function(Crawler $node) use (&$ret) {
                            $ret = $node->text();
                    }
                );
        }
        
        return $ret;
    }

    /**
     * extract image url from crawler open graph
     * @param Crawler $crawler
     * @return string
     */
    public function extractImage(Crawler $crawler)
    {
        $ret = null;
        $theAdapter = $this;

        $crawler->filterXPath("//head/meta[@property='og:image']")
            ->each(
                function(Crawler $node) use (&$ret) {
                    if($this->getCheckSmallImage($node->attr('content')) === false){ //not small image size
                        $ret = $node->attr('content');
                    }
                }
            );
        
        if (empty($ret) === true) {            
            $crawler->filterXPath('//img')
                ->each(                        
                    function(Crawler $node) use (&$ret, $theAdapter) {                    
                        $img_src = $theAdapter->normalizeLink($node->attr('src'));
                        $width_org = $height_org = 0;
                    
                        $url = pathinfo($img_src);
                        list($width, $height) = getimagesize($url['dirname'].'/'.urlencode($url['basename']));

                        if (empty($ret) === false) {
                            $url_ret = pathinfo($ret);
                            list($width_org, $height_org) = getimagesize(
                                $url_ret['dirname'].
                                '/'.urlencode($url_ret['basename'])
                            );
                        }
                        if ($width > $width_org && $height > $height_org
                            && $width > 200 && $height > 200 //min size of the image amended
                        ) {
                            $ret = $img_src;
                        }
                    }
                );
        }
        
        if (empty($ret) === false) {
            $ret = $this->normalizeLink($ret);
        }
        
        return $ret;
    }

    public function extractDescription(Crawler $crawler)
    {
        $ret = null;

        $crawler->filterXPath("//head/meta[@property='og:description']")
            ->each(
                function(Crawler $node) use (&$ret) {
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
                function(Crawler $node) use (&$ret) {
                
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

        $crawler->filterXPath("//head/meta[@property='article:published_time']")
            ->each(
                function(Crawler $node) use (&$date_str) {
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
        $crawler->filterXPath("//head/meta[@property='article:author']")
            ->each(
                function(Crawler $node) use (&$ret) {
                        $ret = $node->attr('content');
                }
            );
                
        return $ret;
    }
    
    public function getCheckSmallImage($imageUrl){

        $url_ret = pathinfo($imageUrl);
        list($width_org, $height_org) = getimagesize(
            $url_ret['dirname'].'/'.urlencode($url_ret['basename'])
        );

        if($width_org<200 || $height_org < 200){
            return true;
        }else{
            return false;
        }
    }
}
