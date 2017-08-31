<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 25.07.17
 * Time: 17:06
 */

namespace HeimrichHannot\NewsBundle\Module;


use Contao\NewsModel;
use Patchwork\Utf8;

class ModuleNewsSuggestions extends \ModuleNews
{
    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_news_suggestions';

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

        $this->news_archives = $this->sortOutProtected(\StringUtil::deserialize($this->news_archives));

        // Do not index or cache the page if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives)) {
            return '';
        }

        return parent::generate();
    }

    protected function compile()
    {
        /**
         * @var \Twig_Environment $twig
         */
        $twig                        = \System::getContainer()->get('twig');
        $this->Template->suggestions = $twig->render('@HeimrichHannotContaoNews/news/suggestions.html.twig', ['suggestions' => $this->getSuggestions()]);
    }

    protected function getNews($order)
    {
        $news = \Contao\NewsModel::findPublishedByPid($this->news_archives, $this->perPage, ['order' => $order . ' DESC']);
        if ($news == null) {
            return null;
        }
        $orderedNews = [];
        foreach ($news as $newsModel) {
            $this->getTeaserImage($newsModel);
            $orderedNews[] = [
                'subheadline' => $newsModel->subheadline,
                'headline'    => $newsModel->headline,
                'link'        => $newsModel->getUrl(null),
                'image'       => $newsModel->src,
            ];
        }

        return $orderedNews;
    }

    protected function getSuggestions()
    {
        $newsSuggestions = unserialize($this->news_suggestion);
        $suggestions     = null;
        if (empty($newsSuggestions)) {
            return $suggestions;
        }

        foreach ($newsSuggestions as $suggestion) {
            $suggestions[] = [
                'collapse' => str_replace(' ', '', $suggestion['suggestion_label']),
                'label'    => $suggestion['suggestion_label'],
                'news'     => $this->getNews($suggestion['suggestion_order_column']),
            ];
        }

        return $suggestions;
    }

    /**
     * @param NewsModel $objArticle
     */
    protected function getTeaserImage($objArticle)
    {
        // Add an image
        if ($objArticle->addImage && $objArticle->singleSRC != '') {
            $objModel = \FilesModel::findByUuid($objArticle->singleSRC);

            if ($objModel !== null && is_file(TL_ROOT . '/' . $objModel->path)) {
                // Do not override the field now that we have a model registry (see #6303)
                $arrArticle = $objArticle->row();

                // Override the default image size
                if ($this->imgSize != '') {
                    $size = \StringUtil::deserialize($this->imgSize);

                    if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2])) {
                        $arrArticle['size'] = $this->imgSize;
                    }
                }

                $arrArticle['singleSRC'] = $objModel->path;
                $this->addImageToTemplate($objArticle, $arrArticle, null, null, $objModel);
            }
        }
    }
}