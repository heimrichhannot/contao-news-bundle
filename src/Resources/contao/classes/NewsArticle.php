<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 17.08.17
 * Time: 11:25
 */

namespace HeimrichHannot\NewsBundle;


use Contao\CoreBundle\Monolog\ContaoContext;
use HeimrichHannot\NewsBundle\Module\ModuleNewsListRelated;
use NewsCategories\NewsCategories;
use NewsCategories\NewsCategoryModel;
use Psr\Log\LogLevel;

class NewsArticle extends \ModuleNews
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
     * @var \Module
     */
    protected $module;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(\FrontendTemplate $template, array $article, \Module $module)
    {
        $this->template = $template;
        $this->article  = (object)$article;
        $this->module   = $module;
        $this->twig     = \System::getContainer()->get('twig');

        parent::__construct($module->objModel);

        $this->generate();
    }

    protected function compile()
    {
        $this->setSeen();
        $this->addRelatedNews();
    }

    /**
     * Mark news as seen for newslist and newsarchive modules
     */
    protected function setSeen()
    {
        if ($this->module instanceof \ModuleNewsList || $this->module instanceof \ModuleNewsArchive) {
            NewsList::addSeen($this->article->id);
        }
    }


    protected function addRelatedNews()
    {
        if (!$this->article->add_related_news || !$this->module->related_news_module) {
            $this->template->add_related_news = false;

            return false;
        }

        if (($model = \ModuleModel::findByPk($this->module->related_news_module)) === null) {
            $this->template->add_related_news = false;

            return false;
        }

        $strClass = \Module::findClass($model->type);

        // Return if the class does not exist
        if (!class_exists($strClass)) {
            $this->template->add_related_news = false;

            \System::getContainer()->get('monolog.logger.contao')->log(
                LogLevel::ERROR,
                'Module class "'.$strClass.'" (module "'.$model->type.'") does not exist',
                ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]
            );

            return false;
        }

        $model->typePrefix = 'mod_';

        /** @var ModuleNewsListRelated $objModule */
        $objModule = new $strClass($model);
        $objModule->setNews($this->article->id);
        $strBuffer = $objModule->generate();

        // HOOK: add custom logic
        if (isset($GLOBALS['TL_HOOKS']['getFrontendModule']) && is_array($GLOBALS['TL_HOOKS']['getFrontendModule'])) {
            foreach ($GLOBALS['TL_HOOKS']['getFrontendModule'] as $callback) {
                $strBuffer = static::importStatic($callback[0])->{$callback[1]}($model, $strBuffer, $objModule);
            }
        }

        // Disable indexing if protected
        if ($model->protected && !preg_match('/^\s*<!-- indexer::stop/', $strBuffer)) {
            $strBuffer = "\n<!-- indexer::stop -->".$strBuffer."<!-- indexer::continue -->\n";
        }

        $this->template->related_news = $strBuffer;
    }


    /**
     * @return \FrontendTemplate
     */
    public function getNewsTemplate(): \FrontendTemplate
    {
        return $this->template;
    }

    /**
     * @param \FrontendTemplate $template
     */
    public function setNewsTemplate(\FrontendTemplate $template)
    {
        $this->template = $template;
    }
}