# Changelog
All notable changes to this project will be documented in this file.

## [4.1.2] - 2023-09-07
- Fixed: php8 warning: undefined array key `deleteConfirm`

## [4.1.1] - 2022-06-09
- Fixed: warning with php 8 and dc_multilingual

## [4.1.0] - 2022-06-02
- Removed: social stats ([#3])

## [4.0.1] - 2022-05-18
- Fixed: invalid hook registration

## [4.0.0] - 2022-05-05
- Changed: removed codefog/tags-bundle integration
- Changed: allow symfony 5
- Changed: allow php 8
- Changed: removed test setup

## [3.0.0-beta16] - 2021-06-21

- fixed terminal42/changelanguage incompatibility

## [3.0.0-beta15] - 2021-06-09

- contao 4.9 compatibility

## [3.0.0-beta14] - 2020-06-12

- contao 4.9 compatibility

## [3.0.0-beta13] - 2020-01-16

- fixed model retrieval

## [3.0.0-beta12.2] - 2019-11-26

- fixed backend controller dependency in NewsArchive.php

## [3.0.0-beta12.1] - 2019-11-13

- fixed news-list-reader-bundle dependency

## [3.0.0-beta12.0] - 2019-11-13

- refactored List and Reader Item into own bundle and added it as dependency

## [3.0.0-beta11] - 2019-10-23

- fixed callback issues

## [3.0.0-beta10] - 2019-10-22

- updated dependencies
- migrated reader config element type to ReaderConfigElementTypeInterface

## [2.4.3] - 2018-06-04

#### Fixed
- fixed instance of namespace in NewsArticle.php

## [2.4.2] - 2018-04-03

#### Changed
- `ModuleNewsListRelated` fallback for current news from `auto_item`

## [2.4.1] - 2018-03-21

#### Fixed
- shareUrls null

## [2.4.0] - 2018-03-21

#### Added
- support for contao-share shareUrls template variable

## [2.3.0] - 2018-03-12

#### Changed
- utils bundle 2.0 dependency

## [2.2.12] - 2018-03-12

#### Changed
- Utils bundle dependency

## [2.2.11] - 2018-03-02

#### Added
- custom `robots` tag for news articles, can be set for each article (`default: index,follow` even if parent page has for example, `noindex,follow`)

## [2.2.10] - 2018-03-02

#### Changed
- `heimrichhannot/contao-utils` bundle dependency changed from `dev-master` to `^1.0`

## [2.2.9] - 2018-03-02

#### Changed
- removed tl_member dca fields title and organization, use headline and company instead with `heimrichhannot/contao-member_plus`

## [2.2.8] - 2018-02-19

#### Changed
- removed CfgTagModel and added utils-bundle dep

## [2.2.7] - 2018-02-06

#### Changed
- refactored module callback class to bundle structure

## [2.2.6] - 2018-01-24

#### Changed
- `tl_news.categories` should be non mandatory

## [2.2.5] - 2018-01-19

#### Changed
- licence LGPL-3.0+ is now LGPL-3.0-or-later

## [2.2.4] - 2018-01-15

#### Added
- added CategoriesBundle to `setLoadAfter`

## [2.2.3] - 2018-01-12

#### Added
- added linkedMember to tl_news. link a member to a news

## [2.2.2] - 2018-01-12

#### Fixed
- removed old news categories code from ModuleNewsReader

## [2.2.1] - 2018-01-11

#### Fixed 
- bug resulting in not resetting google analytics counter between articles

## [2.2.0] - 2018-01-11

#### Added
- socialstats: article option to get stats for a single article

#### Changed
- socialstats: better readable debug output1

#### Fixed
- socialstats: returned only last url count from google analytics


## [2.1.0] - 2018-01-05

#### Added
- add a custom news palettes to tl_news and overwrite the default palettes for selected news archives

## [2.0.0] - 2017-12-18

#### Changed
- using "heimrichhannot/contao-categories-bundle"

## [1.3.1] - 2017-12-15

#### Fixed
- socialstats: no debug output for disqus
- socialstats: umlauts etc. mot handled in urls
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
- checkbox was to greedy (used a varchar(255) instead of char(1) to save it's value) -> needs database update to affect

## [1.0.1] - 2017-11-24

### Fixed

- `og:url` did not recognize the news category url (based on primary category), instead used the default news archive jumpTo page


[#3]: https://github.com/heimrichhannot/contao-news-bundle/pull/3
