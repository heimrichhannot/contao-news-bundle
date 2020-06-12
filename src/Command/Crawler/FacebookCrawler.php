<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace  HeimrichHannot\NewsBundle\Command\Crawler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class FacebookCrawler extends AbstractCrawler
{
    /**
     * FacebookCrawler constructor.
     *
     * @param $client Client
     * @param $item
     * @param $baseUrl
     */
    public function __construct($client, $item = null, $baseUrl = '')
    {
        parent::__construct($client, $item, '');
    }

    /**
     * Return share count or error message.
     *
     * @return int|array
     */
    public function getCount()
    {
        $this->count = 0;
        $count = 0;

        foreach ($this->getUrls() as $url) {
            try {
                $response = $this->client->request('GET', 'https://graph.facebook.com/?id='.$url);
            } catch (ClientException $e) {
                $this->setErrorCode(static::ERROR_BREAKING);
                $error = json_decode($e->getResponse()->getBody()->getContents());
                $this->setErrorMessage($error->error->message);

                return $this->error;
            }

            if ($response && 200 == $response->getStatusCode()) {
                $data = json_decode($response->getBody()->getContents(), true);
                $count += (int) ($data['share']['share_count']);
            }
        }
        $this->count = $count;

        return $count;
    }

    /**
     * Update the current item.
     */
    public function updateItem()
    {
        $this->item->facebook_counter = $this->count;
        $this->item->facebook_updated_at = time();
        $this->item->save();
    }
}
