<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 17.08.17
 * Time: 11:25
 */

namespace HeimrichHannot\NewsBundle;


use Codefog\TagsBundle\Model\TagModel;
use Codefog\TagsBundle\Tag;
use Contao\CoreBundle\Monolog\ContaoContext;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\NewsBundle\Manager\NewsTagManager;
use HeimrichHannot\NewsBundle\Module\ModuleNewsInfoBox;
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
     * Simple tokens
     * @var array
     */
    protected $tokens = [];

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
        $this->replaceTokens();
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
        $this->addInfoBox();
        $this->addPageMeta();
    }

    protected function addPageMeta()
    {
        if (!$this->module instanceof \ModuleNewsReader) {
            return;
        }

        global $objPage;

        // Overwrite the page title
        if ($this->article->pageTitle != '') {
            $objPage->pageTitle = strip_tags(\StringUtil::stripInsertTags($this->article->pageTitle));
        } else if ($this->article->headline != '') {
            $objPage->pageTitle = strip_tags(\StringUtil::stripInsertTags($this->article->headline));
        }

        // Overwrite the page description
        if ($this->article->metaDescription != '') {
            $objPage->description = $this->prepareMetaDescription($this->article->metaDescription);
        } else if ($this->article->teaser != '') {
            $objPage->description = $this->prepareMetaDescription($this->article->teaser);
        }

        $keywords = deserialize($this->article->metaKeywords, true);

        if (!empty($keywords)) {
            $GLOBALS['TL_KEYWORDS'] = implode(',', $keywords);
        }
    }

    /**
     * Replace tokens within teaser and text
     */
    protected function replaceTokens()
    {
        $id     = $this->article->id;
        $tokens = $this->tokens;

        if ($this->template->hasText) {
            $this->template->text = function () use ($id, $tokens) {
                $strText    = '';
                $objElement = \ContentModel::findPublishedByPidAndTable($id, 'tl_news');

                if ($objElement !== null) {
                    while ($objElement->next()) {
                        $strText .= $this->getContentElement($objElement->current());
                    }
                }

                if (count($tokens) > 0) {
                    $strText = \StringUtil::parseSimpleTokens($strText, $tokens);
                }

                return $strText;
            };
        }

        if ($this->template->hasTeaser) {
            $this->template->teaser = \StringUtil::parseSimpleTokens($this->template->teaser, $tokens);
        }
    }

    /**
     * Add info box
     */
    protected function addInfoBox()
    {
        $this->template->hasInfoBox = false;

        if (!$this->module->newsInfoBoxModule) {
            return;
        }

        if (($model = \ModuleModel::findByPk($this->module->newsInfoBoxModule)) === null) {
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

        /** @var ModuleNewsInfoBox $objModule */
        $objModule = new $strClass($model);
        $strBuffer = $objModule->generate();

        // Disable indexing if protected
        if ($model->protected && !preg_match('/^\s*<!-- indexer::stop/', $strBuffer)) {
            $strBuffer = "\n<!-- indexer::stop -->" . $strBuffer . "<!-- indexer::continue -->\n";
        }

        $this->template->hasInfoBox    = true;
        $this->template->infoBox       = $strBuffer;
        $this->tokens['news_info_box'] = $strBuffer; // support ##news_info_box## simple tokens (within tl_content for example)
    }

    /**
     * Add news tags
     */
    protected function addTags()
    {
        $this->template->hasTags = false;

        if (!in_array('tags', $this->module->news_metaFields)) {
            return;
        }

        $ids = deserialize($this->article->tags, true);

        if (empty($ids)) {
            return;
        }

        /** @var $manager NewsTagManager */
        $manager = \System::getContainer()->get('huh.news.news_tags_manager');

        if (($models = $manager->findMultiple(['values' => $ids])) === null) {
            return;
        }

        $tags = [];

        /** @var $model Tag */
        foreach ($models as $model) {
            $tag = $model->getData();

            if (($url = Url::generateFrontendUrl($this->module->newsTagFilterJumpTo))) {
                $tag['href'] = $url . '/' . $tag['alias'];
            } else {
                $tag['href'] = '#';
            }

            $tag['linkValue'] = '#';

            $tags[$tag['id']] = $tag;
        }

        $this->template->hasTags       = true;
        $this->template->tags          = $tags;
        $this->template->hasMetaFields = true;
    }

    /**
     * Add article writers from member table
     */
    protected function addWriters()
    {
        $this->template->hasWriters = false;

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

        $this->template->hasWriters    = true;
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