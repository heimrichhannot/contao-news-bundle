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
     * @var null|NewsModel|\Contao\Model\Collection
     */
    private $items = null;

    private $debug = null;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('huh:news:socialstats')
            ->setDescription('Updates the database with social stats.')
            ->addOption('no-chunksize', null, null, "Set to 1 to ignore the limit set in options (means all results). Default 0.")
            ->addOption('no-days', null, null, "Set to 1 to ignore the days settings (means there is no limit due age of the news).")
            ->addOption('only-current', null, null, "Update latest articles.")
            ->addOption('debug-mode', null, null, "Add debug informations to console output.")
            ;

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

        $io->title('Updating social stats...');

        $this->httpClient = new Client([
            'base_url'                  => $this->baseUrl,
            'social_stats'              => System::getContainer()->getParameter('social_stats'),
            'ssl.certificate_authority' => false
        ]);

        $this->applyOptions($input);

        try
        {
            $this->updateGoogleAnalyticsCount();
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
     * Check and apply command options
     *
     * @param InputInterface $input
     */
    private function applyOptions ($input)
    {
        if ($input->getOption('no-chunksize') == 1)
        {
            $this->config['chunksize'] = 0;
            $this->io->note('Ignoring chunksize.');
        }
        if ($input->getOption('no-days') == 1)
        {
            $this->config['days'] = 0;
            $this->io->note('Ignoring days config.');
        }
        if ($input->getOption('debug-mode'))
        {
            $this->debug = $this->io;
            $this->io->note('Activated debug mode');
            $this->io->text('Base-Url: '.$this->baseUrl);
        }
        if ($input->getOption('only-current') == 1)
        {
            /**
             * @var NewsModel $model
             */
            $model = $this->framework->getAdapter(NewsModel::class);
            if ($model)
            {
                $this->items = $model->findPublishedFromToByPids(0, time(), $this->config['archives'], $this->config['chunksize']);
            }
            $this->io->note('Retriving stats for newest items.');
        }
    }

    /**
     *
     */
    private function updateGoogleAnalyticsCount()
    {
        $crawlerConfig = [
            'name' => 'Google Analytics',
            'alias' => 'google_analytics',
            'method' => 'findByGoogleAnalyticsUpdateDate'
        ];
        $this->updateStats(
            new GoogleAnalyticsCrawler(
                $this->httpClient,
                null,
                '',
                $this->config['google_analytics']
            ),
            $crawlerConfig
        );
    }

    /**
     * Update Facebook stats
     */
    private function updateFacebookCount()
    {
        $crawlerConfig = [
            'name' => 'Facebook',
            'alias' => 'facebook',
            'method' => 'findByFacebookCounterUpdateDate'
        ];
        $this->updateStats(
            new FacebookCrawler($this->httpClient),
            $crawlerConfig
        );
    }

    /**
     * Update Twitter stats
     */
    private function updateTwitterCount()
    {
        $crawlerConfig = [
            'name' => 'Twitter',
            'alias' => 'twitter',
            'method' => 'findByTwitterCounterUpdateDate'
        ];
        $this->updateStats(
            new TwitterCrawler($this->httpClient, null, $this->baseUrl, $this->config['twitter']),
            $crawlerConfig
        );
    }

    /**
     * Update Google Plus stats
     */
    private function updateGooglePlusCount()
    {
        $crawlerConfig = [
            'name' => 'Google Plus',
            'alias' => 'google_plus',
            'method' => 'findByGooglePlusCounterUpdateDate'
        ];
        $this->updateStats(
            new GooglePlusCrawler($this->httpClient),
            $crawlerConfig
        );
    }

    /**
     * Update Disqus stats
     */
    private function updateDisqusCount()
    {
        $crawlerConfig = [
            'name' => 'Disqus',
            'alias' => 'disqus',
            'method' => 'findByDisqusCounterUpdateDate'
        ];
        $this->updateStats(
            new DisqusCrawler(
                $this->httpClient,
                null,
                $this->baseUrl,
                $this->config['disqus'],
                $this->getOpti),
            $crawlerConfig
        );
    }

    /**
     * @param AbstractCrawler $crawler
     * @param array $crawlerConfig

     */
    private function updateStats(AbstractCrawler $crawler, array $crawlerConfig)
    {
        if (!array_key_exists($crawlerConfig['alias'], $this->config))
        {
            $this->io->note("No ".$crawlerConfig['name']." config provided. Skipping...");
            return;
        }
        $this->io->section("Retriving ".$crawlerConfig["name"]." counts");
        if (!$this->items)
        {
            $method = $crawlerConfig["method"];
            $items = NewsModel::$method(
                $this->config['chunksize'],
                $this->config['days'],
                $this->config['archives']
            );
        }
        else {
            $items = $this->items;
        }

        foreach ($items as $item)
        {
            $this->io->text('Updating news article ' . $item->id . ' (' . $item->headline . ')');
            $crawler->setItem($item);
            $crawler->setBaseUrl($this->baseUrl);
            $crawler->setIo($this->debug);
            $count = $crawler->getCount();
            if (is_array($count))
            {
                $this->io->note('Error: ' . $count['message']);
                $this->logger->addNotice($crawlerConfig['name'] . ': ' . $count['message']);
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
            $this->io->text('Found ' . $count . ' shares for ' . $crawlerConfig['name'] . '.');
        }
    }
}
