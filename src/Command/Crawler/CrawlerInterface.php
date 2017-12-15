<?php

namespace  HeimrichHannot\NewsBundle\Command\Crawler;

/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 26.07.17
 * Time: 10:16
 *
 * @deprecated Replaced by abstract crawler
 */
interface CrawlerInterface
{
    /**
     * Return share count or error message
     * @return integer|string
     */
    public function getCount();
}