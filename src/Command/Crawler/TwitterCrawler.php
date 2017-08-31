<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;


use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterCrawler extends AbstractCrawler
{
    /**
     * @var TwitterOAuth
     */
    private $connection;

    /**
     * TwitterCrawler constructor.
     * @param \GuzzleHttp\Client $client
     * @param \HeimrichHannot\NewsBundle\NewsModel $item
     * @param $baseUrl
     * @param $config Must contain following keys: consumer_key, consumer_secret, access_token, access_token_secret
     */
    public function __construct($client, $item, $baseUrl, $config)
    {
        parent::__construct($client, $item, $baseUrl);
        $this->connection = new TwitterOAuth(
            $config['consumer_key'],
            $config['consumer_secret'],
            $config['access_token'],
            $config['access_token_secret']
        );
    }

    /**
     * Return share count or error.
     * Twitter allows only search within the last seven day,
     * so share count only includes shares from the last seven days.
     *
     * @return int|array
     */
    public function getCount()
    {
        $count = 0;
        foreach ($this->getUrls() as $url)
        {
            $response = $this->connection->get("search/tweets", [
                "q" => 'url:'.$url,
                "count" => 100
            ]);
            if ($errors = $response->errors)
            {
                $this->setErrorCode(static::ERROR_BREAKING);
                $this->setErrorMessage($errors[0]->message);
                return $this->error;
            } else
            {
                $count += count($response->statuses);
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
        $this->item->twitter_counter = $this->count;
        $this->item->twitter_updated_at = time();
        $this->item->save();
    }
}

?>
