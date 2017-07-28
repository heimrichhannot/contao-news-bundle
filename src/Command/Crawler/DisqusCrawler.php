<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;


class DisqusCrawler extends AbstractCrawler
{
    private $forum      = null;
    private $apikey     = null;
    private $identifier = "";

    public function __construct($client, $url, $apikey, $forum, $identifier = "")
    {
        parent::__construct($client, $url);
        $this->apikey     = $apikey;
        $this->forum      = $forum;
        $this->identifier = $identifier;
    }

    public function getCount($url = null)
    {
        $response = $this->client->request(
            'GET',
            'https://disqus.com/api/3.0/threads/details.json?api_key=' . $this->apikey . '&forum=' . $this->forum . '&thread:ident=' . $this->identifier
        );

        $count = 0;

        if ($response->getStatusCode() == 200)
        {
            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['response']) && isset($data['response']['posts']))
            {
                $count = intval($data['response']['posts']);
            }
        }

        return $count;
    }

    public function getCountMost($interval = '30d', $limit = 25)
    {
        $response = $this->client->request(
            'GET',
            'https://disqus.com/api/3.0/threads/listPopular.json?api_key=' . $this->apikey . '&forum=' . $this->forum . '&interval=' . $interval . '&limit=' . $limit
        );

        $posts = [];

        if ($response->getStatusCode() == 200)
        {
            $data = json_decode($response->getBody()->getContents(), true);

            if (!is_array())
            {
                return $posts;
            }

            foreach ($data['response'] as $thread)
            {
                $identifiers = $thread['identifiers'];
                $identifier  = $identifiers[count($identifiers) - 1];
                $parts       = explode("-", $identifier);
                if (count($parts) == 3)
                { // format: news-uid-12
                    $uid         = intval($parts[2]);
                    $posts[$uid] = intval($thread['posts']);
                }
                if (count($parts) == 2)
                { // format: uid-12
                    $uid         = intval($parts[1]);
                    $posts[$uid] = intval($thread['posts']);
                }
            }
        }

        return $posts;
    }
}

?>
