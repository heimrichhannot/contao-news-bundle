<?php
namespace  HeimrichHannot\NewsBundle\Command\Crawler;

class FacebookCrawler extends AbstractCrawler
{
    public function __construct($client, $url)
    {
        parent::__construct($client, $url);
    }
    
    public function getCount($url = null)
    {
		$response = $this->client->request('GET', 'https://graph.facebook.com/?id=' . $this->url);

		$count = 0;

		if($response->getStatusCode() == 200)
		{
			$data = json_decode($response->getBody()->getContents(), true);

			if($data['id'] == $this->url)
			{
				$count = intval($data['shares']);
			}
		}

        return $count;
    }
}

?>
