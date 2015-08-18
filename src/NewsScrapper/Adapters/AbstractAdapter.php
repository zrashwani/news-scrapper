<?php

namespace Zrashwani\NewsScrapper\Adapters;

use \Symfony\Component\DomCrawler\Crawler;

/**
 * Base class that defines skeleton of the any adapter implemented
 *
 * @author Zeid Rashwani <zrashwani.com>
 */
abstract class AbstractAdapter
{

    abstract public function extractTitle(Crawler $crawler);

    abstract public function extractImage(Crawler $crawler);

    abstract public function extractDescription(Crawler $crawler);

    abstract public function extractKeywords(Crawler $crawler);

    abstract public function extractBody(Crawler $crawler);

    abstract public function extractPublishDate(Crawler $crawler);

    abstract public function extractAuthor(Crawler $crawler);

    /**
     * normalize link and turn it into absolute format
     * @param string $link
     * @param string $baseUrl
     * @return string
     */
    public function normalizeLink($link, $baseUrl)
    {
        if (preg_match('@^http(s?)://.*$@', $link) === 0) { //is not absolute
            $link = $baseUrl . trim($link, '/');
        } elseif (strpos('//', $link) === 0) {
            $protocol = parse_url($baseUrl, 'schema');
            $link = $protocol . ':' . $link;
        } elseif (strpos('/', $link) === 0 && strpos('/', $baseUrl) == strlen($baseUrl) - 1) {
            $link = $baseUrl . trim($link, '/');
        }
        return $link;
    }

    /**
     * normalizing html scrapped by removing unwanted tags (ex. script, css)
     * and amending external resources paths
     * @param string $html
     * @return string
     */
    public function normalizeHtml(Crawler $crawler)
    {
        $disallowed_tags = ['script', 'style', 'iframe'];
        $crawler
            ->filter(implode(',', $disallowed_tags))
            ->each(
                function (Crawler $node, $i) {
                    foreach ($node as $subnode) {
                            //delete these elements from dom document
                            $subnode->parentNode->removeChild($subnode);
                    }
                }
            );

        $html = '';
        foreach ($crawler as $domElement) {
            $html .= $domElement->ownerDocument->saveHTML($domElement);
        }

        return $html;
    }


    public function normalizeKeywords(array $keywords){
        foreach($keywords as $k => $word){
            $keywords[$k] = trim($word);
        }
        
        return $keywords;
    }
}
