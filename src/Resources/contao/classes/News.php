<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsBundle;


use HeimrichHannot\NewsBundle\Component\NewsFeedGenerator;

class News extends \NewsCategories\News
{



    /**
     * Generate an XML files and save them to the root directory
     *
     * @param array
     */
    protected function generateFiles($arrFeed)
    {
        if ($arrFeed["feedGeneration"] == NewsFeedGenerator::FEEDGENERATION_DYNAMIC)
        {

        }
        else
        {
            return parent::generateFiles($arrFeed);
        }



    }

}