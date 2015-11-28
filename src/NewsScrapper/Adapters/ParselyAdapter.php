<?php

namespace Zrashwani\NewsScrapper\Adapters;

use \Symfony\Component\DomCrawler\Crawler;

/**
 * Adapter to extract news base on parse.ly metadata specification specifications
 * @link https://www.parsely.com/docs/integration/metadata/jsonld.html parse.ly json-ld documentation
 * @link https://www.parsely.com/docs/integration/metadata/metatags.html parse.ly repeated meta tags documentation
 * @author Zeid Rashwani <zrashwani.com>
 */
class ParselyAdapter extends AbstractAdapter
{
    public function extractTitle(Crawler $crawler)
    {
        $ret = null;

        $crawler->filterXPath('//meta[@name="parsely-title"]')
            ->each(
                function(Crawler $node) use (&$ret) {
                        $ret = $node->attr('content');
                }
            );


        return $ret;
    }

    public function extractImage(Crawler $crawler)
    {
        $ret = null;

        $crawler->filterXPath('//meta[@name="parsely-image-url"]')
            ->each(
                function(Crawler $node) use (&$ret) {
                        $ret = $node->attr('content');
                }
            );
        if (empty($ret) === false) {
            $ret = $this->normalizeLink($ret);
        }
        
        return $ret;
    }

    public function extractDescription(Crawler $crawler)
    {
        return;
    }

    public function extractKeywords(Crawler $crawler)
    {
        $ret = array();

        $crawler->filterXPath('//meta[@name="parsely-tags"]')
            ->each(
                function(Crawler $node) use (&$ret) {
                        $ret = explode(',', $node->attr('content'));
                }
            );

        return $ret;
    }

    public function extractBody(Crawler $crawler)
    {
        return;
    }

    public function extractPublishDate(Crawler $crawler)
    {
        $date_str = null;

        $crawler->filterXPath('//meta[@name="parsely-pub-date"]')
            ->each(
                function(Crawler $node) use (&$date_str) {
                        $date_str = $node->attr('content');
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
        $crawler->filterXPath('//meta[@name="parsely-author"]')
            ->each(
                function(Crawler $node) use (&$ret) {
                        $ret = $node->attr('content');
                }
            );

        return $ret;
    }
}
