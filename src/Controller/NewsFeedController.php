<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\NewsBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class NewsFeedController extends Controller
{
    /**
     * Generates Feed by type
     *
     * @Route("/share/{alias}", name="heimrichhannot_newsbundle_dynamic_feed",defaults={"_format"="xml"})
     */
    public function dynamicFeedByAliasAction($alias)
    {
        $this->container->get('contao.framework')->initialize();

        $objFeed = \NewsFeedModel::findByAlias($alias);
        if ($objFeed === null)
        {
            throw $this->createNotFoundException('The rss feed you try to access does not exist.');
        }
        $objFeed->feedName = $objFeed->alias ?: 'news' . $objFeed->id;

        $strFeed = $this->container->get('app.news_feed_generator')->generateFeed($objFeed->row());
        return new Response($strFeed);
    }

    /**
     * Generate feed by alias and type id
     *
     * @param string $alias
     * @param string|id $id
     *
     * @return Response
     *
     * @Route("/share/{alias}/{id}", name="heimrichhannot_newsbundle_dynamic_feed_single", defaults={"_format"="xml"})
     */
    public function dynamicFeedByAliasAndIdAction($alias, $id)
    {
        $this->container->get('contao.framework')->initialize();

        $objFeed = \NewsFeedModel::findByAlias($alias);
        if ($objFeed === null)
        {
            throw $this->createNotFoundException('The rss feed you try to access does not exist.');
        }
        $objFeed->feedName = $objFeed->alias ?: 'news' . $objFeed->id;
        if (is_numeric($id))
        {
            $id = intval($id);
        }
        $strFeed = $this->container->get('app.news_feed_generator')->generateFeed($objFeed->row(), $id);
        return new Response($strFeed);
    }
}