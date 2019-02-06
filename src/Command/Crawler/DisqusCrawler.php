<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace  HeimrichHannot\NewsBundle\Command\Crawler;

use Contao\NewsModel;
use GuzzleHttp\Exception\ClientException;

class DisqusCrawler extends AbstractCrawler
{
    private $config;
    private $forum = null;
    private $apikey = null;
    private $identifier = '';

    /**
     * DisqusCrawler constructor.
     *
     * @param \GuzzleHttp\Client                         $client
     * @param \HeimrichHannot\NewsBundle\Model\NewsModel $item
     * @param string                                     $baseUrl
     * @param array                                      $config
     */
    public function __construct($client, $item, $baseUrl, $config)
    {
        parent::__construct($client, $item, $baseUrl);
        $this->config = $config;
        $this->apikey = $config['public_api_key'];
        $this->forum = $config['forum_name'];
        $this->identifier = str_replace('{id}', $item->id, $config['identifier']);
    }

    /**
     * Return comment count or error.
     *
     * @return int|array
     */
    public function getCount()
    {
        $this->count = 0;

        if ($this->io) {
            $this->io->text('Forum: '.$this->forum.' | Thread: '.$this->identifier);
        }
        $count = 0;

        try {
            $response = $this->client->request(
                'GET',
                'https://disqus.com/api/3.0/threads/details.json?api_key='.$this->apikey
                .'&forum='.$this->forum.'&thread:ident='.$this->identifier
            );
        } catch (ClientException $e) {
            $error = json_decode($e->getResponse()->getBody()->getContents());
            $this->setErrorMessage($error->response);

            if (2 == $error->code) {
                $this->setErrorCode(AbstractCrawler::ERROR_NOTICE);
            }

            return $this->error;
        }

        if ($response && 200 == $response->getStatusCode()) {
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['response']) && isset($data['response']['posts'])) {
                $count = (int) ($data['response']['posts']);
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
        $this->item->disqus_counter = $this->count;
        $this->item->disqus_updated_at = time();
        $this->item->save();
    }

    /**
     * @param NewsModel $item
     */
    public function setItem(NewsModel $item)
    {
        parent::setItem($item);
        $this->identifier = str_replace('{id}', $item->id, $this->config['identifier']);
    }
}
