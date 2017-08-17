<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Services;


use NewsCategories\NewsCategoryModel;

class CategoryHelper
{

    public function __construct() { }


    /**
     * Get the category tree with all parent categories of the given category id
     *
     * @param       $intId     The category id
     * @param bool  $max_level Maximum level of parent categories that should be covered, set to null for unlimited execution, 0 for only current category with its parent
     * @param array $all       Required for recursion
     *
     * @return array|null
     */
    public function getCategoryTree($intId, $max_level = null, $all = [])
    {
        if (!is_numeric($max_level))
        {
            $max_level = null;
        }

        $category = NewsCategoryModel::findPublishedByIdOrAlias($intId);

        if ($category === null)
        {
            return null;
        }

        $category = (object) $category->row();

        // store parent within current category
        if (!empty($all))
        {
            $all[count($all) - 1]->parent = $category;

            if ($max_level !== null && $max_level <= count($all))
            {
                return array_reverse($all);  // sort in reverse order (parent to children)
            }
        }

        $all[] = $category;

        // no more parent category
        if ($category->pid == 0)
        {
            return array_reverse($all);  // sort in reverse order (parent to children)
        }

        return $this->getCategoryTree($category->pid, $max_level, $all);
    }
}