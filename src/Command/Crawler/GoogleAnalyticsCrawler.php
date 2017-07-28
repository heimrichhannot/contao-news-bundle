<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;

use TYPO3\CMS\Core\Utility\PathUtility;

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
        $this->init($most);
    }

    public function getCount($url)
    {
        if (!array_key_exists($url, $this->results))
        {
            return 0;
        }

        return $this->results[$url];
    }

    private function init($most)
    {
        $this->client->setApplicationName('test-dav-aa'); // name of your app

        $keyFile = PATH_site . ltrim($this->keyfile, '/');

        if (!file_exists($keyFile))
        {
            return false;
        }

        $key = file_get_contents($keyFile);

        // set assertion credentials
        $this->client->setAssertionCredentials(
            new \Google_Auth_AssertionCredentials(
                $this->serviceAccountEmail, // email you added to GA
                ['https://www.googleapis.com/auth/analytics.readonly'], $key // keyfile you downloaded
            )
        );

        // other settings
        $this->client->setClientId($this->serviceAccountClientId); // from API console
        $this->client->setAccessType('offline_access'); // this may be unnecessary?

        // create service and get data
        $this->service = new \Google_Service_Analytics($this->client);

        $this->fetchData($most);

    }

    public function fetchData($most)
    {
        if ($most)
        {
            $interval   = 3600 * 24 * 14; // latest 14 days - #4787
            $startdate  = date("Y-m-d", (time() - $interval));
            $maxResults = 10;
        }
        else
        {
            $startdate  = '2005-01-01'; // Startdate of ga profile
            $maxResults = 10000;
        }
        $result = $this->service->data_ga->get(
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

    public function getCountMost()
    {
        $views = [];
        foreach ($this->results as $url => $count)
        {
            $pathRaw = trim($url, "/");
            $pathArr = explode("/", $pathRaw);
            if (count($pathArr) > 4)
            {
                $uid         = intval($pathArr[(count($pathArr) - 2)]);
                $views[$uid] = intval($count);
            }
        }

        return $views;
    }

}

?>
