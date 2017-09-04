<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 25.07.17
 * Time: 17:06
 */

namespace HeimrichHannot\NewsBundle\Module;


use HeimrichHannot\NewsBundle\Model\NewsModel;
use Patchwork\Utf8;

class ModuleNewsInfoBox extends \ModuleNews
{
    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_news_info_box';

    /**
     * Current News
     * @var \Contao\NewsModel|null
     */
    protected $article;

    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['newsreader'][0]) . ' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

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

        if ($this->article === null || $this->article->info_box_none) {
            return '';
        }

        return parent::generate();
    }

    protected function compile()
    {
        $infoBox = null;

        switch ($this->article->info_box_selector) {
            case 'info_box_text':
                $infoBox = $this->getInfoBoxText();
                break;
            default:
                $infoBox = $this->getInfoBoxCustom();
        }

        $this->Template->infoBox = $infoBox;
    }

    protected function getInfoBoxText()
    {
        $infoBox             = null;
        $infoBox['header']   = $this->article->info_box_text_header;
        $infoBox['text']     = $this->article->info_box_text_text;
        $infoBox['link']     = $this->article->info_box_text_link == '' ? null : $this->article->info_box_text_link;
        $infoBox['linkText'] = $this->article->info_box_text_link_text == '' ? null : $this->article->info_box_text_link_text;

        /**
         * @var \Twig_Environment $twig
         */
        $twig = \System::getContainer()->get('twig');

        return $twig->render(
            '@HeimrichHannotContaoNews/news/info_box.html.twig',
            ['infoBox' => $infoBox]
        );
    }

    /**
     * Custom info box hook
     * @param $objArticle
     * @return bool
     */
    protected function getInfoBoxCustom()
    {
        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['getCustomNewsInfoBox']) && is_array($GLOBALS['TL_HOOKS']['getCustomNewsInfoBox'])) {
            foreach ($GLOBALS['TL_HOOKS']['getCustomNewsInfoBox'] as $callback) {
                if (($infoBox = \System::importStatic($callback[0])->{$callback[1]}($this->article, $this)) === false) {
                    continue;
                }

                return $infoBox;
            }
        }

        return false;
    }
}