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
    public $currentUrl;

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
     * @return string
     */
    public function normalizeLink($link)
    {
        $baseUrl = $this->currentUrl;
        if (preg_match('@^http(s?)://.*$@', $baseUrl) === 0 && //local environment assumed here
            preg_match('@^http(s?)://.*$@', $link) === 0) {
                $link = pathinfo($baseUrl, PATHINFO_DIRNAME).'/'.$link;
        } elseif (preg_match('@^http(s?)://.*$@', $link) === 0) { //is not absolute
            $urlParts = parse_url($baseUrl);
            $scheme = isset($urlParts['scheme'])===true?$urlParts['scheme']:'http';
            $host = isset($urlParts['host'])===true?$urlParts['host']:'';
            if (strpos($link, '//') === 0) { //begins with //
                $link = $scheme . ':' . $link;
            } elseif (strpos($link, '/') === 0) { //begins with /
                $link = $scheme.'://'.$host.$link;
            } else {
                $path = isset($urlParts['path'])===true?$urlParts['path']:'/';
                $link = $scheme.'://'.$host.$path.$link;
            }
        }
        
        return $link;
    }

    /**
     * normalizing html scrapped by removing unwanted tags (ex. script, css)
     * and amending external resources paths
     * @param string $raw_html
     * @return string
     */
    public function normalizeHtml($raw_html)
    {
        if (empty($raw_html)) {
            return $raw_html;
        }
        $crawler = new Crawler($raw_html);
        $disallowed_tags = ['script', 'style', 'meta'];
        
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
        
        $html = $this->normalizeBodyLinks($crawler->html());
        $html2 = preg_replace('@\s{2,}@', ' ', $html); //remove empty spaces from document
        
        return $html2;
    }
    
    /**
     * covert all relative paths in html to absolute ones
     * including: img src, a href ...etc.
     * @param string $html
     * @return string
     */
    public function normalizeBodyLinks($html)
    {
        if (empty($html)===true) { //if html is empty, do nothing
            return $html;
        }
        
        $xmlDoc = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $xmlDoc->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($xmlDoc);
        $lnk_entries = $xpath->query('//a');
        
        foreach ($lnk_entries as $entry) {
                $href = $entry->getAttribute('href');
                $normalized_href = $this->normalizeLink($href);
                
                $entry->setAttribute('href', $normalized_href);
                $entry->setAttribute('target', '_blank');
        }
        
        $img_entries = $xpath->query('//img');
        
        foreach ($img_entries as $entry) {
                $src = $entry->getAttribute('src');
                $normalized_src = $this->normalizeLink($src);
                $entry->setAttribute('src', $normalized_src);
        }
        
        $final_html = $xmlDoc->saveHTML();
                
        return $this->getBodyHtml($final_html);
    }
    

    /**
     * normalize keywords by removing spaces from each
     * @param array $keywords
     * @return array
     */
    public function normalizeKeywords(array $keywords)
    {
        foreach ($keywords as $k => $word) {
            $keywords[$k] = trim($word);
        }
        
        return $keywords;
    }
    
    /**
     * extract body content from html document
     * @param string $doc_html
     * @return string
     */
    protected function getBodyHtml($doc_html)
    {
        $html_crawler = new Crawler($doc_html);
        
        $ret = '';
        $html_crawler->filter('body')->each(
            function (Crawler $node) use (&$ret) {
                $ret = $node->html();
            }
        );
        
        return $ret;
    }
}
