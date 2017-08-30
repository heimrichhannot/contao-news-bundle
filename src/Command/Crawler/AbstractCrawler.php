<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 26.07.17
 * Time: 10:18
 */

namespace HeimrichHannot\NewsBundle\Command\Crawler;


use GuzzleHttp\Client;
use HeimrichHannot\NewsBundle\NewsModel;

abstract class AbstractCrawler implements CrawlerInterface
{
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var NewsModel
     */
    protected $item;
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var integer
     */
    protected $count;

    /**
     * AbstractCrawler constructor.
     * @param Client $client
     * @param NewsModel $item
     * @param string $baseUrl
     */
    public function __construct($client, $item = null, $baseUrl = '')
    {
        $this->client  = $client;
        $this->item    = $item;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Returns all available urls for an array
     * @return array
     */
    public function getUrls()
    {
        $urls = [];
        $urls[] = $this->item->getUrl($this->baseUrl);
        if (isset($GLOBALS['TL_HOOKS']['addNewsArticleUrlsToSocialStats'])
            && is_array($GLOBALS['TL_HOOKS']['addNewsArticleUrlsToSocialStats']))
        {
            foreach ($GLOBALS['TL_HOOKS']['addNewsArticleUrlsToSocialStats'] as $callback)
            {
                $this->import($callback[0]);
                $urls = array_merge(
                    $this->$callback[0]->$callback[1]($this->item),
                    $urls
                );
            }
        }
        $urls = $this->item->getLegacyUrls($this->baseUrl);
        return $urls;
    }

    /**
     * Update the current item
     */
    public function updateItem()
    {
    }

    /**
     * @return NewsModel
     */
    public function getItem(): NewsModel
    {
        return $this->item;
    }

    /**
     * @param NewsModel $item
     */
    public function setItem(NewsModel $item)
    {
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }


}