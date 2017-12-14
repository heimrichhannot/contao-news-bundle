<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 26.07.17
 * Time: 10:18
 */

namespace HeimrichHannot\NewsBundle\Command\Crawler;

use Contao\NewsModel;
use GuzzleHttp\Client;

abstract class AbstractCrawler implements CrawlerInterface
{
    const ERROR_NO_ERROR = 0;
    const ERROR_BREAKING = 1;
    const ERROR_NOTICE = 2;

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
     * @var array
     */
    protected $error = [
        'code' => 1,
        'message' => 'No error specified'
    ];

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
        $this->count   = 0;
    }

    /**
     * Returns all available urls for an array
     * @return array
     */
    public function getUrls()
    {
        \System::getContainer()->get('contao.framework')->initialize();
        $urls   = [];
        if (isset($GLOBALS['TL_HOOKS']['addNewsArticleUrlsToSocialStats'])
            && is_array($GLOBALS['TL_HOOKS']['addNewsArticleUrlsToSocialStats'])) {
            foreach ($GLOBALS['TL_HOOKS']['addNewsArticleUrlsToSocialStats'] as $callback) {
                $addUrls = \System::importStatic($callback[0])->{$callback[1]}($this->item, $this->baseUrl);
                $urls    = array_merge(
                    $addUrls,
                    $urls
                );
            }
        }
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

    public function setErrorCode ($code)
    {
        $this->error['code'] = $code;
        return $this->error;
    }

    public function setErrorMessage ($message)
    {
        $this->error['message'] = $message;
        return $this->error;
    }

    /**
     * @return array
     */
    public function getError(): array
    {
        return $this->error;
    }




}