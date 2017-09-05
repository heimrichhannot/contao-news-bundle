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
        $this->setName('hundh:news:socialstats')->setDescription('Updates the database with social stats.');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $this->framework->initialize();
        $this->output  = $output;
        $route         = System::getContainer()->get('router')->getContext();
        $this->baseUrl = $route->getScheme() . $route->getHost();
        $this->logger  = System::getContainer()->get('monolog.logger.contao');
        $this->config  = System::getContainer()->getParameter('social_stats');

        $message = 'START updating social stats...';
        $output->writeln($message);
        $this->logger->info($message, ['contao' => new ContaoContext(__CLASS__ . '::' . __FUNCTION__, TL_CRON)]);

        $this->items      = NewsModel::findMultipleByIds([2114, 1427, 2026]);
        $this->httpClient = new Client([
            'base_url'                  => $route->getScheme() . $route->getHost(),
            'social_stats'              => System::getContainer()->getParameter('social_stats'),
            'ssl.certificate_authority' => false
        ]);

        try {
            $this->updateGoogleAnalyticsCount();
            $this->updateFacebookCount();
            $this->updateTwitterCount();
            $this->updateGooglePlusCount();
            $this->updateDisqusCount();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $this->output->writeln('<fg=red>Error: ' . $e->getMessage() . '</>');
            return 1;
        }

        return 0;
    }

    /**
     *
     */
    private function updateGoogleAnalyticsCount()
    {
        if (!array_key_exists('google_analytics', $this->config)) {
            $message = "No Google Analytics config provided. Skipping...";
            $this->output->writeln('<bg=red>' . $message . '</>');
            $this->logger->addNotice($message);
            return;
        }
        $items = NewsModel::findByGoogleAnalyticsUpdateDate($this->config['chunksize'], $this->config['days'], $this->config['archives']);
        $this->output->writeln("<fg=green;options=bold>Retriving Google Analytics counts</>");
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
        if (!array_key_exists('facebook', $this->config)) {
            $this->output->writeln('<bg=red>No Facebook config provided. Skipping...</>');
            $this->logger->addNotice('No Facebook config provided. Skipping...');
            return;
        }
        $items = NewsModel::findByFacebookCounterUpdateDate($this->config['chunksize'], $this->config['days'], $this->config['archives']);
        $this->output->writeln("<fg=green;options=bold>Retriving Facebook counts</>");
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
        if (!$this->config['twitter']) {
            $this->output->writeln('<bg=red>No Twitter config provided. Skipping...</>');
            $this->logger->addNotice('No Twitter config provided. Skipping...');
            return;
        }
        $items = NewsModel::findByTwitterCounterUpdateDate($this->config['chunksize'], $this->config['days'], $this->config['archives']);
        $this->output->writeln("<fg=green;options=bold>Retriving Twitter counts</>");
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
        if (!array_key_exists('google_plus', $this->config)) {
            $this->output->writeln('<bg=red>No Google Plus config provided. Skipping...</>');
            $this->logger->addNotice('No Google Plus config provided. Skipping...');
            return;
        }
        $items = NewsModel::findByGooglePlusCounterUpdateDate($this->config['chunksize'], $this->config['days'], $this->config['archives']);
        $this->output->writeln("<fg=green;options=bold>Retriving Google Plus counts</>");
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
        if (!$this->config['disqus']) {
            $this->output->writeln('<bg=red>No Disqus config provided. Skipping...</>');
            $this->logger->addNotice('No Disqus config provided. Skipping...');
            return;
        }
        $items = NewsModel::findByDisqusCounterUpdateDate($this->config['chunksize'], $this->config['days'], $this->config['archives']);
        $this->output->writeln("<fg=green;options=bold>Retriving Disqus counts</>");
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
        foreach ($items as $item) {
            $this->output->writeln('Updating news article ' . $item->id . ' (' . $item->headline . ')');
            $crawler->setItem($item);
            $crawler->setBaseUrl($this->baseUrl);
            $count = $crawler->getCount();
            if (is_array($count)) {
                $this->output->writeln('<bg=red>Error: ' . $count['message'] . '</>');
                $this->logger->addError($provider . ': ' . $count['message']);
                if ($count['code'] == AbstractCrawler::ERROR_BREAKING) {
                    $this->output->writeln('<fg=red>Stopping updating stats for current provider.</>');
                    break;
                } else {
                    continue;
                }
            }
            $crawler->updateItem();
            $this->output->writeln('Found ' . $count . ' shares for ' . $provider . '.');
            $this->logger->addInfo('Found ' . $count . ' shares for ' . $provider . '.');
        }
    }
}
