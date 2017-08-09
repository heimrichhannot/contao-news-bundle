<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;

/**
 * Class GoogleAnalyticsCrawler
 *
 * @package HeimrichHannot\NewsBundle\Command\Crawler
 */
class GoogleAnalyticsCrawler extends AbstractCrawler
{
    private $service                = null;
    private $results                = [];
    private $keyfile                = null; // 3e87b7c8ae683f264264779bf135cc613e516737-privatekey.p12
    private $serviceAccountEmail    = null; // '486021245191@developer.gserviceaccount.com'
    private $serviceAccountClientId = null; // '486021245191.apps.googleusercontent.com'
    private $gaProfileId            = null;
    private $gaAccountId            = null;

    public function __construct($client, $keyfile, $serviceAccountEmail, $serviceAccountClientId, $gaProfileId, $gaAccountId, $most = false)
    {
        parent::__construct($client, null);
        $this->keyfile                = $keyfile;
        $this->serviceAccountEmail    = $serviceAccountEmail;
        $this->serviceAccountClientId = $serviceAccountClientId;
        $this->gaProfileId            = $gaProfileId;
        $this->gaAccountId            = $gaAccountId;
        $this->init();
    }

    public function getCount($url)
    {
        if (!array_key_exists($url, $this->results))
        {
            return 0;
        }

        return $this->results[$url];
    }

    private function init()
    {
        $this->client->setApplicationName('test-dav-aa'); // name of your app

        $keyFile = '/home/kwagner/Kunden/dav/anwaltauskunft/produkte/contao/files/e1bc8698bc015b6bd01001951103eed7a1bad8b3-privatekey.p12';

        if (!file_exists($keyFile))
        {
            return false;
        }

        $key = file_get_contents($keyFile);

        $this->client->setDeveloperKey($key);
        $this->client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

        // other settings
        $this->client->setClientId($this->serviceAccountClientId); // from API console

        // create service and get data
        $this->service = new \Google_Service_Analytics($this->client);

        $this->fetchData();

    }

    public function fetchData()
    {
        $startdate  = '2005-01-01'; // Startdate of ga profile
        $maxResults = 10000;
        $result     = $this->service->data_ga->get(
            'ga:' . $this->gaProfileId,
            $startdate,
            date("Y-m-d"),
            'ga:pageviews',
            [
                'filters'     => 'ga:pagePath=~/magazin/(.*)/(.*)/([0-9]*)/(.*)/$',
                'dimensions'  => 'ga:pagePath,ga:hostname',
                'metrics'     => 'ga:pageviews',
                'max-results' => $maxResults,
                'sort'        => '-ga:pageviews',
            ]
        );

        foreach ($result['rows'] as $row)
        {
            $url                 = 'https://' . $row[1] . $row[0];
            $this->results[$url] = $row[2];
        }
    }
}

?>
