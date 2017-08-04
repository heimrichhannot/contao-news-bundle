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
     * @var Client
     */
    private $httpClient;

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

        $this->logger = System::getContainer()->get('monolog.logger.contao');
        $this->logger->info('START updating social stats...', ['contao' => new ContaoContext(__CLASS__ . '::' . __FUNCTION__, TL_CRON)]);
        $config       = ['ssl.certificate_authority' => false];
        $route        = System::getContainer()->get('router')->getContext();
        $this->config = [
            'base_url' => $route->getScheme() . $route->getHost(),
            System::getContainer()->getParameter('social_stats'),
        ];
        try
        {
            $this->httpClient = new Client($config);

            try
            {
                // update google analytics
                $newsItems = NewsModel::getAllForSocialStatsUpdate(false);

                $this->updatePageViews($newsItems);
            } catch (\Exception $e)
            {
                $this->logger->warning('ga pageviews: ' . $e->getMessage(), ['contao' => new ContaoContext(__CLASS__ . '::' . __FUNCTION__, TL_CRON)]);
            };

            // count all items
            $numItems  = NewsModel::getAllForSocialStatsUpdate(true);
            $offset    = 0;
            $chunkSize = (intval($this->config['chunksize']) > 0 ? intval($this->config['chunksize']) : 100);
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

                try
                {
                    $this->updateStats($newsItems, 'twitter', NewsModel::$TYPE_NEWS);
                } catch (GuzzleException $e)
                {
                    $this->logger->warning('twitter stats: ' . $e->getMessage());
                };

                try
                {
                    $this->updateStats($newsItems, 'googlePlus', NewsModel::$TYPE_NEWS);
                } catch (GuzzleException $e)
                {
                    $this->logger->warning('googlePlus stats: ' . $e->getMessage());
                };

                try
                {
                    $this->updateStats($newsItems, 'disqus', NewsModel::$TYPE_NEWS);
                } catch (GuzzleException $e)
                {
                    $this->logger->warning('disqus stats: ' . $e->getMessage());
                };

                $offset = ($chunkSize * $i) + 1;
            }
        } catch (\Exception $e)
        {
            $this->logger->critical($e->getMessage());

            return 1;
        }

        return 0;
    }

    private function updatePageViews($items)
    {
        // all existing entries for update
        if (null === $items)
        {
            $this->logger->info('No items to update for google analytics ');

            return 0;
        }

        $client                 = new \Google_Client();
        $keyfile                = $this->config['google_sa_keyfile'];
        $serviceAccountEmail    = $this->config['google_sa_email'];
        $serviceAccountClientId = $this->config['google_sa_clientid'];
        $gaProfileId            = $this->config['google_analytics_profile_id'];
        $gaAccountId            = $this->config['google_analytics_account_id'];
        $ga                     = new GoogleAnalyticsCrawler($client, $keyfile, $serviceAccountEmail, $serviceAccountClientId, $gaProfileId, $gaAccountId);

        // update existing items
        /** @var  $item  NewsModel */
        foreach ($items as $item)
        {
            $url = $item->getUrl($this->config['base_url']);
            $this->logger->debug('Updating google analytics stats for url: ' . $url);
            $gaCount = $ga->getCount($url);
            $this->logger->debug('Received google analytics count for url: ' . $url . ':' . intval($gaCount));
            if ($gaCount > 0)
            {
                $item->google_analytic_counter    = $gaCount;
                $item->google_analytic_updated_at = time();
                $item->save();
            }
        }
    }

    /**
     * Updates the stats for the given items of the given provider.
     *
     * @param array  $items    Collection of items to fetch stats for
     * @param string $provider Identifier for social platform
     * @param string $type     Type of item
     *
     * @throws \Exception
     */
    private function updateStats($items, $provider, $type)
    {
        // all existing entries for update
        if (null === $items)
        {
            $this->logger->info('No items to update for: ' . $provider);

            return 0;
        }
        /** @var $item NewsModel */
        foreach ($items as $item)
        {
            try
            {
                if ('facebook' == $provider)
                {
                    if ($item)
                    {
                        $this->logger->debug('Updating fb stats for url: ' . $item->getUrl($this->config['base_url']));
                        $fb                        = new FacebookCrawler($this->httpClient, $item->getUrl($this->config['base_url']));
                        $item->facebook_updated_at = time();
                        $item->faceook_counter     = $fb->getCount();
                        $item->save();
                    }
                }
                else if ('twitter' == $provider)
                {
                    if ($item)
                    {
                        $this->logger->debug('Updating twitter stats for url: ' . $item->getUrl($this->config['base_url']));
                        $tw                       = new TwitterCrawler($this->httpClient, $item->getUrl($this->config['base_url']));
                        $item->twitter_count      = $tw->getCount();
                        $item->twitter_updated_at = time();
                        $item->save();
                    }
                }
                else if ('googlePlus' == $provider)
                {
                    if ($item)
                    {
                        $this->logger->debug('Updating googleplus stats for url: ' . $item->getUrl($this->config['base_url']));
                        $gp                           = new GooglePlusCrawler($this->httpClient, $item->getUrl($this->config['base_url']));
                        $item->google_plus_updated_at = $gp->getCount();
                        $item->google_plus_updated_at = time();
                        $item->save();
                    }
                }
                else if ('disqus' == $provider)
                {
                    if ($item)
                    {
                        if ($type == NewsModel::$TYPE_NEWS)
                        {
                            $identifier = 'news-id-' . $item->id;
                        }
                        else
                        {
                            $identifier = "";
                        }

                        $this->logger->debug('Updating disqus stats for identifier: ' . $identifier);
                        $disqusPublicApiKey      = $this->config['disqusPublicApiKey'];
                        $disqusForumName         = $this->config['disqusForumName'];
                        $d                       = new DisqusCrawler(
                            $this->httpClient, $this->config['urlprefixFixed'] . $item->url, $disqusPublicApiKey, $disqusForumName, $identifier
                        );
                        $item->disqus_counter    = $d->getCount();
                        $item->disqus_updated_at = time();
                        $item->save();
                    }
                }
                else
                {
                    throw new \Exception('unknown provider: ' . $provider);
                }
            } catch (RequestException $e)
            {
                $this->logger->notice($e->getMessage());
            };
        }
    }
}
