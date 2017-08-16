<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 25.07.17
 * Time: 17:06
 */

namespace HeimrichHannot\NewsBundle\Module;


use HeimrichHannot\FieldPalette\FieldPaletteModel;
use HeimrichHannot\NewsBundle\Form\ReadersSurveyForm;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\NewsBundle\NewsModel;
use Patchwork\Utf8;
use Symfony\Component\Form\Forms;

class ModuleNewsReadersSurveyResult extends \ModuleNews
{
    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'mod_news_readers_survey_result';

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
        $twig = \System::getContainer()->get('twig');

        $readersSurvey = $this->getReadersSurvey($objArticle);

        $answers = $this->getAnswersVote($objArticle);

        return $this->Template->readers_survey =
            $twig->render('@HeimrichHannotContaoNews/news/readers_survey_result.html.twig', ['answers' => $answers, 'question' => $readersSurvey['question']]);
    }

    protected function getReadersSurvey($objArticle)
    {
        $readersSurvey = null;
        $surveys       = unserialize($objArticle->readers_survey);
        foreach ($surveys as $survey)
        {
            $fieldPaletteQuestion = FieldPaletteModel::findById($survey);
            $answers              = [];
            foreach (unserialize($fieldPaletteQuestion->news_answers) as $answerId)
            {
                $fieldPaletteAnswer = FieldPaletteModel::findById($answerId);
                $answers[]          = [$fieldPaletteAnswer->news_answer => $fieldPaletteAnswer->id];
            }
            $readersSurvey = [
                'question' => $fieldPaletteQuestion->news_question,
                'answers'  => $answers,
            ];
        }

        return $readersSurvey;
    }

    protected function getAnswersVote($objArticle)
    {
        $answers = null;
        $surveys = unserialize($objArticle->readers_survey);

        foreach ($surveys as $survey)
        {
            $fieldPaletteQuestion = FieldPaletteModel::findById($survey);
            $answers              = [];
            foreach (unserialize($fieldPaletteQuestion->news_answers) as $answerId)
            {
                $fieldPaletteAnswer                        = FieldPaletteModel::findById($answerId);
                $answers[$fieldPaletteAnswer->news_answer] = $fieldPaletteAnswer->news_answer_vote;
            }
        }

        return $answers;
    }

}