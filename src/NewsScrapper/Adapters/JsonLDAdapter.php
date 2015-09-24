<?php

namespace Zrashwani\NewsScrapper\Adapters;

use \Symfony\Component\DomCrawler\Crawler;

/**
 * Adapter to extract news base on json-ld format based on schema.org vocabulary specifications
 * @link http://www.w3.org/TR/json-ld/#embedding-json-ld-in-html-documents json-ld W3C recommendation
 * @author Zeid Rashwani <zrashwani.com>
 */
class JsonLDAdapter extends AbstractAdapter
{
    private $json_cached = array();
    private $crawler_cached = null;
    
    public function extractTitle(Crawler $crawler)
    {
        $article_data = $this->getJsonData($crawler);
        $ret = isset($article_data['headline'])?$article_data['headline']:null;

        return $ret;
    }

    public function extractImage(Crawler $crawler)
    {
        $ret = null;
        $article_data = $this->getJsonData($crawler);
        
        if (isset($article_data['image']) === true) {
            $ret = $article_data['image'];
        } elseif (isset($article_data['thumbnailUrl']) === true) {
            $ret = $article_data['thumbnailUrl'];
        }
        
        return $ret;
    }

    public function extractDescription(Crawler $crawler)
    {
        $article_data = $this->getJsonData($crawler);
        $ret = isset($article_data['description'])?$article_data['description']:null;
        
        return $ret;
    }

    public function extractKeywords(Crawler $crawler)
    {
        $article_data = $this->getJsonData($crawler);
        $ret = isset($article_data['keywords'])?$article_data['keywords']:array();
        
        if (!is_array($ret)) {
            $ret = explode(',', $ret);
        }

        return $ret;
    }

    /**
     * extracting body is not implemented for this adapter,
     * json-ld don't have this data
     * @param Crawler $crawler
     * @return null
     */
    public function extractBody(Crawler $crawler)
    {
        return;
    }

    /**
     * extracting publish date based on "datePublished" or "dateCreated" information
     * @param Crawler $crawler
     * @return string
     */
    public function extractPublishDate(Crawler $crawler)
    {
        $date_str = null;
        $article_data = $this->getJsonData($crawler);
        
        if (isset($article_data['datePublished']) === true) {
            $date_str = $article_data['datePublished'];
        } elseif (isset($article_data['dateCreated']) === true) {
            $date_str = $article_data['dateCreated'];
        }

        if (!is_null($date_str)) {
            $ret = new \DateTime($date_str);
            return $ret->format(\DateTime::ISO8601);
        } else {
            return null;
        }
    }

    /**
     * extracting author name through json "author" or "creator" information
     * @param Crawler $crawler
     * @return string
     */
    public function extractAuthor(Crawler $crawler)
    {
        $ret = null;
        $article_data = $this->getJsonData($crawler);
        $author_data = null;
        
        if (isset($article_data['author'])) {
            $author_data = $article_data['author'];
        } elseif (isset($article_data['creator'])) {
            $author_data = $article_data['creator'];
        }
        
        if ($author_data !== null) {
            if (is_array($author_data) === true) {
                if (isset($author_data['@type']) === true && $author_data['@type'] == 'Person') {
                    $ret = $author_data['name'];
                } else {
                    $ret = implode(', ', $author_data);
                }
            } else {
                $ret = $author_data;
            }
        }
        

        return $ret;
    }
    
    /**
     * getting json data decoded as array from crawler object
     * @param Crawler $crawler
     * @return array
     */
    protected function getJsonData(Crawler $crawler)
    {
        if (count($this->json_cached) && $crawler === $this->crawler_cached) { //avoid executing xpath several times
            return $this->json_cached;
        }
        
        $ret = array();
        $crawler->filterXPath('//script[@type="application/ld+json"]')
                ->each(function (Crawler $node) use (&$ret) {
                    $json_content = trim($node->text());
                    if (empty($json_content) == true && $node->attr('src')) {
                        $script_path = $this->normalizeLink($node->attr('src'));
                        $json_content = file_get_contents($script_path);
                    }
                    
                    $ret = json_decode($json_content, true);
                });
        
        $valid_article = $this->checkIfArticle($ret);
        if ($valid_article) {
            $this->json_cached = $ret;
            $this->crawler_cached = $crawler;
        }
                
        return $this->json_cached;
    }
    
    /**
     * check if json-ld array passed represents a valid article type
     * based on schema.org vocabulary specification
     * @param array $article_data
     * @return boolean
     */
    protected function checkIfArticle(array $article_data)
    {
        $article_types = ['Article', 'NewsArticle', 'Report', 'ScholarlyArticle',
                'MedicalScholarlyArticle', 'SocialMediaPosting',
                'BlogPosting', 'LiveBlogPosting',
                'DiscussionForumPosting', 'TechArticle',
                'APIReference'];
        
        if (isset($article_data['@context']) &&
                $article_data['@context']=='http://schema.org' &&
                isset($article_data['@type']) &&
                in_array($article_data['@type'], $article_types)) {
            return true;
        } else {
            return false;
        }
    }
}
