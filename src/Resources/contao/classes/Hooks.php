<?php

namespace HeimrichHannot\NewsBundle;


class Hooks
{

    /**
     * Extend news article data
     * @param \FrontendTemplate $objTemplate
     * @param array             $arrArticle
     * @param \Module           $objModule
     */
    public function parseArticleHook(\FrontendTemplate $objTemplate, array $arrArticle, \Module $objModule)
    {
        $objArticle = new NewsArticle($objTemplate, $arrArticle);
        $objTemplate = $objArticle->getTemplate();
    }

}