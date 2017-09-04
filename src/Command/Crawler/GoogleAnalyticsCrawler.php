<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;

use HeimrichHannot\NewsBundle\Model\NewsModel;
use Contao\System;
use Google_Client;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Metric;

/**
 * Class GoogleAnalyticsCrawler
 *
 * @package HeimrichHannot\NewsBundle\Command\Crawler
 */
class GoogleAnalyticsCrawler extends AbstractCrawler
{
    /**
     * @var string
     */
    private $viewId;

    /**
     * @var \Google_Client
     */
    protected $client;
    /**
     * @var \Google_Service_Analytics
     */
    private $analytics;

    /**
     * GoogleAnalyticsCrawler constructor.
     * @param \GuzzleHttp\Client $client
     * @param  NewsModel $item
     * @param string $baseUrl
     * @param $config
     */
    public function __construct($client, $item = null, $baseUrl = '', $config)
    {
        parent::__construct($client, $item, $baseUrl);

        $keyFile = System::getContainer()->getParameter('kernel.root_dir') . '/..';
        $keyFile .= '/' . $config['keyfile'];
        if (!file_exists($keyFile)) {
            return false;
        }
        $client = new Google_Client();
        $client->setApplicationName('Anwaltauskunft Social Stats');
        $client->setAuthConfig($keyFile);
        $client->addScope(['https://www.googleapis.com/auth/analytics.readonly']);
        $analytics = new Google_Service_AnalyticsReporting($client);

        $this->client    = $client;
        $this->analytics = $analytics;
        $this->viewId    = $config['view_id'];
    }

    /**
     * Returns the unique visitors count or error.
     * @return array|int
     */
    public function getCount()
    {
        $count = 0;
        if (empty($urls = $this->getUrls())) {
            return $count;
        }
//        $url = '/magazin/leben/freizeit-alltag/2115/tierquaelerei-wie-zeugen-helfen-koennen/';
        foreach ($urls as $url) {

            $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
            $body->setReportRequests([$this->prepareRequest($url)]);

            try {
                $responce = $this->analytics->reports->batchGet($body);
            } catch (\Google_Service_Exception $e) {
                $this->setErrorCode(static::ERROR_BREAKING);
                $this->setErrorMessage($e->getMessage());
                return $this->getError();
            }

            $report = $responce[0];
            $rows   = $report->getData()->getRows();
            for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                $row     = $rows[$rowIndex];
                $metrics = $row->getMetrics();
                $values  = $metrics[0]->getValues();
                $count   += $values[0];
            }
        }
        $this->count = $count;
        return $count;
    }

    public function prepareRequest($url)
    {
        $dataRange = new \Google_Service_AnalyticsReporting_DateRange();
        $dataRange->setStartDate('2005-01-01');
        $dataRange->setEndDate(date("Y-m-d"));

        $metric = new Google_Service_AnalyticsReporting_Metric();
        $metric->setExpression("ga:uniquePageviews");

        $dimension = new \Google_Service_AnalyticsReporting_Dimension();
        $dimension->setName('ga:pagePath');

        $dimensionFilter = new \Google_Service_AnalyticsReporting_DimensionFilter();
        $dimensionFilter->setDimensionName('ga:pagePath');
        $dimensionFilter->setExpressions([$url]);

        $dimensionFilterClause = new \Google_Service_AnalyticsReporting_DimensionFilterClause();
        $dimensionFilterClause->setFilters([$dimensionFilter]);

        $request = new \Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($this->viewId);
        $request->setDateRanges($dataRange);
        $request->setMetrics([$metric]);
        $request->setDimensions($dimension);
        $request->setDimensionFilterClauses($dimensionFilterClause);
        return $request;


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

    /**
     * Update the current item
     */
    public function updateItem()
    {
        $this->item->google_analytic_counter    = $this->count;
        $this->item->google_analytic_updated_at = time();
        $this->item->save();
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl(string $baseUrl)
    {
        parent::setBaseUrl('');
    }


}

?>
