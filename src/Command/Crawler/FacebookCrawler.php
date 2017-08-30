<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class FacebookCrawler extends AbstractCrawler
{
    /**
     * FacebookCrawler constructor.
     * @param $client Client
     * @param $item
     * @param $baseUrl
     */
    public function __construct($client, $item = null, $baseUrl = '')
    {
        parent::__construct($client, $item, $baseUrl);
    }

    /**
     * Return share count or error message
     * @param null $url
     * @return int|string
     */
    public function getCount($url = null)
    {
        $count = 0;
        foreach ($this->getUrls() as $url)
        {
            try {
                $response = $this->client->request('GET', 'https://graph.facebook.com/?id=' . $url);
            } catch (ClientException $e)
            {
                $error = json_decode($e->getResponse()->getBody()->getContents());
                return $error->error->message;
            }

            if ($response && $response->getStatusCode() == 200)
            {
                $data = json_decode($response->getBody()->getContents(), true);

                if ($data['id'] == $url)
                {
                    $count += intval($data['share']['share_count']);
                }
            }
        }
        $this->count = $count;
        return $count;
    }

    /**
     * Update the current item
     */
    public function updateItem()
    {
        $this->item->facebook_counter = $this->count;
        $this->item->facebook_updated_at = time();
        $this->item->save();
    }
}

?>
