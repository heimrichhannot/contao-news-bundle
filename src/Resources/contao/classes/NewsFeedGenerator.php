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

use Codefog\TagsBundle\Model\TagModel;
use HeimrichHannot\NewsBundle\NewsModel;
use HeimrichHannot\NewsBundle\NewsTagsModel;

class NewsFeedGenerator extends \Frontend
{

    public function generateFeedsByTag ()
    {

//        $objNews = NewsTagsModel::findNewsByTagId(308);
//
//        if ($objNews === null) {
//            return null;
//        }


//        $tag = TagModel::findByIdOrAlias(308);
//        $news = NewsModel::findBy('tags', $tag);
//
//
//        $tags = TagModel::findAll();
//
//        $news = NewsModel::findAll();
//
//        if ($tags === null)
//        {
//            return;
//        }
//
//
//
//        foreach ($tags as $tag)
//        {
//            $news = NewsModel::findBy('tags', $tag->getId());
//            if ($news === null) {
//                break;
//            }
//        }
    }

}