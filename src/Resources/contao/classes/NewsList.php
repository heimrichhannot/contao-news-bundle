<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle;


use HeimrichHannot\FieldPalette\FieldPaletteModel;

class NewsList
{

    /**
     * List of news archive ids
     *
     * @var array
     */
    protected $newsArchives = [];

    /**
     * Set true if only featured items should be handled
     *
     * @var bool
     */
    protected $blnFeatured;

    /**
     * Current front end module instance
     *
     * @var \Module
     */
    protected $objModule;

    /**
     * News table
     *
     * @var string
     */
    protected static $strTable = 'tl_news';

    /**
     * NewsList constructor.
     *
     * @param array     $newsArchives
     * @param bool|null $blnFeatured
     * @param \Module   $objModule
     */
    public function __construct(array $newsArchives, $blnFeatured, \Module $objModule)
    {
        $this->newsArchives = $newsArchives;
        $this->blnFeatured  = $blnFeatured;
        $this->objModule    = $objModule;
    }

    /**
     * Count news items
     *
     * @return integer|boolean Return the number of news items of false for default count behavior
     */
    public function count()
    {
        if ($this->objModule->use_news_lists)
        {
            $objRelations = FieldPaletteModel::findPublishedByPidsAndTableAndField(deserialize($this->objModule->news_lists, true), 'tl_news_list', 'news');

            if ($objRelations === null)
            {
                return false;
            }

            return NewsModel::countPublishedByPidsAndIds($this->newsArchives, $objRelations->fetchEach('news_list_news'), $this->blnFeatured);
        }

        return false;
    }

    /**
     * Fetch news items
     *
     * @param integer $limit  Current limit from pagination
     * @param integer $offset Current offset from pagination
     *
     * @return \Model\Collection|NewsModel|null|boolean Return a collection it news items of false for the default fetch behavior
     */
    public function fetch($limit, $offset)
    {
        if ($this->objModule->use_news_lists)
        {
            $t = static::$strTable;

            $objRelations = FieldPaletteModel::findPublishedByPidsAndTableAndField(deserialize($this->objModule->news_lists, true), 'tl_news_list', 'news');

            if ($objRelations === null)
            {
                return false;
            }

            $ids = $objRelations->fetchEach('news_list_news');

            $arrOptions['order'] = "FIELD($t.pid, " . implode(',', array_map('intval', $this->newsArchives)) . "), FIELD($t.id, " . implode(',', array_map('intval', $ids)) . ")";

            return NewsModel::findPublishedByPidsAndIds($this->newsArchives, $ids, $this->blnFeatured, $limit, $offset, $arrOptions);
        }

        return false;
    }

}