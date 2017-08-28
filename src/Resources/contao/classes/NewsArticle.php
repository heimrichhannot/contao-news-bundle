<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 17.08.17
 * Time: 11:25
 */

namespace HeimrichHannot\NewsBundle;


use Codefog\TagsBundle\Model\TagModel;
use Contao\CoreBundle\Monolog\ContaoContext;
use Dav\NewsBundle\Models\NewsTagsModel;
use HeimrichHannot\NewsBundle\Manager\NewsTagManager;
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

    /**
     * Initialize the object
     * @param \FrontendTemplate $template
     * @param array $article
     * @param \Module $module
     */
    public function __construct(\FrontendTemplate $template, array $article, \Module $module)
    {
        $this->template = $template;
        $this->article  = (object)$article;
        $this->module   = $module;
        $this->twig     = \System::getContainer()->get('twig');

        parent::__construct($module->objModel);

        $this->generate();
    }

    /**
     * Compile the current element
     */
    protected function compile()
    {
        $this->module->news_metaFields = deserialize($this->module->news_metaFields, true);

        $this->setSeen();
        $this->addRelatedNews();
        $this->addWriters();
        $this->addTags();
    }


    /**
     * Add news tags
     */
    protected function addTags()
    {
        if (!in_array('tags', $this->module->news_metaFields)) {
            return;
        }

        $ids = deserialize($this->article->tags, true);

        if (empty($ids)) {
            return;
        }

        /**
         * @var $manager NewsTagManager
         */
        $manager = \System::getContainer()->get('app.news_tags_manager');

        if (($models = $manager->findMultiple(['values' => $ids])) === null) {
            return;
        }

        $tags = [];

        while ($models->next()) {
            $tags[] = $models->row();
        }

        $this->template->tags          = $tags;
        $this->template->hasMetaFields = true;
    }

    /**
     * Add article writers from member table
     */
    protected function addWriters()
    {
        if (!in_array('writers', $this->module->news_metaFields)) {
            return;
        }

        $ids = deserialize($this->article->writers, true);

        if (empty($ids)) {
            return;
        }

        if (($members = \MemberModel::findMultipleByIds($ids)) === null) {
            return;
        }

        $writers = [];

        while ($members->next()) {
            $writers[] = $members->row();
        }

        /**
         * Provide a helper function that returns the writer names separated with given delimiter
         * @param string $delimiter The delimiter
         * @param string|null $format The writer name format string (default: ##firstname## ##lastname##)
         * @return string The writers separated by the delimiter string
         */
        $this->template->writerNames = function ($delimiter = ',', $format = null) use ($writers) {
            if ($format === null) {
                $format = '##firstname## ##lastname##';
            }

            $names = [];

            foreach ($writers as $writer) {
                $names[] = trim(\StringUtil::parseSimpleTokens($format, $writer));
            }


            return implode($delimiter, $names);
        };

        $this->template->writers       = $writers;
        $this->template->hasMetaFields = true;
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


    /**
     * Add related news to article
     */
    protected function addRelatedNews()
    {
        if (!$this->article->add_related_news || !$this->module->related_news_module) {
            $this->template->add_related_news = false;

            return;
        }

        if (($model = \ModuleModel::findByPk($this->module->related_news_module)) === null) {
            $this->template->add_related_news = false;

            return;
        }

        $strClass = \Module::findClass($model->type);

        // Return if the class does not exist
        if (!class_exists($strClass)) {
            $this->template->add_related_news = false;

            \System::getContainer()->get('monolog.logger.contao')->log(
                LogLevel::ERROR,
                'Module class "' . $strClass . '" (module "' . $model->type . '") does not exist',
                ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]
            );

            return;
        }

        $model->typePrefix = 'mod_';

        /** @var ModuleNewsListRelated $objModule */
        $objModule = new $strClass($model);
        $objModule->setNews($this->article->id);
        $strBuffer = $objModule->generate();

        // Disable indexing if protected
        if ($model->protected && !preg_match('/^\s*<!-- indexer::stop/', $strBuffer)) {
            $strBuffer = "\n<!-- indexer::stop -->" . $strBuffer . "<!-- indexer::continue -->\n";
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