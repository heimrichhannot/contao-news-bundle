<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Module;

use HeimrichHannot\FieldPaletteBundle\Model\FieldPaletteModel;
use HeimrichHannot\NewsBundle\Model\NewsModel;

class ModuleNewsReadersSurveyResult extends \ModuleNews
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_news_readers_survey_result';

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
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
        if (null === $objArticle || !$objArticle->add_readers_survey) {
            return '';
        }
        /**
         * @var \Twig_Environment
         */
        $twig = \System::getContainer()->get('twig');

        $readersSurvey = $this->getReadersSurvey($objArticle);

        $answers = $this->getAnswersVote($objArticle);

        return $this->Template->readers_survey = $twig->render(
            '@HeimrichHannotContaoNews/news/readers_survey_result.html.twig',
            ['answers' => $answers, 'question' => $readersSurvey['question']]
        );
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
        $result = null;
        $surveys = unserialize($objArticle->readers_survey);
        $answers = [];
        $sum = 0;
        foreach ($surveys as $survey) {
            $fieldPaletteQuestion = FieldPaletteModel::findById($survey);
            $answers = [];
            foreach (unserialize($fieldPaletteQuestion->news_answers) as $answerId) {
                $fieldPaletteAnswer = FieldPaletteModel::findById($answerId);
                $answers[$fieldPaletteAnswer->news_answer] = $fieldPaletteAnswer->news_answer_vote;
                $sum += $fieldPaletteAnswer->news_answer_vote;
            }
        }

        foreach ($answers as $key => $value) {
            $vote = ((int) $value / (int) $sum) * 100;
            $result[$key] = round($vote);
        }

        return $result;
    }
}
