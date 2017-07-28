<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 26.07.17
 * Time: 10:18
 */

namespace  HeimrichHannot\NewsBundle\Command\Crawler;


abstract class AbstractCrawler implements CrawlerInterface
{
    protected $client;
    protected $url;

    public function __construct($client, $url)
    {
        $this->client = $client;
        $this->url    = $url;
    }
}