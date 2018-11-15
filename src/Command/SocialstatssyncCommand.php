<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Command;

use Contao\CoreBundle\Command\AbstractLockedCommand;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\System;
use GuzzleHttp\Client;
use HeimrichHannot\NewsBundle\Command\Crawler\AbstractCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\DisqusCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\FacebookCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\GoogleAnalyticsCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\GooglePlusCrawler;
use HeimrichHannot\NewsBundle\Command\Crawler\TwitterCrawler;
use HeimrichHannot\NewsBundle\Model\NewsModel;
use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Debug\Exception\ClassNotFoundException;

class SocialstatssyncCommand extends AbstractLockedCommand implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    public $baseUrl;

    /**
     * @var Logger
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
            ->addOption('no-chunksize', null, null, 'Set to 1 to ignore the limit set in options (means all results). Default 0.')
            ->addOption('no-days', null, null, 'Set to 1 to ignore the days settings (means there is no limit due age of the news).')
            ->addOption('only-current', null, null, 'Update latest articles.')
            ->addOption('debug-mode', null, null, 'Add debug informations to console output.')
            ->addOption('article', null, InputOption::VALUE_OPTIONAL, 'Update stats for a single news article')
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $this->framework->initialize();
        $route = $this->getContainer()->get('router')->getContext();
        $this->baseUrl = $route->getScheme().$route->getHost();
        $this->logger = System::getContainer()->get('monolog.logger.contao');
        $this->config = System::getContainer()->getParameter('social_stats');
        $io = new SymfonyStyle($input, $output);
        $this->io = $io;

        $io->title('Updating social stats...');

        $this->httpClient = $this->setUpHttpClient();

        try {
            $this->applyOptions($input);
        } catch (ClassNotFoundException $e) {
            $this->logger->critical($e->getMessage());
            $io->error($e->getMessage());

            return 0;
        }

        try {
            $this->updateGoogleAnalyticsCount();
            $this->updateFacebookCount();
            $this->updateTwitterCount();
            $this->updateGooglePlusCount();
            $this->updateDisqusCount();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            $io->error($e->getMessage());

            return 1;
        }
        $io->success('Finished updating social stats.');

        return 0;
    }

    /**
     * Creates the http client.
     *
     * @return Client
     */
    private function setUpHttpClient()
    {
        return new Client([
            'base_url' => $this->baseUrl,
            'social_stats' => System::getContainer()->getParameter('social_stats'),
            'ssl.certificate_authority' => false,
        ]);
    }

    /**
     * Check and apply command options.
     *
     * @param InputInterface $input
     *
     * @throws ClassNotFoundException
     */
    private function applyOptions($input)
    {
        if ($input->getOption('debug-mode')) {
            $this->debug = $this->io;
            $this->io->note('Activated debug mode');
            $this->io->text('Base-Url: '.$this->baseUrl);
        }

        if ($articleId = $input->getOption('article')) {
            /**
             * @var NewsModel
             */
            $model = $this->framework->getAdapter(NewsModel::class);

            if ($model) {
                if ($items = $model->findMultipleByIds([$articleId])) {
                    $this->items = $items;
                    $this->io->note([
                        'Retriving stats only for article with id '.$articleId.'.',
                        'Other article related settings will be skipped.',
                    ]);

                    return;
                }

                $this->io->warning([
                        "No news article with id $articleId found.",
                        'Continue by ignoring article setting.',
                    ]);
            } else {
                throw new ClassNotFoundException('NewsModel could not be found (while applying article option).');
            }
        }

        if (1 == $input->getOption('no-chunksize')) {
            $this->config['chunksize'] = 0;
            $this->io->note('Ignoring chunksize.');
        }

        if (1 == $input->getOption('no-days')) {
            $this->config['days'] = 0;
            $this->io->note('Ignoring days config.');
        }

        if (1 == $input->getOption('only-current')) {
            /**
             * @var NewsModel
             */
            $model = $this->framework->getAdapter(NewsModel::class);

            if ($model) {
                $this->items = $model->findPublishedFromToByPids(0, time(), $this->config['archives'], $this->config['chunksize']);
            } else {
                throw new ClassNotFoundException('NewsModel could no be found (while applying only-current option).');
            }
            $this->io->note('Retriving stats for newest items.');
        }
    }

    private function updateGoogleAnalyticsCount()
    {
        $crawlerConfig = [
            'name' => 'Google Analytics',
            'alias' => 'google_analytics',
            'method' => 'findByGoogleAnalyticsUpdateDate',
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
     * Update Facebook stats.
     */
    private function updateFacebookCount()
    {
        $crawlerConfig = [
            'name' => 'Facebook',
            'alias' => 'facebook',
            'method' => 'findByFacebookCounterUpdateDate',
        ];
        $this->updateStats(
            new FacebookCrawler($this->httpClient),
            $crawlerConfig
        );
    }

    /**
     * Update Twitter stats.
     */
    private function updateTwitterCount()
    {
        $crawlerConfig = [
            'name' => 'Twitter',
            'alias' => 'twitter',
            'method' => 'findByTwitterCounterUpdateDate',
        ];
        $this->updateStats(
            new TwitterCrawler($this->httpClient, null, $this->baseUrl, $this->config['twitter']),
            $crawlerConfig
        );
    }

    /**
     * Update Google Plus stats.
     */
    private function updateGooglePlusCount()
    {
        $crawlerConfig = [
            'name' => 'Google Plus',
            'alias' => 'google_plus',
            'method' => 'findByGooglePlusCounterUpdateDate',
        ];
        $this->updateStats(
            new GooglePlusCrawler($this->httpClient),
            $crawlerConfig
        );
    }

    /**
     * Update Disqus stats.
     */
    private function updateDisqusCount()
    {
        $crawlerConfig = [
            'name' => 'Disqus',
            'alias' => 'disqus',
            'method' => 'findByDisqusCounterUpdateDate',
        ];
        $this->updateStats(
            new DisqusCrawler(
                $this->httpClient,
                null,
                $this->baseUrl,
                $this->config['disqus']),
            $crawlerConfig
        );
    }

    /**
     * @param AbstractCrawler $crawler
     * @param array           $crawlerConfig
     */
    private function updateStats(AbstractCrawler $crawler, array $crawlerConfig)
    {
        $crawler->setIo($this->io);

        if (!array_key_exists($crawlerConfig['alias'], $this->config)) {
            $this->io->note('No '.$crawlerConfig['name'].' config provided. Skipping...');

            return;
        }
        $this->io->section('Retriving '.$crawlerConfig['name'].' counts');

        if (!$this->items) {
            $method = $crawlerConfig['method'];
            $items = $this->framework->getAdapter(NewsModel::class)->$method(
                $this->config['chunksize'],
                $this->config['days'],
                $this->config['archives']
            );
        } else {
            $items = $this->items;
        }

        foreach ($items as $item) {
            $this->io->text('Updating news article '.$item->id.' ('.$item->headline.')');
            $crawler->setItem($item);
            $crawler->setBaseUrl($this->baseUrl);
            $crawler->setIo($this->debug);
            $count = $crawler->getCount();

            if (\is_array($count)) {
                $this->io->note('Error: '.$count['message']);
                $this->logger->addNotice($crawlerConfig['name'].': '.$count['message']);

                if (AbstractCrawler::ERROR_BREAKING == $count['code']) {
                    $this->io->note('Stopping updating stats for current provider.');

                    return;
                }

                continue;
            }
            $crawler->updateItem();
            $this->io->text('Found <bg=green;fg=white> '.$count.' </> shares on '.$crawlerConfig['name'].'.');
            $this->io->newLine();
        }
    }
}
