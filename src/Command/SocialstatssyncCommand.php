<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace HeimrichHannot\NewsBundle\Command;

use Codeception\Module\Symfony;
use Contao\CoreBundle\Command\AbstractLockedCommand;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use GuzzleHttp\Exception\RequestException;
use HeimrichHannot\NewsBundle\Command\Crawler\DisqusCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\FacebookCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\GoogleAnalyticsCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\GooglePlusCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\TwitterCrawler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use HeimrichHannot\NewsBundle\NewsModel;
use Model\Collection;
use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SocialstatssyncCommand extends AbstractLockedCommand implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @var $logger Logger
     */
    private $logger;

    /**
     * @var
     */
    private $config;

    /**
     * @var NewsModel|Collection
     */
    private $items;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('hh:news-socialstatssync')->setDescription('Synchronizes the social stats with the database.');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $this->framework->initialize();
        $this->output = $output;

        $this->logger = System::getContainer()->get('monolog.logger.contao');
        $output->writeln('START updating social stats...');
        $this->logger->info('START updating social stats...', ['contao' => new ContaoContext(__CLASS__ . '::' . __FUNCTION__, TL_CRON)]);
        $config       = ['ssl.certificate_authority' => false];
        $route        = System::getContainer()->get('router')->getContext();
        $this->config = [
            'base_url'     => $route->getScheme() . $route->getHost(),
            'social_stats' => System::getContainer()->getParameter('social_stats'),
        ];

//        $this->items = NewsModel::getAllForSocialStatsUpdate(false);
        $this->items = NewsModel::findMultipleByIds([2026]);

        
//        $this->updateGoogleAnalytics();

        try {
            $this->httpClient = new Client($config);

            // count all items
//            $numItems  = NewsModel::getAllForSocialStatsUpdate(true);
            $offset    = 0;
            $chunkSize = (intval($this->config['social_stats']['chunksize']) > 0 ? intval($this->config['social_stats']['chunksize']) : 100);
            $numChunks = intval(ceil($numItems / $chunkSize));

            // chunking
            for ($i = 1; $i <= $numChunks; $i++)
            {
                $newsItems = NewsModel::getAllForSocialStatsUpdate(false, $offset, $chunkSize);

                try
                {
                    $this->updateStats($newsItems, 'facebook', NewsModel::$TYPE_NEWS);
                } catch (GuzzleException $e)
                {
                    $this->logger->warning('facebook stats: ' . $e->getMessage());
                };
//
//                try
//                {
//                    $this->updateStats($newsItems, 'twitter', NewsModel::$TYPE_NEWS);
//                } catch (GuzzleException $e)
//                {
//                    $this->logger->warning('twitter stats: ' . $e->getMessage());
//                };
//
//                try
//                {
//                    $this->updateStats($newsItems, 'googlePlus', NewsModel::$TYPE_NEWS);
//                } catch (GuzzleException $e)
//                {
//                    $this->logger->warning('googlePlus stats: ' . $e->getMessage());
//                };
//
//                try
//                {
//                    $this->updateStats($newsItems, 'disqus', NewsModel::$TYPE_NEWS);
//                } catch (GuzzleException $e)
//                {
//                    $this->logger->warning('disqus stats: ' . $e->getMessage());
//                };
//
                $offset = ($chunkSize * $i) + 1;
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());

            return 1;
        }

        return 0;
    }


    private function updateGoogleAnalytics()
    {
        try {
            $this->output->writeln('Updating Google Analytics pageviews');
            $analyticsCrawler = new GoogleAnalyticsCrawler($this->config['social_stats']['google_analytics']);
        }
        catch (\Exception $e) {
            $message = 'Google Analytics pageviews: ' . $e->getMessage();
            $this->output->writeln($message);
            $this->logger->warning($message, [
                'contao' => new ContaoContext(__CLASS__ . '::' . __FUNCTION__, TL_CRON)
            ]);
        };
    }


    private function updatePageViews($items)
    {
        // all existing entries for update
        if (null === $items) {
            $message = 'No items to update for google analytics ';
            $this->output->writeln($message);
            $this->logger->info($message);

            return 0;
        }
        $config = $this->config['social_stats']['google_analytics'];

        $email            = $config['email'];
        $keyId            = $config['key_id'];
        $clientId         = $config['client_id'];
        $clientKey        = $config['client_key'];
        $viewId           = $config['view_id'];
        $client           = new \Google_Client();
        $analyticsCrawler = new GoogleAnalyticsCrawler($client, $email, $keyId, $clientId, $clientKey, $viewId);

        $items = NewsModel::findMultipleByIds([2026]);

        // update existing items
        /** @var  $item  NewsModel */
//        foreach ($items as $item) {
//            $urls    = $item->getLegacyUrls($this->config['base_url']);
//            $message = 'Updating Google Analytics stats for url: ' . $url;
//            $this->output->writeln($message);
//            foreach ($urls as $url) {
//                $gaCount = $analyticsCrawler->getCount($url);
//                $message = 'Found ' . intval($gaCount) . ' pageviews.';
//                $this->logger->debug($message);
//                $this->output->writeln($message);
//                if ($gaCount > 0)
//                {
//                    $item->google_analytic_counter    = $gaCount;
//                    $item->google_analytic_updated_at = time();
//                    $item->save();
//                }
//            }
//        }
    }



    /**
     * Updates the stats for the given items of the given provider.
     *
     * @param array $items Collection of items to fetch stats for
     * @param string $provider Identifier for social platform
     * @param string $type Type of item
     *
     * @throws \Exception
     */
    private function updateStats($items, $provider, $type)
    {
        // all existing entries for update
        if (null === $items) {
            $this->logger->info('No items to update for: ' . $provider);

            return 0;
        }
        /** @var $item NewsModel */
        foreach ($items as $item) {
            try {
                if ('facebook' == $provider) {
                    if ($item) {
                        $this->logger->debug('Updating fb stats for url: ' . $item->getUrl($this->config['base_url']));
                        $fb                        = new FacebookCrawler($this->httpClient, $item->getUrl($this->config['base_url']));
                        $item->facebook_updated_at = time();
                        $item->faceook_counter     = $fb->getCount();
                        $item->save();
                    }
                } else {
                    if ('twitter' == $provider) {
                        if ($item) {
                            $this->logger->debug('Updating twitter stats for url: ' . $item->getUrl($this->config['base_url']));
                            $tw                       = new TwitterCrawler($this->httpClient, $item->getUrl($this->config['base_url']));
                            $item->twitter_count      = $tw->getCount();
                            $item->twitter_updated_at = time();
                            $item->save();
                        }
                    } else {
                        if ('googlePlus' == $provider) {
                            if ($item) {
                                $this->logger->debug('Updating googleplus stats for url: ' . $item->getUrl($this->config['base_url']));
                                $gp                           = new GooglePlusCrawler($this->httpClient, $item->getUrl($this->config['base_url']));
                                $item->google_plus_updated_at = $gp->getCount();
                                $item->google_plus_updated_at = time();
                                $item->save();
                            }
                        } else {
                            if ('disqus' == $provider) {
                                if ($item) {
                                    if ($type == NewsModel::$TYPE_NEWS) {
                                        $identifier = 'news-id-' . $item->id;
                                    } else {
                                        $identifier = "";
                                    }

                                    $this->logger->debug('Updating disqus stats for identifier: ' . $identifier);
                                    $disqusPublicApiKey      = $this->config['social_stats']['disqusPublicApiKey'];
                                    $disqusForumName         = $this->config['social_stats']['disqusForumName'];
                                    $d                       = new DisqusCrawler(
                                        $this->httpClient, $this->config['social_stats']['urlprefixFixed'] . $item->url, $disqusPublicApiKey, $disqusForumName, $identifier
                                    );
                                    $item->disqus_counter    = $d->getCount();
                                    $item->disqus_updated_at = time();
                                    $item->save();
                                }
                            } else {
                                throw new \Exception('unknown provider: ' . $provider);
                            }
                        }
                    }
                }
            } catch (RequestException $e) {
                $message = $provider.' error: '.$e->getMessage();
                $this->logger->notice($message);
                $this->output->writeln($message);
            };
        }
    }
}
