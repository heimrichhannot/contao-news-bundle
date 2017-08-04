<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 26.07.17
 * Time: 10:18
 */

namespace HeimrichHannot\NewsBundle\Command\Crawler;


use GuzzleHttp\Client;

abstract class AbstractCrawler implements CrawlerInterface
{
    /**
     * @var \Google_Client | Client
     */
    protected $client;
    protected $url;

    public function __construct($client, $url)
    {
        $this->client = $client;
        $this->url    = $url;
    }
}