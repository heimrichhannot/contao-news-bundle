<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsBundle\Module;


use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Module;
use HeimrichHannot\NewsBundle\Model\NewsModel;
use HeimrichHannot\NewsBundle\NewsList;
use Patchwork\Utf8;
use Psr\Log\LogLevel;

class ModuleNewsNavigation extends \Contao\ModuleNews
{
    /**
     * Current news id
     * @var int
     */
    protected $current;


    /**
     * @var NewsList
     */
    protected $list;

    /**
     * Display a wildcard in the back end
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var \Contao\BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['newsnavigation'][0]) . ' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        if (($listModuleModel = \ModuleModel::findByPk($this->newsListModule)) === null)
        {
            return '';
        }
        $class = Module::findClass($listModuleModel->type);
        // Return if the class does not exist
        if (!class_exists($class))
        {
            $this->container->get('monolog.logger.contao')->log(LogLevel::ERROR, 'Module class "' . $class . '" (module "' . $listModuleModel->type . '") does not exist', ['contao' => new ContaoContext(__METHOD__, TL_ERROR)]);

            return '';
        }

        $listModuleModel->typePrefix = 'mod_';

        /** @var ModuleNewsNavigation $module */
        $module = new $class($listModuleModel);



        $this->news_archives = $this->sortOutProtected(\StringUtil::deserialize($module->news_archives));

        // Return if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives))
        {
            return '';
        }

        $featured = null;

        // Handle featured news
        if ($this->news_featured == 'featured')
        {
            $featured = true;
        } elseif ($this->news_featured == 'unfeatured')
        {
            $featured = false;
        }

        $this->list = new NewsList($this->news_archives, $featured, $module);

        return parent::generate();
    }


    /**
     * Compile the current element
     */
    protected function compile()
    {
        $total = $this->list->count();

        if ($total < 1)
        {
            return;
        }

        $t = NewsModel::getTable();

        $this->list->initFetch();

        $columns = $this->list->getFilterColumns();
        $values  = $this->list->getFilterValues();
        $options = $this->list->getFilterOptions();



        $next = NewsModel::findBy(
            array_merge($columns, ["$t.time > ?"]),
            array_merge(is_array($values) ? $values : [$this->current->time]),
            array_merge($options, ['limit' => 1])
        );
    }

    public function setCurrent($news)
    {
        if (($model = NewsModel::findByPk($news)) !== null)
        {
            $this->current = $model;
        }
    }
}