 # Contao News Bundle

This module contains enhancements for the contao news entity. It works with Contao 4 (and higher).

## Features

### News lists

- prevent duplicate content for news lists with multiple pages by setting `<meta name="robots" content="noindex/follow">` an all pages except first and adding `<link rel="prev" href="URL">` and/or `<link rel="next" href="URL">` of next or prev page exists (See: [Indicate paginated content
](https://support.google.com/webmasters/answer/1663744?hl=en))
- define "news lists" for filtering the news to be displayed in the ordinary NewsList module (new back end entity)

### Info box

An info box is a widget that can be rendered within a news article. 
The default info box comes with headline, text (wysiwyg), hyperlink and hyperlink text fields.
Use `##news_info_box##` within your news content elements / details (tl_content) to render the box content.

### News navigation

Navigate between news articles. You can go to the next (newer) or the previous (older) article direct from a news article. Respect filters set by the user.

## Technical instructions

### Add tag filtered websites to sitemap and/or search index

This feature is useful e.g. if you have a tag filtered list module on some website and want to have this site containing all tags as auto_item in the sitemap and/or search index.
You can do this by setting a jumpTo page for the desired tag source in the global contao settings (tl_settings) in the tags section.

### News navigation

To activate news navigation, you need to create a `newsnavigation` module and configure it with a `newslist` module. And you need to add set the `newsnavigation` module in the `newsreader` module. Don't add the module to an article, as this won't work, instead a template variable with the navigation is added to the news article template.

### Social stats

Searches _Facebook_, _Twitter_ and _Google Plus_ for share counts. Also count number of comments on _Disqus_ and number of visitors by _Google Analytics_.
To use is, just call `huh.news.socialstats` from a cronjob periodically.

Full available config for your `config.yml`:

```yml
social_stats:
  chunksize: 20 #max number of articles per job
  days: 180 #max age of articles in days
  archives: 0 #news archive ids. only news in given archives are updated. Example: [1,2]. 0 means all archives.
  disqus:
    public_api_key: 
    forum_name: 
    identifier: {id} #{id} is replaced with news id. Example: news-uid-{id}
  google_analytics:
    email: #service account email
    key_id: #service account key id
    client_id: #oauth client id
    client_key: #oauth client key
    view_id: #view id
    api_key: 
    keyfile: files/newsbundle/socialstats/google_analytics/privatekey.json #relative path to keyfile from project root
  twitter:
    consumer_key: 
    consumer_secret: 
    access_token: 
    access_token_secret: 
  facebook: #no value needed, just set to activate
  google_plus: #no value needed, just set to activate
```
To deactivate a plattform, don't set settings. Default values are given

You can scan for more urls than the default one, if you use the `addNewsArticleUrlsToSocialStats` Hook (for example if you need to scan for legacy urls due plattform change).

> Because twitter search api is limited, you can only count for shares of last seven days


### Modules

Name           | Description
-------------- | -----------
newsnavigation | Create the news navigation. Respect filters set by the `newslist_filter` module. To be used in combination with `newslist` module and `newsreader` module.

### Insert Tags

Name | Description | Arguments | Example
---- | ----------- | --------- | -------
news_list | prints the link to a certain news list | id or alias of a news list | {{news_list::1}}
news_list_url | prints the url to a certain news list | id or alias of a news list | {{news_list_url::1}}
news_list_title | prints the title of a certain news list | id or alias of a news list | {{news_list_title::1}}
