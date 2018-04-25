<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Module;

use HeimrichHannot\NewsBundle\Model\NewsModel;
use Patchwork\Utf8;

class ModuleNewsInfoBox extends \ModuleNews
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_news_info_box';

    /**
     * Current News.
     *
     * @var \Contao\NewsModel|null
     */
    protected $article;

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['newsreader'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        // Set the item from the auto_item parameter
        if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Do not index or cache the page if no news item has been specified
        if (!\Input::get('items')) {
            return '';
        }

        $this->news_archives = $this->sortOutProtected(\StringUtil::deserialize($this->news_archives));

        // Do not index or cache the page if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives)) {
            return '';
        }

        // Get the news item
        $this->article = NewsModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->news_archives);

        if (null === $this->article || 'none' == $this->article->infoBox) {
            return '';
        }

        return parent::generate();
    }

    protected function compile()
    {
        $infoBox = null;

        switch ($this->article->infoBox) {
            case 'text':
                $infoBox = $this->getInfoBoxText();
                break;
            default:
                $infoBox = $this->getInfoBoxCustom();
        }

        $this->Template->infoBox = $infoBox;
    }

    protected function getInfoBoxText()
    {
        $infoBox = null;
        $infoBox['header'] = $this->article->infoBox_header;
        $infoBox['text'] = $this->article->infoBox_text;
        $infoBox['link'] = '' == $this->article->infoBox_link ? null : $this->article->infoBox_link;
        $infoBox['linkText'] = '' == $this->article->infoBox_linkText ? null : $this->article->infoBox_linkText;

        /**
         * @var \Twig_Environment
         */
        $twig = \System::getContainer()->get('twig');

        return $twig->render(
            '@HeimrichHannotContaoNews/news/info_box.html.twig',
            ['infoBox' => $infoBox]
        );
    }

    /**
     * Custom info box hook.
     *
     * @param $objArticle
     *
     * @return bool
     */
    protected function getInfoBoxCustom()
    {
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['getCustomNewsInfoBox']) && is_array($GLOBALS['TL_HOOKS']['getCustomNewsInfoBox'])) {
            foreach ($GLOBALS['TL_HOOKS']['getCustomNewsInfoBox'] as $callback) {
                if (false === ($infoBox = \System::importStatic($callback[0])->{$callback[1]}($this->article, $this))) {
                    continue;
                }

                return $infoBox;
            }
        }

        return false;
    }
}
