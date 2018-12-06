<?php

namespace HeimrichHannot\NewsBundle\Controller;


use HeimrichHannot\Ajax\Response\ResponseData;
use HeimrichHannot\Ajax\Response\ResponseSuccess;
use HeimrichHannot\NewsBundle\News;
use HeimrichHannot\Ajax\Ajax;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function xhrAction()
    {
        Ajax::runActiveAction(News::XHR_GROUP, News::XHR_READER_SURVEY_RESULT_ACTION, $this);
    }

    public function showReadersSurveyResultAction($id)
    {
        $objResponse = new ResponseSuccess();
        $objResponse->setResult(new ResponseData(\Controller::getFrontendModule($id)));

        return $objResponse;
    }
}