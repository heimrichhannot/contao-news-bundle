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
        $this->addParentCategory();
    }

    protected function addParentCategory()
    {
        $set = new \stdClass();

        $categories = deserialize($this->article->categories, true);

        if ($this->article->primaryCategory > 0
            && ($tree = \System::getContainer()->get('hh.news-bundle.category_helper')->getCategoryTree($this->article->primaryCategory, 0)) !== null)
        {
            $set->primary = $tree[0];
        }

        if (count($categories) > 0 && ($objAllCategories = NewsCategoryModel::findPublishedByIds($categories)) !== null)
        {
            $all = [];

            while ($objAllCategories->next())
            {
                // set first category as primary category
                if (!$set->primary && ($tree = \System::getContainer()->get('hh.news-bundle.category_helper')->getCategoryTree($this->article->primaryCategory, 0)) !== null)
                {
                    $set->primary = $tree[0];
                }

                $category       = (object) $objAllCategories->row();
                $category->tree = \System::getContainer()->get('hh.news-bundle.category_helper')->getCategoryTree($objAllCategories->id);
                $all[]          = $category;
            }

            $set->categories = $all;
        }

        $this->template->categories = $set;
    }

    protected function getCategoryTree($intCategory)
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