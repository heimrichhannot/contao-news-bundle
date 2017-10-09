<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 17.08.17
 * Time: 11:25
 */

namespace HeimrichHannot\NewsBundle;


use Codefog\TagsBundle\Tag;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\ImageSizeModel;
use HeimrichHannot\FieldPalette\FieldPaletteModel;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\NewsBundle\Manager\NewsTagManager;
use HeimrichHannot\NewsBundle\Module\ModuleNewsInfoBox;
use HeimrichHannot\NewsBundle\Module\ModuleNewsListRelated;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

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
     *
     * @var array
     */
    protected $tokens = [];

    /**
     * The container object
     *
     * @var ContainerAwareInterface The container object
     */
    protected $container;

    /**
     * @var object|\Symfony\Component\Translation\DataCollectorTranslator
     */
    protected $translator;

    /**
     * Initialize the object
     *
     * @param \FrontendTemplate $template
     * @param array             $article
     * @param \Module           $module
     */
    public function __construct(\FrontendTemplate $template, array $article, \Module $module)
    {
        $this->template   = $template;
        $this->article    = (object)$article;
        $this->module     = $module;
        $this->container  = \System::getContainer();
        $this->twig       = $this->container->get('twig');
        $this->translator = \System::getContainer()->get('translator');

        parent::__construct($module->objModel);

        $this->module->news_metaFields = deserialize($this->module->news_metaFields, true);

        $this->generate();
    }

    /**
     * Compile the current element
     */
    protected function compile()
    {
        $this->module->news_metaFields = deserialize($this->module->news_metaFields, true);
        $this->template->module        = $this->module;

        $this->setSeen();
        $this->addRelatedNews();
        $this->addWriters();
        $this->addTags();
        $this->addInfoBox();
        $this->addPageMeta();
        $this->addNewsListFieldOverwrite();
        $this->addRatings();
        $this->addTeaserImage();
        $this->addPlayer();
        $this->addShare();

        $this->replaceTokens();
    }

    /**
     * Add a player for internal or external video-/audio files
     *
     * @return void;
     */
    protected function addPlayer()
    {
        $this->template->addPlayer = false;

        if (!$this->article->player || $this->article->player == 'none') {
            return;
        }

        global $objPage;

        $template = new \FrontendTemplate('newsplayer_default');
        $isVideo  = false;
        $sources  = [];

        switch ($this->article->player) {
            case 'internal':

                $uuid = \Contao\StringUtil::deserialize($this->article->playerSRC);

                if (!is_array($uuid) || empty($uuid)) {
                    return;
                }

                $files = \Contao\FilesModel::findMultipleByUuidsAndExtensions($uuid, ['mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv', 'm4a', 'mp3', 'wma', 'mpeg', 'wav', 'ogg']);

                if ($files === null) {
                    return;
                }

                // Pre-sort the array by preference
                if (in_array($files->first()->extension, ['mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv'])) {
                    $isVideo = true;
                    $sources = ['mp4' => null, 'm4v' => null, 'mov' => null, 'wmv' => null, 'webm' => null, 'ogv' => null];
                } else {
                    $isVideo = false;
                    $sources = ['m4a' => null, 'mp3' => null, 'wma' => null, 'mpeg' => null, 'wav' => null, 'ogg' => null];
                }

                $files->reset();

                // Convert the language to a locale (see #5678)
                $language = str_replace('-', '_', $objPage->language);

                // Pass File objects to the template
                while ($files->next()) {
                    $arrMeta = \Contao\StringUtil::deserialize($files->meta);

                    if (is_array($arrMeta) && isset($arrMeta[$language])) {
                        $strTitle = $arrMeta[$language]['title'];
                    } else {
                        $strTitle = $files->name;
                    }

                    $file        = new \Contao\File($files->path);
                    $file->title = \Contao\StringUtil::specialchars($strTitle);

                    $sources[$file->extension] = $file;
                }

                break;
            case 'external':
                $paths = \Contao\StringUtil::trimsplit('|', $this->article->playerUrl);

                if (!is_array($paths) || empty($paths)) {
                    return;
                }

                $extension = pathinfo($paths[0], PATHINFO_EXTENSION);

                // Pre-sort the array by preference
                if (in_array($extension, ['mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv'])) {
                    $isVideo = true;
                    $sources = ['mp4' => null, 'm4v' => null, 'mov' => null, 'wmv' => null, 'webm' => null, 'ogv' => null];
                } else {
                    $isVideo = false;
                    $sources = ['m4a' => null, 'mp3' => null, 'wma' => null, 'mpeg' => null, 'wav' => null, 'ogg' => null];
                }

                $mimetypes = $GLOBALS['TL_MIME'];

                // set source by extension
                foreach ($paths as $path) {
                    $extension = pathinfo($path, PATHINFO_EXTENSION);

                    if (!isset($GLOBALS['TL_MIME'][$extension])) {
                        continue;
                    }

                    $file                = new \stdClass();
                    $file->mime          = $GLOBALS['TL_MIME'][$extension][0];
                    $file->path          = Url::addScheme($path);
                    $sources[$extension] = $file;
                }

                break;
        }

        $template->poster = false;
        $posterSRC        = $this->article->posterSRC ?: $this->module->posterSRC;

        // Optional poster
        if ($posterSRC != '') {
            if (($poster = \FilesModel::findByUuid($posterSRC)) !== null) {
                $template->poster = $poster->path;
            }
        }

        $size = \StringUtil::deserialize($this->module->imgSize, true);

        if ($isVideo) {
            $template->size = ' width="640" height="360"';

            if ($size[0] > 0 || $size[1] > 0) {
                $template->size = ' width="' . $size[0] . '" height="' . $size[1] . '"';
            } else if (is_numeric($size[2])) {
                /** @var ImageSizeModel $imageModel */
                $imageModel = $this->container->get('contao.framework')->getAdapter(ImageSizeModel::class);
                $imageSize  = $imageModel->findByPk($size[2]);

                if (null !== $imageSize) {
                    $template->size = ' width="' . $imageSize->width . '" height="' . $imageSize->height . '"';
                }
            }
        } else {

            if ($template->poster != '') {
                $image = ['singleSRC' => $template->poster, 'size' => serialize([640, 360])];

                if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2])) {
                    $image['size'] = $this->module->imgSize;
                }

                $this->addImageToTemplate($template, $image, null, null, $poster);
            }
        }

        $template->files   = array_values(array_filter($sources));
        $template->isVideo = $isVideo;

        $this->template->addPlayer = true;
        $this->template->player    = $template->parse();
    }

    /**
     * Use a custom teaser if not in newsreader module
     */
    protected function addTeaserImage()
    {
        if ($this->module instanceof \ModuleNewsReader || $this->module->doNotUse) {
            return;
        }

        if (!$this->article->add_teaser_image || $this->article->teaser_singleSRC == '') {
            return;
        }

        $objModel = \FilesModel::findByUuid($this->article->teaser_singleSRC);

        if ($objModel === null || !is_file(TL_ROOT . '/' . $objModel->path)) {
            return;
        }

        $arrArticle = (array)$this->article;

        // Override the default image size
        if ($this->module->imgSize != '') {
            $size = \StringUtil::deserialize($this->module->imgSize);

            if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2])) {
                $arrArticle['size'] = $this->module->imgSize;
            }
        }

        $arrArticle['singleSRC'] = $objModel->path;
        $this->addImageToTemplate($this->template, $arrArticle, null, null, $objModel);
    }


    /**
     * Add custom fields from news list article relation
     */
    protected function addNewsListFieldOverwrite()
    {
        if (!$this->module->use_news_lists) {
            return;
        }

        $relations = FieldPaletteModel::findPublishedByPidsAndTableAndField(deserialize($this->module->news_lists, true), 'tl_news_list', 'news', ['limit' => 1], ['tl_fieldpalette.news_list_news = ?'], [$this->article->id]);

        if ($relations === null || !$relations->news_list_set_fields) {
            return;
        }

        $customFields = deserialize($relations->news_list_fields, true);

        foreach ($customFields as $key => $set) {
            if (!$set['field']) {
                continue;
            }

            $this->template->{$set['field']} = $set['value'];
        }
    }


    /**
     * Add page meta information
     */
    protected function addPageMeta()
    {
        if (!$this->module instanceof \ModuleNewsReader) {
            return;
        }

        global $objPage;

        $this->container->get('huh.head.tag.meta_date')->setContent(\Date::parse('c', $this->article->date));
        $this->container->get('huh.head.tag.og_site_name')->setContent($objPage->rootPageTitle);
        $this->container->get('huh.head.tag.og_locale')->setContent($this->container->get('request_stack')->getCurrentRequest()->getLocale());
        $this->container->get('huh.head.tag.og_type')->setContent('article');
        $this->container->get('huh.head.tag.og_title')->setContent(\StringUtil::stripInsertTags($this->article->headline));
        $this->container->get('huh.head.tag.og_url')->setContent(\Environment::get('url') . '/' . $this->template->link);
        $this->container->get('huh.head.tag.og_description')->setContent(str_replace("\n", ' ', strip_tags(\Controller::replaceInsertTags($this->article->teaser))));

        if ($this->template->addImage) {
            $this->container->get('huh.head.tag.og_image')->setContent(\Environment::get('url') . '/' . $this->template->singleSRC);
        }

        $title = !$this->article->pageTitle ? \StringUtil::stripInsertTags($this->article->pageTitle) : \StringUtil::stripInsertTags($this->article->headline . ' - ' . $objPage->rootPageTitle);
        $this->container->get('huh.head.tag.meta_title')->setContent($title);

        // Overwrite the page title
        if ($this->article->headline != '') {
            $objPage->pageTitle = strip_tags(\StringUtil::stripInsertTags($this->article->headline));
        }

        $description = '';

        // Overwrite the page description
        if ($this->article->metaDescription != '') {
            $description = $this->article->metaDescription;
        } else if ($this->article->teaser != '') {
            $description = $this->article->teaser;
        }

        if ($description) {
            $this->container->get('huh.head.tag.meta_description')->setContent($this->prepareMetaDescription($description));
        }

        $keywords = deserialize($this->article->metaKeywords, true);

        if (!empty($keywords)) {
            // keywords should be delimited by comma with space (see https://github.com/contao/core-bundle/issues/1078)
            $this->container->get('huh.head.tag.meta_keywords')->setContent(implode(', ', $keywords));
        }

        // twitter card
        if ($this->article->twitterCard) {
            $this->container->get('huh.head.tag.twitter_card')->setContent($this->article->twitterCard);

            if ($objPage->rootId > 0 && ($rootPage = \PageModel::findByPk($objPage->rootId)) !== null && $rootPage->twitterSite) {
                \System::getContainer()->get('huh.head.tag.twitter_site')->setContent($rootPage->twitterSite);
            }

            if ($this->article->twitterCreator) {
                \System::getContainer()->get('huh.head.tag.twitter_creator')->setContent($this->article->twitterCreator);
            }

            $this->container->get('huh.head.tag.twitter_title')->setContent($title);

            if ($description) {
                $this->container->get('huh.head.tag.twitter_description')->setContent($this->prepareMetaDescription($description));
            }

            if ($this->template->addImage) {
                $this->container->get('huh.head.tag.twitter_image')->setContent(\Environment::get('url') . '/' . $this->template->singleSRC);

                if ($this->template->alt) {
                    $this->container->get('huh.head.tag.twitter_image_alt')->setContent($this->template->alt);
                }
            }

            if ($this->template->addYoutube) {
                $this->container->get('huh.head.tag.twitter_player')->setContent('https://www.youtube.com/embed/' . $this->youtube);
                $this->container->get('huh.head.tag.twitter_player_width')->setContent(480);
                $this->container->get('huh.head.tag.twitter_player_height')->setContent(300);
            }
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

            $this->container->get('monolog.logger.contao')->log(LogLevel::ERROR, 'Module class "' . $strClass . '" (module "' . $model->type . '") does not exist', ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]);

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
        $manager = $this->container->get('huh.news.news_tags_manager');

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
     * Add article ratings (google page impressions, facebook likes, disqus comments)
     */
    protected function addRatings()
    {
        $this->template->hasRatings = false;

        if (!in_array('ratings', $this->module->news_metaFields)) {
            return;
        }

        if ($this->article->google_analytic_updated_at > 0) {
            $this->template->hasRatings = true;
            $this->template->viewRating = $this->article->google_analytic_counter;
        }

        if ($this->article->disqus_updated_at > 0) {
            $this->template->hasRatings    = true;
            $this->template->commentRating = $this->article->disqus_counter;
        }

        if ($this->article->facebook_updated_at > 0) {
            $this->template->hasRatings  = true;
            $this->template->likesRating = ($this->template->likesRating ?: 0) + $this->article->facebook_counter;
        }

        if ($this->article->google_plus_updated_at > 0) {
            $this->template->hasRatings  = true;
            $this->template->likesRating = ($this->template->likesRating ?: 0) + $this->article->google_plus_counter;
        }

        if ($this->article->twitter_updated_at > 0) {
            $this->template->hasRatings  = true;
            $this->template->likesRating = ($this->template->likesRating ?: 0) + $this->article->twitter_counter;
        }
    }

    /**
     * Add heimrichhannot/contao-share support
     */
    protected function addShare()
    {
        $this->template->addShare = false;

        if ($this->module->addShare) {
            $this->article->title     = $this->article->headline;
            $this->template->addShare = true;
            $objShare                 = new \HeimrichHannot\Share\Share($this->module->getModel(), $this->article);
            $this->template->share    = $objShare->generate();
        }
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
         *
         * @param string      $delimiter The delimiter
         * @param string|null $format    The writer name format string (default: ##firstname## ##lastname##)
         *
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
        $relatedNewsModules = deserialize($this->module->relatedNewsModules, true);

        $relatedNews = [];

        if (!empty($relatedNewsModules)) {
            foreach ($relatedNewsModules as $relatedNewsModule) {
                if (($model = \ModuleModel::findByPk($relatedNewsModule['module'])) === null) {
                    $this->template->add_related_news = false;

                    continue;
                }

                $strClass = \Module::findClass($model->type);

                // Return if the class does not exist
                if (!class_exists($strClass)) {
                    $this->template->add_related_news = false;

                    $this->container->get('monolog.logger.contao')->log(LogLevel::ERROR, 'Module class "' . $strClass . '" (module "' . $model->type . '") does not exist', ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]);

                    continue;
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

                $relatedNews[$relatedNewsModule['alias']] = $strBuffer;
            }
        }

        $this->template->related_news = $relatedNews;
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