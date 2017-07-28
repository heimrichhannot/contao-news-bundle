<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;

class GooglePlusCrawler extends AbstractCrawler
{
    public function __construct($client, $url)
    {
        parent::__construct($client, $url);
    }
    
    public function getCount($url = null)
    {
		$body = '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $this->url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]';
		$response = $this->client->request('POST', 'https://clients6.google.com/rpc', ['body' => $body]);

		$count = 0;

		if($response->getStatusCode() == 200)
		{
			$data = json_decode($response->getBody()->getContents(), true);

			if(isset($data[0]['result']['metadata']['globalCounts']['count']))
			{
				$count = intval($data[0]['result']['metadata']['globalCounts']['count']);
			}
		}

		return $count;
    }
}

?>
