<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Item;

use Contao\Controller;
use Contao\Date;
use Contao\Environment;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;

class DefaultReaderItem extends \HeimrichHannot\ReaderBundle\Item\DefaultItem
{
    use NewsItemTrait;
    const SESSION_SEEN_NEWS = 'SESSION_SEEN_NEWS';

    public function parse(): string
    {
        $this->addPageMeta();
        $this->getManager()->setMetaDescription();
        $this->addSeen();

        return parent::parse();
    }

    /**
     * Add news to list of already seen for current page.
     */
    protected function addSeen()
    {
        global $objPage;

        $pages = System::getContainer()->get('session')->get(static::SESSION_SEEN_NEWS);

        if (!\is_array($pages)) {
            $pages = [];
        }

        $pages[$objPage->id][$this->id] = $this->id;

        System::getContainer()->get('session')->set(static::SESSION_SEEN_NEWS, $pages);
    }

    /**
     * Add page meta information.
     */
    protected function addPageMeta()
    {
        global $objPage;
        $container = System::getContainer();
        $article = $this->getRaw();

        $image = $this->getFormattedValue('singleSRC');

        if (Validator::isBinaryUuid($image)) {
            $image = System::getContainer()->get('huh.utils.file')->getPathFromUuid($image);
        }

        $container->get('huh.head.tag.meta_robots')->setContent($article['robots'] ?: 'index,follow');
        $container->get('huh.head.tag.meta_date')->setContent(Date::parse('c', $article['date']));
        $container->get('huh.head.tag.og_site_name')->setContent($objPage->rootPageTitle);
        $container->get('huh.head.tag.og_locale')->setContent($container->get('request_stack')->getCurrentRequest()->getLocale());
        $container->get('huh.head.tag.og_type')->setContent('article');
        $container->get('huh.head.tag.og_title')->setContent(StringUtil::stripInsertTags($article['headline']));
        $container->get('huh.head.tag.og_url')->setContent('{{news_category_url::'.$article['id'].'}}');
        $container->get('huh.head.tag.og_description')->setContent(str_replace("\n", ' ', strip_tags(Controller::replaceInsertTags($article['teaser']))));

        if ($article['addImage'] && $image) {
            $container->get('huh.head.tag.og_image')->setContent(Environment::get('url').'/'.$image);
        } else {
            $container->get('huh.head.tag.og_image')->setContent('');
        }

        $title = !$article['pageTitle'] ? StringUtil::stripInsertTags($article['pageTitle']) : StringUtil::stripInsertTags($article['headline'].' - '.$objPage->rootPageTitle);
        $container->get('huh.head.tag.meta_title')->setContent($title);

        //Overwrite the page title
        if ('' != $article['headline']) {
            $objPage->pageTitle = strip_tags(StringUtil::stripInsertTags($article['headline']));
        }

        $description = '';

        // Overwrite the page description
        if ('' != $article['metaDescription']) {
            $description = $article['metaDescription'];
        } else {
            if ('' != $article['teaser']) {
                $description = $article['teaser'];
            }
        }

        if ($description) {
            $container->get('huh.head.tag.meta_description')->setContent($this->prepareMetaDescription($description));
        }

        $keywords = StringUtil::deserialize($article['metaKeywords'], true);

        if (!empty($keywords)) {
            // keywords should be delimited by comma with space(see https://github.com/contao/core-bundle/issues/1078)
            $container->get('huh.head.tag.meta_keywords')->setContent(implode(', ', $keywords));
        }

        // twitter card
        if ($article['twitterCard']) {
            $container->get('huh.head.tag.twitter_card')->setContent($article['twitterCard']);

            if ($objPage->rootId > 0 && null !== ($rootPage = PageModel::findByPk($objPage->rootId)) && $rootPage->twitterSite) {
                $container->get('huh.head.tag.twitter_site')->setContent($rootPage->twitterSite);
            }

            if ($article['twitterCreator']) {
                $container->get('huh.head.tag.twitter_creator')->setContent($article['twitterCreator']);
            }

            $container->get('huh.head.tag.twitter_title')->setContent($title);

            if ($description) {
                $container->get('huh.head.tag.twitter_description')->setContent($this->prepareMetaDescription($description));
            }

            if ($article['addImage'] && $image) {
                $container->get('huh.head.tag.twitter_image')->setContent(Environment::get('url').'/'.$image);

                if ($article['alt']) {
                    $container->get('huh.head.tag.twitter_image_alt')->setContent($article['alt']);
                }
            } else {
                $container->get('huh.head.tag.twitter_image')->setContent('');
            }

            if ($article['addYoutube']) {
                $container->get('huh.head.tag.twitter_player')->setContent('https://www.youtube.com/embed/'.$article['youtube']);
                $container->get('huh.head.tag.twitter_player_width')->setContent(480);
                $container->get('huh.head.tag.twitter_player_height')->setContent(300);
            }
        }
    }

    /**
     * Prepare a text to be used in the meta description tag.
     *
     * @param string $strText
     *
     * @return string
     */
    protected function prepareMetaDescription($strText)
    {
        $strText = Controller::replaceInsertTags($strText, false);
        $strText = strip_tags($strText);
        $strText = str_replace("\n", ' ', $strText);
        $strText = StringUtil::substr($strText, 320);

        return trim($strText);
    }
}
