<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;


use Contao\NewsModel;
use GuzzleHttp\Exception\ClientException;

class DisqusCrawler extends AbstractCrawler
{
    private $config;
    private $forum      = null;
    private $apikey     = null;
    private $identifier = "";


    /**
     * DisqusCrawler constructor.
     * @param \GuzzleHttp\Client $client
     * @param \HeimrichHannot\NewsBundle\NewsModel $item
     * @param string $baseUrl
     * @param array $config
     */
    public function __construct($client, $item, $baseUrl, $config)
    {
        parent::__construct($client, $item, $baseUrl);
        $this->config     = $config;
        $this->apikey     = $config['public_api_key'];
        $this->forum      = $config['forum_name'];
        $this->identifier = str_replace('{id}', $item->id, $config['identifier']);
    }

    /**
     * Return comment count or error
     * @return int|array
     */
    public function getCount()
    {
        $count = 0;
        try {
            $response = $this->client->request(
                'GET',
                'https://disqus.com/api/3.0/threads/details.json?api_key=' . $this->apikey
                . '&forum=' . $this->forum . '&thread:ident=' . $this->identifier
            );
        } catch (ClientException $e)
        {
            $error = json_decode($e->getResponse()->getBody()->getContents());
            $this->setErrorMessage($error->response);
            if ($error->code == 2)
            {
                $this->setErrorCode(AbstractCrawler::ERROR_NOTICE);
            }
            return $this->error;
        }

        if ($response && $response->getStatusCode() == 200)
        {
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['response']) && isset($data['response']['posts']))
            {
                $count = intval($data['response']['posts']);
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
        $this->item->disqus_counter = $this->count;
        $this->item->disqus_updated_at = time();
        $this->item->save();
    }

    /**
     * @param NewsModel $item
     */
    public function setItem(NewsModel $item)
    {
        parent::setItem($item);
        $this->identifier = str_replace('{id}', $item->id, $this->config['identifier']);
    }





//    public function getCountMost($interval = '30d', $limit = 25)
//    {
//        $response = $this->client->request(
//            'GET',
//            'https://disqus.com/api/3.0/threads/listPopular.json?api_key=' . $this->apikey . '&forum=' . $this->forum . '&interval=' . $interval . '&limit=' . $limit
//        );
//
//        $posts = [];
//
//        if ($response->getStatusCode() == 200)
//        {
//            $data = json_decode($response->getBody()->getContents(), true);
//
//            if (!is_array())
//            {
//                return $posts;
//            }
//
//            foreach ($data['response'] as $thread)
//            {
//                $identifiers = $thread['identifiers'];
//                $identifier  = $identifiers[count($identifiers) - 1];
//                $parts       = explode("-", $identifier);
//                if (count($parts) == 3)
//                { // format: news-uid-12
//                    $uid         = intval($parts[2]);
//                    $posts[$uid] = intval($thread['posts']);
//                }
//                if (count($parts) == 2)
//                { // format: uid-12
//                    $uid         = intval($parts[1]);
//                    $posts[$uid] = intval($thread['posts']);
//                }
//            }
//        }
//
//        return $posts;
//    }
}

?>
