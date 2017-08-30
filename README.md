# Contao News Bundle

This module contains enhancements for the contao news entity. It works with Contao 4 (and higher).

## Features

- define "news lists" for filtering the news to be displayed in the ordinary NewsList module

## Technical instructions

### Add tag filtered websites to sitemap and/or search index

This feature is useful e.g. if you have a tag filtered list module on some website and want to have this site containing all tags as auto_item in the sitemap and/or search index.
You can do this by setting a jumpTo page for the desired tag source in the global contao settings (tl_settings) in the tags section.

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