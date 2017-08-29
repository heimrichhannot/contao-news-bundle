<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;

use GuzzleHttp\Client;

class FacebookCrawler extends AbstractCrawler
{
    /**
     * FacebookCrawler constructor.
     * @param $client Client
     * @param $url
     */
    public function __construct($client, $url)
    {
        parent::__construct($client, $url);
    }

    public function getCount($url = null)
    {
        $response = $this->client->request('GET', 'https://graph.facebook.com/?id=' . $this->url);
        $count    = 0;

        if ($response->getStatusCode() == 200)
        {
            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['id'] == $this->url)
            {
                $count = intval($data['share']['share_count']);
            }
        }
        return $count;
    }
}

?>
