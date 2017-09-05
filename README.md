# Contao News Bundle

This module contains enhancements for the contao news entity. It works with Contao 4 (and higher).

## Features

- define "news lists" for filtering the news to be displayed in the ordinary NewsList module

## Technical instructions

### Add tag filtered websites to sitemap and/or search index

This feature is useful e.g. if you have a tag filtered list module on some website and want to have this site containing all tags as auto_item in the sitemap and/or search index.
You can do this by setting a jumpTo page for the desired tag source in the global contao settings (tl_settings) in the tags section.

### Social stats

Searches _Facebook_, _Twitter_ and _Google Plus_ for share counts. Also count number of comments on _Disqus_ and number of visitors by _Google Analytics_.
To use is, just call `huh.news.socialstats` from a cronjob periodically.

Full available config for your `config.yml`:

```
social_stats:
  chunksize: 20 # default value is 20
  archives: [1,2] # news archive ids. only news in given archives are updated. Default: empty (all archives)
  disqus:
    public_api_key: MYPUBLICKEY
    forum_name: my_shortname
    identifier: news-uid-{id} # {id} is replaced with news id. Default value is {id}
  google_analytics:
    email: example@developer.gserviceaccount.com # service account email
    key_id: 123456abcdef # service account key id
    client_id: my_client_id.apps.googleusercontent.com # oauth client id
    client_key: ABCD1234 # oauth client key
    view_id: ga:12345678 # view id
    api_key: MYAPIKEY
    keyfile: files/newsbundle/socialstats/google_analytics/privatekey.json # relative path to keyfile from project root, default is the given value
  twitter:
    consumer_key: MYCONSUMERKEY
    consumer_secret: myConsumerSecret
    access_token: MYACCESSTOKEN
    access_token_secret: MYACCESSTOKENSECRET
  facebook: # no value needed
  google_plus: # no value needed
```
To deactivate a plattform, don't set settings. 

You can scan for more urls than the default one, if you use the `addNewsArticleUrlsToSocialStats` Hook (for example if you need to scan for legacy urls due plattform change).

> Because twitter search api is limited, you can only count for shares of last seven days


### Modules

Name | Description
---- | -----------
...

### Insert Tags

Name | Description | Arguments | Example
---- | ----------- | --------- | -------
news_list | prints the link to a certain news list | id or alias of a news list | {{news_list::1}}
news_list_url | prints the url to a certain news list | id or alias of a news list | {{news_list_url::1}}
news_list_title | prints the title of a certain news list | id or alias of a news list | {{news_list_title::1}}