<?php

/*
 * This file is part of Contao.
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace HeimrichHannot\NewsBundle\Command;

use Contao\CoreBundle\Command\AbstractLockedCommand;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use HeimrichHannot\NewsBundle\Command\Crawler\DisqusCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\FacebookCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\GoogleAnalyticsCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\GooglePlusCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\TwitterCrawler;
use HeimrichHannot\NewsBundle\Domain\Repository\NewsRepository;
use HeimrichHannot\NewsBundle\Domain\Repository\SocialStatisticRepository;
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

//        $this->logger = System::getContainer()->get('monolog.logger.conato');
//        $this->logger->info('START updating social stats...', ['contao' => new ContaoContext(__CLASS__ . '::' . __FUNCTION__, TL_CRON)]);
        $config       = ['ssl.certificate_authority' => false];
        $this->config = [
            'chunksize'                        => 20,
            'google_sa_keyfile'                => '',
            'google_sa_email'                  => '1072658179942@developer.gserviceaccount.com',
            'google_sa_clientid'               => '1072658179942.apps.googleusercontent.com',
            'google_analytics_profile_id'      => '77925620',
            'google_analytics_account_id'      => '44950131',
            'google_analytics_account_code_id' => 'UA-44950131-1',
            'urlprefixFixed'                   => '',
            'disqusPublicApiKey'               => 'sYEHYXUOuhRsN41YFbNFdlcxkM76dq6APLNFKyZeSmfGH8OL8QFd3P352mvbuZ03',
            'disqusForumName'                  => 'anwaltauskunft',
        ];

        try
        {
            $this->httpClient = new Client($config);

            $newsRepository = new NewsRepository();

            try
            {
                // update google analytics
//                $newsItems = $newsRepository->getAllForSocialstatsUpdate(false);
                $newsItems = NewsModel::findAll();
                if ($newsItems === null)
                {
                    return 0;
                }
                $this->updatePageViews($newsItems, SocialStatisticRepository::$TYPE_NEWS);
            } catch (\Exception $e)
            {
                $this->logger->warning('ga pageviews: ' . $e->getMessage(), ['contao' => new ContaoContext(__CLASS__ . '::' . __FUNCTION__, TL_CRON)]);
            };

            // count all items
            $numItems  = $newsRepository->getAllForSocialstatsUpdate(true);
            $offset    = 0;
            $chunkSize = (intval($this->config['chunksize']) > 0 ? intval($this->config['chunksize']) : 100);
            $numChunks = intval(ceil($numItems / $chunkSize));

            // chunking
            for ($i = 1; $i <= $numChunks; $i++)
            {
                $newsItems = $newsRepository->getAllForSocialstatsUpdate(false, $offset, $chunkSize);

                try
                {
                    $this->updateStats($newsItems, 'facebook', SocialStatisticRepository::$TYPE_NEWS);
                } catch (GuzzleException $e)
                {
                    $this->logger->warning('facebook stats: ' . $e->getMessage());
                };

                try
                {
                    $this->updateStats($newsItems, 'twitter', SocialStatisticRepository::$TYPE_NEWS);
                } catch (GuzzleException $e)
                {
                    $this->logger->warning('twitter stats: ' . $e->getMessage());
                };

                try
                {
                    $this->updateStats($newsItems, 'googlePlus', SocialStatisticRepository::$TYPE_NEWS);
                } catch (GuzzleException $e)
                {
                    $this->logger->warning('googlePlus stats: ' . $e->getMessage());
                };

                try
                {
                    $this->updateStats($newsItems, 'disqus', SocialStatisticRepository::$TYPE_NEWS);
                } catch (GuzzleException $e)
                {
                    $this->logger->warning('disqus stats: ' . $e->getMessage());
                };

                $offset = ($chunkSize * $i) + 1;
            }
//            try
//            {
//                $this->updateMost();
//            } catch (GuzzleException $e)
//            {
//                $this->logger->warning('"most" module: ' . $e->getMessage());
//            } catch (\Exception $e)
//            {
//                $this->logger->warning('"most" module: ' . $e->getMessage());
//            };
        } catch (\Exception $e)
        {
            $this->logger->critical($e->getMessage());

            return 1;
        }

        return 0;
    }

    private function updatePageViews($items, $type)
    {
        $uids = [];

        $socialstatisticRepository = new SocialStatisticRepository();

        if ($type == SocialStatisticRepository::$TYPE_NEWS)
        {
            /** @var $itemRepository NewsRepository */
            $itemRepository = new NewsRepository();
        }

        foreach ($items as $item)
        {
            $uids[] = $item->id;
        }

        // all existing entries for update
//        $socialUpdateItems = $socialstatisticRepository->getAllItemsforUpdate('google_analytic', $type, $uids);
        $socialUpdateItems = $socialstatisticRepository::findAll();
        $updateIds = [];
        foreach ($socialUpdateItems as $updateItem)
        {
            $updateIds[] = $updateItem->id;
        }

        $client                 = new \Google_Client();
        $keyfile                = $this->config['google_sa_keyfile'];
        $serviceAccountEmail    = $this->config['google_sa_email'];
        $serviceAccountClientId = $this->config['google_sa_clientid'];
        $gaProfileId            = $this->config['google_analytics_profile_id'];
        $gaAccountId            = $this->config['google_analytics_account_id'];
        $ga                     = new GoogleAnalyticsCrawler($client, $keyfile, $serviceAccountEmail, $serviceAccountClientId, $gaProfileId, $gaAccountId);

        // update existing items
        foreach ($socialUpdateItems as $socialUpdateItem)
        {
            $updatetableItem = $itemRepository->findByid($socialUpdateItem->Id);
            $this->logger->debug('Updating google analytics stats for url: ' . $this->config['urlprefixFixed'] . $updatetableItem->getUrl());
            $url     = $updatetableItem->getUrl();
            $gaCount = $ga->getCount($this->config['urlprefixFixed'] . $url);
            $this->logger->debug('Received google analytics count for url: ' . $this->config['urlprefixFixed'] . $url . ':' . intval($gaCount));
            if ($gaCount > 0)
            {
                $socialUpdateItem->setGoogleAnalyticCounter($gaCount);
                $socialUpdateItem->setGoogleAnalyticUpdatedAt(time());
                $socialstatisticRepository->save($socialUpdateItem);
            }
        }

        // insert new items
        $insertIds = array_diff($uids, $updateIds);
        foreach ($insertIds as $insertId)
        {
            $insertableItem = $itemRepository->findByUid($insertId);
            if ($insertableItem)
            {
                $this->logger->debug('Creating google analytics stats for url ' . $this->config['urlprefixFixed'] . $insertableItem->getUrl());
                $url     = $insertableItem->getUrl();
                $gaCount = $ga->getCount($this->config['urlprefixFixed'] . $url);
                $this->logger->debug('Received google analytics count for url: ' . $this->config['urlprefixFixed'] . $url . ':' . intval($gaCount));
                if ($gaCount > 0)
                {
                    $socialInsertItem = $this->objectManager->get('DAV\DavNews\Domain\Model\SocialStatistic');
                    $socialInsertItem->setId($insertableItem->getUid());
                    $socialInsertItem->setGoogleAnalyticCounter($gaCount);
                    $socialInsertItem->setGoogleAnalyticUpdatedAt(time());
                    $socialInsertItem->setType($type);
                    $socialstatisticRepository->save($socialInsertItem);
                }
            }
        }
    }

    /**
     * Updates or inserts the stats for the given items of the given provider.
     *
     * @param array  $items    Collection of items to fetch stats for
     * @param string $provider Identifier for social platform
     * @param string $type     Type of item
     *
     * @throws \Exception
     */
    private function updateStats($items, $provider, $type)
    {
        $uids                      = [];
        $socialstatisticRepository = new SocialStatisticRepository();

        if ($type == SocialStatisticRepository::$TYPE_NEWS)
        {
            $itemRepository = new NewsRepository();
        }

        foreach ($items as $item)
        {
            $uids[] = $item->getUid();
        }

        // all existing entries for update
        $socialUpdateItems = $socialstatisticRepository->getAllItemsforUpdate($provider, $type, $uids);

        $updateIds = [];
        foreach ($socialUpdateItems as $updateItem)
        {
            $updateIds[] = $updateItem->getId();
        }

        foreach ($socialUpdateItems as $socialUpdateItem)
        {
            try
            {
                if ('facebook' == $provider)
                {
                    $updatetableItem = $itemRepository->findByUid($socialUpdateItem->getId());
                    if ($updatetableItem)
                    {
                        $this->logger->debug('Updating fb stats for url: ' . $this->config['urlprefixFixed'] . $updatetableItem->getUrl());
                        $fb      = new FacebookCrawler($this->httpClient, $this->config['urlprefixFixed'] . $updatetableItem->getUrl());
                        $fbCount = $fb->getCount();
                        $socialUpdateItem->setFacebookCounter($fbCount);
                        $socialUpdateItem->setFacebookUpdatedAt(time());
                        $socialstatisticRepository->save($socialUpdateItem);
                    }
                }
                else if ('twitter' == $provider)
                {
                    $updatetableItem = $itemRepository->findByUid($socialUpdateItem->getId());
                    if ($updatetableItem)
                    {
                        $this->logger->debug('Updating twitter stats for url: ' . $this->config['urlprefixFixed'] . $updatetableItem->getUrl());
                        $tw      = new TwitterCrawler($this->httpClient, $this->config['urlprefixFixed'] . $updatetableItem->getUrl());
                        $twCount = $tw->getCount();
                        $socialUpdateItem->setTwitterCounter($twCount);
                        $socialUpdateItem->setTwitterUpdatedAt(time());
                        $socialstatisticRepository->save($socialUpdateItem);
                    }
                }
                else if ('googlePlus' == $provider)
                {
                    $updatetableItem = $itemRepository->findByUid($socialUpdateItem->getId());
                    if ($updatetableItem)
                    {
                        $this->logger->debug('Updating googleplus stats for url: ' . $this->config['urlprefixFixed'] . $updatetableItem->getUrl());
                        $gp      = new GooglePlusCrawler($this->httpClient, $this->config['urlprefixFixed'] . $updatetableItem->getUrl());
                        $gpCount = $gp->getCount();
                        $socialUpdateItem->setGooglePlusCounter($gpCount);
                        $socialUpdateItem->setGooglePlusUpdatedAt(time());
                        $socialstatisticRepository->save($socialUpdateItem);
                    }
                }
                else if ('disqus' == $provider)
                {
                    $updatetableItem = $itemRepository->findByUid($socialUpdateItem->getId());
                    if ($updatetableItem)
                    {
                        if ($type == SocialStatisticRepository::$TYPE_NEWS)
                        {
                            $identifier = 'news-uid-' . $socialUpdateItem->getId();
                        }
                        else
                        {
                            $identifier = "";
                        }

                        $this->logger->debug('Updating disqus stats for identifier: ' . $identifier);
                        $disqusPublicApiKey = $this->config['disqusPublicApiKey'];
                        $disqusForumName    = $this->config['disqusForumName'];
                        $d                  = new DisqusCrawler(
                            $this->httpClient, $this->config['urlprefixFixed'] . $updatetableItem->getUrl(), $disqusPublicApiKey, $disqusForumName, $identifier
                        );
                        $dCount             = $d->getCount();
                        $socialUpdateItem->setDisqusCounter($dCount);
                        $socialUpdateItem->setDisqusUpdatedAt(time());
                        $socialstatisticRepository->save($socialUpdateItem);
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

        // new entries for insert
        $insertIds = array_diff($uids, $updateIds);
        foreach ($insertIds as $insertId)
        {
            try
            {
                if ('facebook' == $provider)
                {
                    $insertableItem = $itemRepository->findByUid($insertId);
                    if ($insertableItem)
                    {
                        $this->logger->debug('Creating fb stats for url: ' . $this->config['urlprefixFixed'] . $insertableItem->getUrl());
                        $fb               = new FacebookCrawler($this->httpClient, $this->config['urlprefixFixed'] . $insertableItem->getUrl());
                        $fbCount          = $fb->getCount();
                        $socialInsertItem = new SocialStatisticRepository();
                        $socialInsertItem->setId($insertableItem->getUid());
                        $socialInsertItem->setFacebookCounter($fbCount);
                        $socialInsertItem->setFacebookUpdatedAt(time());
                        $socialInsertItem->setType($type);
                        $socialstatisticRepository->save($socialInsertItem);
                    }
                }
                else if ('twitter' == $provider)
                {
                    $insertableItem = $itemRepository->findByUid($insertId);
                    if ($insertableItem)
                    {
                        $this->logger->debug('Creating twitter stats for url: ' . $this->config['urlprefixFixed'] . $insertableItem->getUrl());
                        $tw               = new TwitterCrawler($this->httpClient, $this->config['urlprefixFixed'] . $insertableItem->getUrl());
                        $twCount          = $tw->getCount();
                        $socialInsertItem = new SocialStatisticRepository();
                        $socialInsertItem->setId($insertableItem->getUid());
                        $socialInsertItem->setTwitterCounter($twCount);
                        $socialInsertItem->setTwitterUpdatedAt(time());
                        $socialInsertItem->setType($type);
                        $socialstatisticRepository->save($socialInsertItem);
                    }
                }
                else if ('googlePlus' == $provider)
                {
                    $insertableItem = $itemRepository->findByUid($insertId);
                    if ($insertableItem)
                    {
                        $this->logger->debug('Creating googleplus stats for url: ' . $this->config['urlprefixFixed'] . $insertableItem->getUrl());
                        $gp               = new GooglePlusCrawler($this->httpClient, $this->config['urlprefixFixed'] . $insertableItem->getUrl());
                        $gpCount          = $gp->getCount();
                        $socialInsertItem = new SocialStatisticRepository();
                        $socialInsertItem->setId($insertableItem->getUid());
                        $socialInsertItem->setGooglePlusCounter($gpCount);
                        $socialInsertItem->setGooglePlusUpdatedAt(time());
                        $socialInsertItem->setType($type);
                        $socialstatisticRepository->save($socialInsertItem);
                    }
                }
                else if ('disqus' == $provider)
                {
                    $insertableItem = $itemRepository->findByUid($insertId);
                    if ($insertableItem)
                    {
                        $this->logger->debug('Creating disqus stats for url: ' . $this->config['urlprefixFixed'] . $insertableItem->getUrl());
                        $disqusPublicApiKey = $this->config['disqusPublicApiKey'];
                        $disqusForumName    = $this->config['disqusForumName'];
                        $d                  =
                            new DisqusCrawler($this->httpClient, $this->config['urlprefixFixed'] . $insertableItem->getUrl(), $disqusPublicApiKey, $disqusForumName);
                        $dCount             = $d->getCount();
                        $socialInsertItem   = new SocialStatisticRepository();
                        $socialInsertItem->setId($insertableItem->getUid());
                        $socialInsertItem->setGooglePlusCounter($dCount);
                        $socialInsertItem->setGooglePlusUpdatedAt(time());
                        $socialInsertItem->setType($type);
                        $socialstatisticRepository->save($socialInsertItem);
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

        // force saving to database immediately
        $this->persistenceManager->persistAll();
    }

    private function updateMost()
    {
        $mostRepository = $this->objectManager->get('DAV\DavMost\Domain\Repository\MostItemRepository');

        $changedUids = [];

        $this->logger->debug('Updating records for "most" module...');

        // ---------------------------
        //  COMMENTS
        // ---------------------------
        $disqusPublicApiKey = $this->config['disqusPublicApiKey'];
        $disqusForumName    = $this->config['disqusForumName'];
        $disqus             = new DisqusCrawler($this->httpClient, null, $disqusPublicApiKey, $disqusForumName);
        $posts              = $disqus->getCountMost('30d', 5);

        // update/insert records
        foreach ($posts as $uid => $count)
        {
            $record = $mostRepository->getItemByIdAndType($uid, MostItemRepository::$TYPE_NEWS);
            if (!$record)
            {
                $record = $this->objectManager->get('DAV\DavMost\Domain\Model\MostItem');
                $record->setItemId($uid);
                $record->setType(MostItemRepository::$TYPE_NEWS);
                $record->setCommentCounter($count);
                $record->setCommentUpdatedAt(time());
                $mostRepository->save($record);
                $changedUids[] = $uid;
            }
            else
            {
                $record->setCommentCounter($count);
                $record->setCommentUpdatedAt(time());
                $mostRepository->save($record);
                $changedUids[] = $uid;
            }
        }

        // ---------------------------
        //  PAGEVIEWS
        // ---------------------------
        $client                 = new \Google_Client();
        $keyfile                = $this->config['google_sa_keyfile'];
        $serviceAccountEmail    = $this->config['google_sa_email'];
        $serviceAccountClientId = $this->config['google_sa_clientid'];
        $gaProfileId            = $this->config['google_analytics_profile_id'];
        $gaAccountId            = $this->config['google_analytics_account_id'];
        $ga                     = new GoogleAnalyticsCrawler($client, $keyfile, $serviceAccountEmail, $serviceAccountClientId, $gaProfileId, $gaAccountId, true);
        $views                  = $ga->getCountMost();

        foreach ($views as $uid => $count)
        {
            $record = $mostRepository->getItemByIdAndType($uid, MostItemRepository::$TYPE_NEWS);
            if (!$record)
            {
                $record = $this->objectManager->get('DAV\DavMost\Domain\Model\MostItem');
                $record->setItemId($uid);
                $record->setType(MostItemRepository::$TYPE_NEWS);
                $record->setViewCounter($count);
                $record->setViewUpdatedAt(time());
                $mostRepository->save($record);
                $changedUids[] = $uid;
            }
            else
            {
                $record->setViewCounter($count);
                $record->setViewUpdatedAt(time());
                $mostRepository->save($record);
                $changedUids[] = $uid;
            }
        }

        // delete not updated records
        $mostRepository->deleteExcept(implode(', ', $changedUids));

        $this->logger->debug('Updating records for "most" module COMPLETED.');

    }

}
