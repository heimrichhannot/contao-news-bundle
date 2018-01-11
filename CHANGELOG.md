# Changelog
All notable changes to this project will be documented in this file.

## [1.4.1] - 2018-01-11

#### Fixed
- bug resulting in not resetting google analytics counter between articles

## [1.4.0] - 2018-01-11

#### Added
- socialstats: article option to get stats for a single article

#### Changed
- socialstats: better readable debug output1

#### Fixed
- socialstats: returned only last url count from google analytics

## [1.3.1] - 2017-12-15

#### Fixed
- socialstats: no debug output for disqus
- socialstats: umlauts etc. not handled in urls
- socialstats: wrong method call

## [1.3.0] - 2017-12-15

#### Added
- socialstats: debug-option to command
- socialstats: console print informations about set options

#### Changed
- marked CrawlerInterface deprecated

## [1.2.0] - 2017-12-14

#### Added
- socialstats: only-current parameter to update only current news

#### Changed
- socialstats: better url handling for google analytics crawler
- socialstats: optimized command code
- socialstats: removed some debug code

#### Fixed
- socialstats: google analytics not crawled
- socialstats: nolimit param not working

## [1.1.1] - 2017-12-13

#### Changed
- drop old bootstrapper datetimepicker connection, made compatible with new flatpickr 

## [1.1.0] - 2017-12-12

#### Changed
- rewrote parts of social stats command due better handling of updated and new news entries and better command output

#### Fixed
- error in social stats command documentation for archive

## [1.0.9] - 2017-12-06

#### Fixed
- `hasWriters` template variable if member model is empty

## [1.0.8] - 2017-12-06

#### Added
- tags filter within backend news list 

## [1.0.7] - 2017-12-06

#### Fixed
- `hasRatings` template variable check

## [1.0.6] - 2017-11-30

#### Fixed
- added tl_class clr to infoBox_linkText and fixed typo

## [1.0.5] - 2017-11-29

#### Added
- when in `\HeimrichHannot\NewsBundle\Backend\NewsList::MODE_AUTO_ITEM` mode, set module headline from news list title

## [1.0.4] - 2017-11-29

#### Fixed
- wrong palette replacement in `tl_user_group`
- wrong palette replacement in `tl_user`

## [1.0.3] - 2017-11-29

#### Fixed
- permission handling for `tl_news_list_archive` (typo)

## [1.0.2] - 2017-11-28

### Fixed
* checkbox was to greedy (used a varchar(255) instead of char(1) to save it's value) -> needs database update to affect

## [1.0.1] - 2017-11-24

### Fixed

- `og:url` did not recognize the news category url (based on primary category), instead used the default news archive jumpTo page
