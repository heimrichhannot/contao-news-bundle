<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;

use GuzzleHttp\Exception\ClientException;

class GooglePlusCrawler extends AbstractCrawler
{

    /**
     * GooglePlusCrawler constructor.
     * @param \GuzzleHttp\Client $client
     * @param \HeimrichHannot\NewsBundle\Model\NewsModel $item
     * @param $baseUrl
     */
    public function __construct($client, $item = null, $baseUrl = '')
    {
        parent::__construct($client, $item, $baseUrl);
    }

    /**
     * Return share count or error message
     * @return int|array
     */
    public function getCount()
    {
        $this->count = 0;
        $count = 0;
        foreach ($this->getUrls() as $url)
        {
            $body = '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]';
            try {
                $response = $this->client->request('POST', 'https://clients6.google.com/rpc', ['body' => $body]);
            } catch (ClientException $e)
            {
                $this->setErrorCode(static::ERROR_BREAKING);
                $this->setErrorMessage($e->getResponse()->getBody()->getContents());
                return $this->error;
            }

            if($response->getStatusCode() == 200)
            {
                $data = json_decode($response->getBody()->getContents(), true);

                if(isset($data[0]['result']['metadata']['globalCounts']['count']))
                {
                    $count+= intval($data[0]['result']['metadata']['globalCounts']['count']);
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
        $this->item->google_plus_counter = $this->count;
		$this->item->google_plus_updated_at = time();
        $this->item->save();
    }

}

?>
