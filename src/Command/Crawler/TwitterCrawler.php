<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;


class TwitterCrawler extends AbstractCrawler
{
    public function __construct($client, $url)
    {
        parent::__construct($client, $url);
    }

    /**
     * @deprecated Twitter share count api has been shut down
     *
     * @param null $url
     *
     * @return int
     */
    public function getCount($url = null)
    {
//        return 0;

        $this->url = 'https://anwaltauskunft.de/magazin/beruf/bildung-ausbildung/2113/bewerbung-bei-der-polizei-wer-darf-ordnungshueter-werden/';

//        $response = $this->client->request('GET', 'http://urls.api.twitter.com/1/urls/count.json?url=' . $this->url);
        $response = $this->client->request(
            'GET',
            'https://api.twitter.com/1.1/search/tweets.json?q=https://anwaltauskunft.de/magazin/beruf/bildung-ausbildung/2113/bewerbung-bei-der-polizei-wer-darf-ordnungshueter-werden/'
        );

        $count = 0;

        if ($response->getStatusCode() == 200)
        {
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['count']))
            {
                $count = intval($data['count']);
            }
        }

        return $count;
    }

}

?>
