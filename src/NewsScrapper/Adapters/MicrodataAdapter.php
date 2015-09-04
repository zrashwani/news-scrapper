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
                            $ret = trim($node->text());
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
                    if ($node->nodeName() == 'meta') {
                        $ret = trim($node->attr('content'));
                    } else {
                        $ret = trim($node->text());
                    }
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
                    if ($node->nodeName() == 'meta') {
                        $keyword_txt = trim($node->attr('content'));
                    } else {
                        $keyword_txt = trim($node->text());
                    }

                    if (empty($keyword_txt) !== true) {
                        $ret = explode(',', $keyword_txt);
                    }
                }
            );

        return $ret;
    }

    public function extractBody(Crawler $crawler)
    {
        $ret = '';

        $crawler->filterXPath('//*[@itemprop="articleBody"]')
            ->each(
                function (Crawler $node) use (&$ret) {
                        $ret .= $node->html();
                }
            );

        if (empty($ret) === true) {
            $article_types = ['Article', 'NewsArticle', 'Report', 'ScholarlyArticle',
                'MedicalScholarlyArticle', 'SocialMediaPosting',
                'BlogPosting', 'LiveBlogPosting',
                'DiscussionForumPosting', 'TechArticle',
                'APIReference'];

            foreach ($article_types as $article_type) {
                $crawler->filterXPath(
                    "//*[@itemtype='http://schema.org/$article_type']"
                )
                    ->each(
                        function ($node) use (&$ret) {
                                    $ret .= $node->html();
                        }
                    );
                
                if (empty($ret) === false) { //if content found, exit loop                    
                    break;
                }
            }
        }

        $ret = $this->normalizeHtml($ret);    
        
        return $ret;
    }

    public function extractPublishDate(Crawler $crawler)
    {
        $date_str = null;

        $crawler->filterXPath('//*[@itemprop="datePublished"]')
            ->each(
                function ($node) use (&$date_str) {
                    if ($node->nodeName() == 'meta') {
                        $date_str = $node->attr('content');
                    } elseif ($node->attr('datetime')) {
                        $date_str = $node->attr('datetime');
                    }/* else {
                            $date_str = $node->text();
                            }*/
                }
            );

        if (!is_null($date_str)) {
            $date_str = str_replace('ET', '', $date_str); //TODO: amend in better way
            $ret = new \DateTime($date_str);
            return $ret->format(\DateTime::ISO8601);
        }
        
        return $date_str; //null
    }

    public function extractAuthor(Crawler $crawler)
    {
        $ret = null;
        $crawler->filterXPath(
            '//*[@itemprop="author" '.
            'and @itemtype="http://schema.org/Person"]//*[@itemprop="name"]'
        )
            ->each(
                function ($node) use (&$ret) {
                            $ret = $node->text();
                }
            );

        if (is_null($ret)) {
            $crawler->filterXPath('//*[@itemprop="author"]')
                ->each(
                    function ($node) use (&$ret) {
                        if ($node->nodeName() == 'meta') {
                                $ret = $node->attr('content');
                        } else {
                            $ret = $node->text();
                        }
                    }
                );
        }
        $ret = preg_replace('@\s{2,}@', ' ',$ret);
        
        return $ret;
    }
}
