# Changelog
All notable changes to this project will be documented in this file.

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
