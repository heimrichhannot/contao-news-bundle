<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace  HeimrichHannot\NewsBundle\Command\Crawler;

/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 26.07.17
 * Time: 10:16.
 *
 * @deprecated Replaced by abstract crawler
 */
interface CrawlerInterface
{
    /**
     * Return share count or error message.
     *
     * @return int|string
     */
    public function getCount();
}
