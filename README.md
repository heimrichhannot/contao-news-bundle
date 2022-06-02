 # Contao News Bundle
 
> This bundle is deprecated! Don't use is anymore. Version 4 is intended to removed feature one by one.
> New minor versions will remove features, so stick to bugfix versions if you need to use version 4.
> 
> - Version 4.1: Removed social stats ([#3])
> - Version 4.0: Removed codefog/tags-bundle support

This module contains enhancements for the contao news entity. It works with Contao 4 (and higher).

## Replacements:
- List-/Readersupport: [Contao News List Reader Bundle](https://github.com/heimrichhannot/contao-news-list-reader-bundle)
- Social stats: [Social Stats Bundle](https://github.com/heimrichhannot/contao-social-stats-bundle)


## Features
* [List Bundle](https://github.com/heimrichhannot/contao-list-bundle) and [Reader Bundle](https://github.com/heimrichhannot/contao-reader-bundle) support through [Contao News List Reader Bundle](https://github.com/heimrichhannot/contao-news-list-reader-bundle)
* news navigation
* custom palettes for news archives

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

### News navigation

To activate news navigation, you need to create a `newsnavigation` module and configure it with a `newslist` module. And you need to add set the `newsnavigation` module in the `newsreader` module. Don't add the module to an article, as this won't work, instead a template variable with the navigation is added to the news article template.

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

## More

### Extension
[Contao News Leisure Bundle](https://github.com/heimrichhannot/contao-news-leisure-bundle) - Add Leisure tipps to news



[#3]: https://github.com/heimrichhannot/contao-news-bundle/pull/3