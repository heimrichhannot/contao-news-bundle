# Changelog
All notable changes to this project will be documented in this file.

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
