# Contao-News-Bundle

## Features
* dynamic rss feed generation
* ...

## Requires
* Contao 4.4
* PHP7

## Usage

### Dynamic rss feed

The dynamic rss feed is based on feed sources and their channels. Feed sources can be categories, tags, etc. A channel can be a single categorie, a single tag, e.g.

#### Register a feed source
1. Your class must implement `FeedSourceInterface`
2. Create a service for your class and add the `news-bundle.feed_source` tag.
3. Create a new feed in the Contao-Backend (News->Create Feed) and select dynamic feed.

```
// Example for codefog/tags-bundle
// services.yml

HeimrichHannot\NewsBundle\Component\TagFeedSource:
        tags: [news-bundle.feed_source]
```

The bundle will add following routes for your feed source:
* /share/[feedAlias|feedId]
* /share/[feddAlias|feedId]/[channelId|channelAlias]