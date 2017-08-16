<?php

namespace HeimrichHannot\NewsBundle\Controller;


use HeimrichHannot\Ajax\Response\ResponseData;
use HeimrichHannot\Ajax\Response\ResponseSuccess;
use HeimrichHannot\FieldPalette\FieldPaletteModel;
use HeimrichHannot\NewsBundle\News;
use HeimrichHannot\Ajax\Ajax;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function xhrAction()
    {
        Ajax::runActiveAction(News::XHR_GROUP, News::XHR_READER_SURVEY_RESULT_ACTION, $this);
        Ajax::runActiveAction(News::XHR_GROUP, News::XHR_READER_SURVEY_SAVE_ACTION, $this);
    }

    public function showReadersSurveyResultAction($id)
    {
        $objResponse = new ResponseSuccess();
        $objResponse->setResult(new ResponseData(\Controller::getFrontendModule($id)));

        return $objResponse;
    }

    public function saveReadersSurveyAnswer($id, $answerId)
    {
        $fieldModel = FieldPaletteModel::findById($answerId);
        if ($fieldModel !== null)
        {
            $fieldModel->news_answer_vote = $fieldModel->news_answer_vote + 1;
            $fieldModel->save();
        }

        return $this->showReadersSurveyResultAction($id);
    }
}