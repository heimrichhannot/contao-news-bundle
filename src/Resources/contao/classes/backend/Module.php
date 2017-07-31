<?php
/**
 * Created by PhpStorm.
 * User: kwagner
 * Date: 08.06.17
 * Time: 13:42
 */

namespace HeimrichHannot\NewsBundle\Backend;


class Module extends \Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }

    public function getNewsReadersSurveyModules(\DataContainer $dc)
    {
        return static::getModuleOptions('news_readers_survey');
    }

    public function getNewsInfoBoxModules(\DataContainer $dc)
    {
        return static::getModuleOptions('news_info_box');
    }

    /**
     * Get all articles and return them as array
     *
     * @param  \DataContainer $dc
     *
     * @return array
     */
    public function getArticleAlias(\DataContainer $dc)
    {
        $arrPids  = [];
        $arrAlias = [];
        if (!$this->User->isAdmin)
        {
            foreach ($this->User->pagemounts as $id)
            {
                $arrPids[] = $id;
                $arrPids   = array_merge($arrPids, $this->Database->getChildRecords($id, 'tl_page'));
            }
            if (empty($arrPids))
            {
                return $arrAlias;
            }
            $objAlias = $this->Database->prepare(
                "SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid WHERE a.pid IN(" . implode(
                    ',',
                    array_map(
                        'intval',
                        array_unique($arrPids)
                    )
                ) . ") ORDER BY parent, a.sorting"
            )->execute($dc->id);
        }
        else
        {
            $objAlias =
                $this->Database->prepare("SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid ORDER BY parent, a.sorting")
                    ->execute($dc->id);
        }
        if ($objAlias->numRows)
        {
            \System::loadLanguageFile('tl_article');
            while ($objAlias->next())
            {
                $arrAlias[$objAlias->parent][$objAlias->id] =
                    $objAlias->title . ' (' . ($GLOBALS['TL_LANG']['COLS'][$objAlias->inColumn] ?: $objAlias->inColumn) . ', ID ' . $objAlias->id . ')';
            }
        }

        return $arrAlias;
    }

    protected static function getModuleOptions($strType)
    {
        $arrOptions = [];

        $objModules = \ModuleModel::findByType($strType);

        if ($objModules === null)
        {
            return $arrOptions;
        }

        return $objModules->fetchEach('name');
    }
}