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
use HeimrichHannot\NewsBundle\Model\NewsModel;
use Contao\System;
use HeimrichHannot\NewsBundle\Command\Crawler\AbstractCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\DisqusCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\FacebookCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\GoogleAnalyticsCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\GooglePlusCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\TwitterCrawler;
use GuzzleHttp\Client;
use Model\Collection;
use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SocialstatssyncCommand extends AbstractLockedCommand implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    public $baseUrl;

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
     * @var SymfonyStyle
     */
    private $io;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('huh:news:socialstats')
            ->setDescription('Updates the database with social stats.')
            ->addOption('no-chunksize', null, null, "Set to 1 to ignore the limit set in options (means all results). Default 0.")
            ->addOption('no-days', null, null, "Set to 1 to ignore the days settings (means there is no limit due age of the news).");
    }

    /**
     * {@inheritdoc}
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $this->framework->initialize();
        $route         = System::getContainer()->get('router')->getContext();
        $this->baseUrl = $route->getScheme() . $route->getHost();
        $this->logger  = System::getContainer()->get('monolog.logger.contao');
        $this->config  = System::getContainer()->getParameter('social_stats');
        $io            = new SymfonyStyle($input, $output);
        $this->io      = $io;
        if ($input->getOption('no-chunksize') == 1)
        {
            $this->config['chunksize'] = 0;
        }
        if ($input->getOption('no-days') == 1)
        {
            $this->config['days'] = 0;
        }

        $io->title('Updating social stats...');

        $this->httpClient = new Client([
            'base_url'                  => $route->getScheme() . $route->getHost(),
            'social_stats'              => System::getContainer()->getParameter('social_stats'),
            'ssl.certificate_authority' => false
        ]);
        try
        {
//            $this->updateGoogleAnalyticsCount();
            $this->updateFacebookCount();
            $this->updateTwitterCount();
            $this->updateGooglePlusCount();
            $this->updateDisqusCount();
        } catch (\Exception $e)
        {
            $this->logger->critical($e->getMessage());
            $io->error($e->getMessage());
            return 1;
        }
        $io->success('Finished updating social stats.');
        return 0;
    }

    /**
     *
     */
    private function updateGoogleAnalyticsCount()
    {
        if (!array_key_exists('google_analytics', $this->config))
        {
            $this->io->note("No Google Analytics config provided. Skipping...");
            return;
        }
        $items = NewsModel::findByGoogleAnalyticsUpdateDate(
            $this->config['chunksize'],
            $this->config['days'],
            $this->config['archives']
        );
        $this->updateStats(
            new GoogleAnalyticsCrawler($this->httpClient, null, '', $this->config['google_analytics']),
            $items,
            'Google Analytics'
        );
    }

    /**
     * Update Facebook stats
     */
    private function updateFacebookCount()
    {
        if (!array_key_exists('facebook', $this->config))
        {
            $this->io->note("No Facebook config provided. Skipping...");
            return;
        }
        $items = NewsModel::findByFacebookCounterUpdateDate($this->config['chunksize'], $this->config['days'], $this->config['archives']);
        $this->updateStats(
            new FacebookCrawler($this->httpClient),
            $items,
            'Facebook'
        );
    }

    /**
     * Update Twitter stats
     */
    private function updateTwitterCount()
    {
        if (!$this->config['twitter'])
        {
            $this->io->note("No Twitter config provided. Skipping...");
            return;
        }
        $items = NewsModel::findByTwitterCounterUpdateDate(
            $this->config['chunksize'],
            $this->config['days'],
            $this->config['archives']
        );
        $this->updateStats(
            new TwitterCrawler($this->httpClient, null, $this->baseUrl, $this->config['twitter']),
            $items,
            'Twitter'
        );
    }

    /**
     * Update Google Plus stats
     */
    private function updateGooglePlusCount()
    {
        if (!array_key_exists('google_plus', $this->config))
        {
            $this->io->note("No Google Plus config provided. Skipping...");
            return;
        }
        $items = NewsModel::findByGooglePlusCounterUpdateDate($this->config['chunksize'], $this->config['days'], $this->config['archives']);
        $this->updateStats(
            new GooglePlusCrawler($this->httpClient),
            $items,
            'Google Plus'
        );
    }

    /**
     * Update Disqus stats
     */
    private function updateDisqusCount()
    {
        if (!$this->config['disqus'])
        {
            $this->io->note("No Disqus config provided. Skipping...");
            return;
        }
        $items = NewsModel::findByDisqusCounterUpdateDate($this->config['chunksize'], $this->config['days'], $this->config['archives']);
        $this->updateStats(
            new DisqusCrawler($this->httpClient, null, $this->baseUrl, $this->config['disqus']),
            $items,
            'Disqus'
        );
    }

    /**
     * @param AbstractCrawler $crawler
     * @param NewsModel|Collection $items
     */
    private function updateStats($crawler, $items, $provider)
    {
        $this->io->section("Retriving $provider counts");
        foreach ($items as $item)
        {
            $this->io->text('Updating news article ' . $item->id . ' (' . $item->headline . ')');
            $crawler->setItem($item);
            $crawler->setBaseUrl($this->baseUrl);
            $count = $crawler->getCount();
            if (is_array($count))
            {
                $this->io->note('Error: ' . $count['message']);
                $this->logger->addNotice($provider . ': ' . $count['message']);
                if ($count['code'] == AbstractCrawler::ERROR_BREAKING)
                {
                    $this->io->note("Stopping updating stats for current provider.");
                    return;
                } else
                {
                    continue;
                }
            }
            $crawler->updateItem();
            $this->io->text('Found ' . $count . ' shares for ' . $provider . '.');
        }
    }
}
