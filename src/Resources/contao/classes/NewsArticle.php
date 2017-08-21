<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 17.08.17
 * Time: 11:25
 */

namespace HeimrichHannot\NewsBundle;


use NewsCategories\NewsCategories;
use NewsCategories\NewsCategoryModel;

class NewsArticle
{
    /**
     * @var \FrontendTemplate
     */
    protected $template;

    /**
     * @var array
     */
    protected $article;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(\FrontendTemplate $template, array $article)
    {
        $this->template = $template;
        $this->article  = (object) $article;
        $this->twig     = \System::getContainer()->get('twig');
        $this->extend();
    }

    protected function extend()
    {
    }


    /**
     * @return \FrontendTemplate
     */
    public function getTemplate(): \FrontendTemplate
    {
        return $this->template;
    }

    /**
     * @param \FrontendTemplate $template
     */
    public function setTemplate(\FrontendTemplate $template)
    {
        $this->template = $template;
    }
}