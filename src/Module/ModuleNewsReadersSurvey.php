<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Module;

use HeimrichHannot\Ajax\AjaxAction;
use HeimrichHannot\FieldPalette\FieldPaletteModel;
use HeimrichHannot\NewsBundle\Form\ReadersSurveyForm;
use HeimrichHannot\NewsBundle\Model\NewsModel;
use HeimrichHannot\NewsBundle\News;
use Patchwork\Utf8;
use Symfony\Component\Form\Forms;

class ModuleNewsReadersSurvey extends \ModuleNews
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_news_readers_survey';

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

        return parent::generate();
    }

    protected function compile()
    {
        // Get the news item
        $objArticle = NewsModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->news_archives);
        $factory = Forms::createFormFactoryBuilder()->addExtensions([])->getFormFactory();
        if (null === $objArticle || !$objArticle->add_readers_survey) {
            return '';
        }
        /**
         * @var \Twig_Environment
         */
        $twig = \System::getContainer()->get('twig');
        $arrOptions = [];
        $showResult = AjaxAction::generateUrl(
            News::XHR_GROUP,
            News::XHR_READER_SURVEY_RESULT_ACTION,
            [
                News::XHR_PARAMETER_ID => $this->news_readers_survey_result,
                'items' => \Input::get('items'),
            ]
        );
        $readersSurvey = $this->getReadersSurvey($objArticle);
        $form = $factory->create(ReadersSurveyForm::class, $readersSurvey, $arrOptions);
        $form->handleRequest();

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $fieldModel = FieldPaletteModel::findById($data['answers']);
                if (null !== $fieldModel) {
                    $fieldModel->news_answer_vote = $fieldModel->news_answer_vote + 1;
                    $fieldModel->save();
                }

                return $this->Template->readers_survey = \Controller::getFrontendModule($this->news_readers_survey_result);
            }
        }
        if (null !== $readersSurvey) {
            $this->Template->readers_survey = $twig->render(
                '@HeimrichHannotContaoNews/news/readers_survey.html.twig',
                ['form' => $form->createView(), 'question' => $readersSurvey['question'], 'showResult' => $showResult]
            );
        }
    }

    protected function getReadersSurvey($objArticle)
    {
        $readersSurvey = null;
        $surveys = unserialize($objArticle->readers_survey);
        foreach ($surveys as $survey) {
            $fieldPaletteQuestion = FieldPaletteModel::findById($survey);
            $answers = [];
            foreach (unserialize($fieldPaletteQuestion->news_answers) as $answerId) {
                $fieldPaletteAnswer = FieldPaletteModel::findById($answerId);
                $answers[] = [$fieldPaletteAnswer->news_answer => $fieldPaletteAnswer->id];
            }
            $readersSurvey = [
                'question' => $fieldPaletteQuestion->news_question,
                'answers' => $answers,
            ];
        }

        return $readersSurvey;
    }

    protected function getAnswersVote($objArticle)
    {
        $answers = null;
        $surveys = unserialize($objArticle->readers_survey);

        foreach ($surveys as $survey) {
            $fieldPaletteQuestion = FieldPaletteModel::findById($survey);
            $answers = [];
            foreach (unserialize($fieldPaletteQuestion->news_answers) as $answerId) {
                $fieldPaletteAnswer = FieldPaletteModel::findById($answerId);
                $answers[$fieldPaletteAnswer->news_answer] = $fieldPaletteAnswer->news_answer_vote;
            }
        }

        return $answers;
    }
}
