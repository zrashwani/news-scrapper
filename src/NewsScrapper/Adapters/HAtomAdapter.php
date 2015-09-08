<?php

namespace Zrashwani\NewsScrapper\Adapters;

use \Symfony\Component\DomCrawler\Crawler;

/**
 * Adapter to extract news base on microdata format base on hAtom microformats specifications
 * @link http://microformats.org/wiki/hatom draft microformat specification
 * @author Zeid Rashwani <zrashwani.com>
 */
class HAtomAdapter extends AbstractAdapter
{
    public function extractTitle(Crawler $crawler)
    {
        $ret = null;

        $crawler->filter('.hentry .entry-title')
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->text();
                }
            );


        return $ret;
    }

    public function extractImage(Crawler $crawler)
    {
        $ret = null;

        $crawler->filter('.entry-thumbnail img')
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->attr('src');
                }
            );
        $ret = $this->normalizeLink($ret);
        
        return $ret;
    }

    public function extractDescription(Crawler $crawler)
    {
        $ret = null;

        $crawler->filter('.hentry .entry-summary')
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->text();
                }
            );

        return $ret;
    }

    public function extractKeywords(Crawler $crawler)
    {
        $ret = array();

        $crawler->filter('.hentry a[rel="tag"]')
            ->each(
                function ($node) use (&$ret) {
                        $ret[] = $node->text();
                }
            );

        return $ret;
    }

    public function extractBody(Crawler $crawler)
    {
        $ret = null;
        $crawler->filter(".hentry .entry-content")
            ->each(
                function ($node) use (&$ret) {
                        $ret = $this->normalizeHtml($node->html());
                }
            );

        return $ret;
    }

    public function extractPublishDate(Crawler $crawler)
    {
        $date_str = null;

        $crawler->filter('time.published, .hentry .entry-date')
            ->each(
                function ($node) use (&$date_str) {
                        $date_str = $node->attr('datetime');
                }
            );

        if (!is_null($date_str)) {
            $ret = new \DateTime($date_str);
            return $ret->format(\DateTime::ISO8601);
        }
    }

    public function extractAuthor(Crawler $crawler)
    {
        $ret = null;
        $crawler->filter('.hentry .author.vcard')
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->text();
                }
            );

        return $ret;
    }
}
