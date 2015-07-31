<?php

namespace Zrashwani\NewsScrapper;

use Goutte\Client as GoutteClient;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Client to scrap article/news contents from serveral news sources
 *
 * @author Zeid Rashwani <zrashwani.com>
 */
class Client
{
    protected $scrapped_data = array();
    protected $scrapClient;
    protected $adaptersList = ['Microdata', 'HAtom', 'OpenGraph', 'Default'];

    /**
     * adapter to scrap content
     * @var Adapters\AbstractAdapter
     */
    protected $adapter;

    /**
     * constructor
     */
    public function __construct($adapter_name = null)
    {
        $this->scrapClient = new GoutteClient();
        $this->scrapClient->followRedirects();
        $this->scrapClient->getClient()->setDefaultOption('config/curl/' . CURLOPT_SSL_VERIFYHOST, false);
        $this->scrapClient->getClient()->setDefaultOption('config/curl/' . CURLOPT_SSL_VERIFYPEER, false);

        $this->setAdapter($adapter_name);
    }

    /**
     * getting selected adapter
     * @return Adapters\AbstractAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * setting adapter preferred for scrapping
     * @param string $adapter_name
     * @throws \Exception
     */
    public function setAdapter($adapter_name)
    {
        $adapterClass = "\Zrashwani\NewsScrapper\Adapters\\" . $adapter_name . "Adapter";
        if (class_exists($adapterClass)) {
            $this->adapter = new $adapterClass();
        } else {
            //smart adapter is asumed
        }

        return $this;
    }

    /**
     * set new source information, including urls, selectors
     * @param array $news_sources
     * @return Client
     */
    public function setSourceInfo($news_sources)
    {
        $this->news_sources = $news_sources;
        return $this;
    }

    /**
     * get data extracting by scrapping
     * @return array
     */
    public function getScrappedData()
    {
        return $this->scrapped_data;
    }

    /**
     * scrap one source of news
     * @param array $source_info
     * @return array
     */
    public function scrapLinkGroup($baseUrl, $linkSelector)
    {
        $crawler = $this->scrapClient->request('GET', $baseUrl);
        $this->setAdapter('Default'); //initialy

        $scrap_result = array();
        $theClient = $this;
        
        $crawler->filter($linkSelector)
            ->each(
                function ($link_node) use (&$scrap_result, $baseUrl, $theClient) {
                        $link = $theClient->getAdapter()
                            ->normalizeLink($link_node->attr('href'), $baseUrl);

                        $article_info = $this->getLinkData($link);
                        $scrap_result[] = $article_info;
                }
            );

        return $scrap_result;
    }

    /**
     * scrap information for single url
     * @param string $link
     * @return \stdClass
     */
    public function getLinkData($link)
    {
        $article_info = new \stdClass();
        $article_info->url = $link;

        $pageCrawler = $this->scrapClient->request('GET', $article_info->url);

        $selected_adapter = $this->getAdapter();
        if ($selected_adapter !== null) {
            $this->extractPageData($article_info, $pageCrawler, $selected_adapter);
        } else { //apply smart scrapping by iterating over all adapters
            foreach ($this->adaptersList as $adapter_name) {
                $this->setAdapter($adapter_name);
                $this->extractPageData($article_info, $pageCrawler, $this->getAdapter());
            }
        }


        return $article_info;
    }

    /**
     * extracting page data from domCrawler according to rules defined by adapter
     * @param \stdClass                                        $article_info
     * @param Crawler                                          $pageCrawler
     * @param \Zrashwani\NewsScrapper\Adapters\AbstractAdapter $adapter      adapter used for scrapping
     */
    protected function extractPageData($article_info, Crawler $pageCrawler, Adapters\AbstractAdapter $adapter)
    {
        if (!isset($article_info->title)) {
            $article_info->title = $adapter->extractTitle($pageCrawler);
        }
        if (!isset($article_info->body)) {
            $article_info->body = $adapter->extractBody($pageCrawler);
        }
        if (!isset($article_info->image)) {
            $article_info->image = $adapter->extractImage($pageCrawler, $article_info->url);
        }
        if (!isset($article_info->description)) {
            $article_info->description = $adapter->extractDescription($pageCrawler);
        }
        if (!isset($article_info->keywords) || count($article_info->keywords) === 0) {
            $article_info->keywords = $adapter->extractKeywords($pageCrawler);
        }
        if (!isset($article_info->author)) {
            $article_info->author = $adapter->extractAuthor($pageCrawler);
        }
        if (!isset($article_info->publishDate)) {
            $article_info->publishDate = $adapter->extractPublishDate($pageCrawler);
        }
    }
}