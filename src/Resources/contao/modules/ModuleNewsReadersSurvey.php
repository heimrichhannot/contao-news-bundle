<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 25.07.17
 * Time: 17:06
 */

namespace HeimrichHannot\NewsBundle\Module;


use Patchwork\Utf8;

class ModuleNewsReadersSurvey extends \ModuleNews
{
    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_news_readers_survey';

    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
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
        if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item']))
        {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Do not index or cache the page if no news item has been specified
        if (!\Input::get('items'))
        {
            return '';
        }

        $this->news_archives = $this->sortOutProtected(\StringUtil::deserialize($this->news_archives));

        // Do not index or cache the page if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives))
        {
            return '';
        }

        return parent::generate();
    }

    protected function compile()
    {
        // Get the news item
        $objArticle = \NewsModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->news_archives);

        if ($objArticle === null || !$objArticle->add_readers_survey)
        {
            return '';
        }
        /**
         * @var \Twig_Environment $twig
         */
        $twig          = \System::getContainer()->get('twig');
        $readersSurvey = $this->getReadersSurvey($objArticle);
        if ($readersSurvey !== null)
        {
            $this->Template->item = $twig->render(
                '@HeimrichHannotContaoNews/news/readers_survey.html.twig',
                ['readersSurvey' => $readersSurvey]
            );
        }
    }

    protected function getReadersSurvey($objArticle)
    {
        $readersSurvey             = null;
        $readersSurvey['question'] = $objArticle->readers_survey_question;
        $answers                   = unserialize($objArticle->readers_survey_question);
        foreach ($answers as $answer)
        {
            $readersSurvey['answers'][] = $answer;
        }

        return $readersSurvey;
    }

}