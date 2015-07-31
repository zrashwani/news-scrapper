<?php

namespace Zrashwani\NewsScrapper\Adapters;

use \Symfony\Component\DomCrawler\Crawler;

/**
 * Adapter to extract news base on microdata format base on schema.org specifications
 * @link http://schema.org/Article schema.org NewsArticle specification
 * @author Zeid Rashwani <zrashwani.com>
 */
class MicrodataAdapter extends AbstractAdapter
{
    /**
     * @todo better way
     * @param Crawler $crawler
     * @return string
     */
    public function extractTitle(Crawler $crawler)
    {
        $ret = null;

        $crawler->filterXPath('//*[@itemprop="headline"]')
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

        $crawler->filterXPath('//img[@itemprop="image"]')
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->attr('src');
                }
            );

        return $ret;
    }

    public function extractDescription(Crawler $crawler)
    {
        $ret = null;

        $crawler->filterXPath('//*[@itemprop="description"]')
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->text();
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

        $crawler->filterXPath('//*[@itemprop="keywords"]')
            ->each(
                function ($node) use (&$ret) {
                        $node_txt = trim($node->text());
                    if (!empty($node_txt)) {
                        $ret = explode(',', $node_txt);
                    }
                }
            );

        return $ret;
    }

    public function extractBody(Crawler $crawler)
    {
        $ret = null;
        $crawler->filterXPath("//*[@itemtype='http://schema.org/Article' or".
                " @itemprop='articleBody' or @itemtype='http://schema.org/BlogPosting']")
            ->each(
                function ($node) use (&$ret) {
                        $html = '';
                    foreach ($node as $domElement) {
                        $html .= $domElement->ownerDocument->saveHTML($domElement);
                    }

                        $ret = $this->normalizeHtml($html);
                }
            );

        return $ret;
    }

    public function extractPublishDate(Crawler $crawler)
    {
        $date_str = null;

        $crawler->filterXPath('//*[@itemprop="datePublished"]')
            ->each(
                function ($node) use (&$date_str) {
                        $date_str = $node->text();
                }
            );

        if (!is_null($date_str)) {
            $date_str = str_replace('ET', '', $date_str); //TODO: amend in better way
            $ret = new \DateTime($date_str);
            return $ret->format(\DateTime::ISO8601);
        } else {
            return null;
        }
    }

    public function extractAuthor(Crawler $crawler)
    {
        $ret = null;
        $crawler->filterXPath('//*[@itemprop="author" and @itemtype="http://schema.org/Person"]//*[@itemprop="name"]')
            ->each(
                function ($node) use (&$ret) {
                        $ret = $node->text();
                }
            );

        if (is_null($ret)) {
            $crawler->filterXPath('//*[@itemprop="author"]')
                ->each(
                    function ($node) use (&$ret) {
                            $ret = $node->text();
                    }
                );
        }
        return $ret;
    }
}
