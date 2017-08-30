<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;

use Contao\System;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Metric;

/**
 * Class GoogleAnalyticsCrawler
 *
 * @package HeimrichHannot\NewsBundle\Command\Crawler
 */
class GoogleAnalyticsCrawler implements CrawlerInterface
{

    private $email;
    private $keyId;
    private $clientId;
    private $clientKey;
    private $viewId;
    private $keyFile;
    /**
     * @var \Google_Client
     */
    private $client;
    /**
     * @var \Google_Service_Analytics
     */
    private $analytics;

    /**
     * GoogleAnalyticsCrawler constructor.
     * @param $config array
     */
    public function __construct($config)
    {
        $this->email = $config['email'];
        $this->keyId = $config['key_id'];
        $this->clientId = $config['client_id'];
        $this->clientKey = $config['client_key'];
        $this->viewId = $config['view_id'];
        $this->keyFile = $config['keyfile'];
        $this->client = new \Google_Client();
        $this->init();
        $this->fetchData();
    }

    public function init ()
    {
        $keyFile = System::getContainer()->getParameter('kernel.root_dir').'/..';
        $keyFile .= '/'.$this->keyFile;
        if (!file_exists($keyFile))
        {
            return false;
        }
        $privateKey = file_get_contents($keyFile);
        $this->client->setApplicationName('Social Stats'); // name of your app
        $credentials = new \Google_Auth_AssertionCredentials(
            $this->email,
            ['https://www.googleapis.com/auth/analytics.readonly'],
            $privateKey);
        $this->client->setAssertionCredentials($credentials);
        $this->client->setClientId($this->clientId);
        $this->analytics = new \Google_Service_Analytics($this->client);
    }

    public function getCount($url)
    {
        if (!array_key_exists($url, $this->results))
        {
            return 0;
        }

        return $this->results[$url];
    }

    public function fetchData()
    {
//        $dataRange = new \Google_Service_AnalyticsReporting_DateRange();
//        $dataRange->setStartDate('2005-01-01');
//        $dataRange->setEndDate(date("Y-m-d"));
//
//        $pageViews = new Google_Service_AnalyticsReporting_Metric();
//        $pageViews->setExpression("ga:pageviews");
//        $pageViews->setAlias("pageviews");
//
//        $request = new \Google_Service_AnalyticsReporting_ReportRequest();
//        $request->setViewId($this->viewId);
//        $request->setDateRanges($dataRange);
//        $request->setMetrics([$pageViews]);
//
//        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
//        $body->setReportRequests( array( $request) );
//        $responce = $this->analytics->reports->batchGet($body);



//        $startdate  = '2005-01-01'; // Startdate of ga profile
//        $maxResults = 10000;
//        $result     = $this->service->data_ga->get(
//            'ga:' . $this->gaProfileId,
//            $startdate,
//            date("Y-m-d"),
//            'ga:pageviews',
//            [
//                'filters'     => 'ga:pagePath=~/magazin/(.*)/(.*)/([0-9]*)/(.*)/$',
//                'dimensions'  => 'ga:pagePath,ga:hostname',
//                'metrics'     => 'ga:pageviews',
//                'max-results' => $maxResults,
//                'sort'        => '-ga:pageviews',
//            ]
//        );
//
//        foreach ($result['rows'] as $row)
//        {
//            $url                 = 'https://' . $row[1] . $row[0];
//            $this->results[$url] = $row[2];
//        }
    }
}

?>
