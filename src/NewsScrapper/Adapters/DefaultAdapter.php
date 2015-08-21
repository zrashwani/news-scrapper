<?php

namespace Zrashwani\NewsScrapper\Adapters;

use \Symfony\Component\DomCrawler\Crawler;

/**
 * Adapter to extract page data according to default html tags
 * @author Zeid Rashwani <zrashwani.com>
 */
class DefaultAdapter extends AbstractAdapter {

    /**
     * extract title information from crawler object
     * @param Crawler $crawler
     * @return string
     */
    public function extractTitle(Crawler $crawler) {
        $ret = null;

        $crawler->filterXPath('//head/title')
                ->each(
                        function ($node) use (&$ret) {
                    $ret = $node->text();
                }
        );

        return $ret;
    }

    /**
     * extract image url from crawler open graph
     * @todo normalize to absolute urls
     * @param Crawler $crawler
     * @return string
     */
    public function extractImage(Crawler $crawler) {
        $ret = null;
        $theAdapter = $this;

        $crawler->filterXPath('//img')
                ->each(function ($node) use (&$ret, $theAdapter) {
                    $img_src = $theAdapter->normalizeLink($node->attr('src')); //TODO: handle
                    $width_org = $height_org = 0;
                    list($width, $height) = getimagesize($img_src);

                    if (empty($ret) === false) {
                        list($width_org, $height_org) = getimagesize($ret);
                    }

                    if ($width > $width_org && $height > $height_org) {
                        $ret = $img_src;
                    }
                }
        );

        return $ret;
    }

    /**
     * extract page description standard meta tags
     * @param Crawler $crawler
     * @return string
     */
    public function extractDescription(Crawler $crawler) {
        $ret = null;

        $crawler->filterXPath("//head/meta[@name='description']")
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
    public function extractKeywords(Crawler $crawler) {
        $ret = array();

        $crawler->filterXPath("//head/meta[@name='keywords']")
                ->each(
                        function ($node) use (&$ret) {
                    $node_txt = trim($node->attr('content'));
                    if (!empty($node_txt)) {
                        $ret = explode(',', $node_txt);
                    }
                }
        );
        $ret = $this->normalizeKeywords($ret);

        return $ret;
    }

    /**
     * extrcting body of page article by selecting <article> tag with longest content
     * @param Crawler $crawler
     * @return string
     */
    public function extractBody(Crawler $crawler) {
        $ret = null;

        $crawler->filterXPath("//article")
                ->each(
                        function ($node) use (&$ret) {

                    $node_txt = $node->text();
                    if (strlen($node_txt) > strlen($ret)) {
                        $ret = $this->normalizeHtml($node->html());
                    }
                }
        );

        return $ret;
    }

    /**
     * extract publish date of page, by examining the first <time> tag in document
     * @param Crawler $crawler
     * @return \DateTime
     */
    public function extractPublishDate(Crawler $crawler) {
        $date_str = null;

        $crawler->filterXPath("//meta[@name='pubdate']") //TODO: revise
                ->each(
                        function ($node) use (&$date_str) {
                    if (empty($date_str) == true) {
                        $date_str = $node->attr('content');
                    }
                }
        );

        try {
            if (!is_null($date_str)) {
                $ret = \DateTime::createFromFormat('Ymd', $date_str);
                $ret->setTime(0, 0, 0);
                return $ret->format(\DateTime::ISO8601);
            }
        } catch (\Exception $ex) {
            error_log('invalid date');
            //TODO: handle invalid date format
        }

        return null;
    }

    /**
     * extracting author information from html metadata
     * @param Crawler $crawler
     * @return string
     */
    public function extractAuthor(Crawler $crawler) {
        $ret = null;
        $crawler->filterXPath("//head/meta[@name='author']")
                ->each(
                        function ($node) use (&$ret) {
                    $ret = $node->attr('content');
                }
        );


        return $ret;
    }

}
