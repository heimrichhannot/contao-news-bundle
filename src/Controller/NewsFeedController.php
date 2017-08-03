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


use Haste\Http\Response\XmlResponse;
use HeimrichHannot\NewsBundle\Component\FeedSourceInterface;
use HeimrichHannot\NewsBundle\Component\NewsFeedGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class NewsFeedController extends Controller
{
    /**
     * @param $alias
     *
     * @return Response
     *
     * @Route("/share/{alias}/source_channels.{_format}",
     *     defaults={"_format"="json"},
     *     name="hh_newsbundle_dynamicfeed_channels",
     *     requirements={
     *         "_format": "json"
     *     })
     * @Route("/share/{alias}/source_channels", defaults={"_format"="json"})
     * @Route("/share/{alias}/source_channels/", defaults={"_format"="json"})
     */
    public function dynamicFeedByAliasChannels($alias, $_format)
    {
        $this->container->get('contao.framework')->initialize();

        $objFeed = \NewsFeedModel::findByIdOrAlias($alias);
        if ($objFeed === null)
        {
            throw $this->createNotFoundException('The rss feed you try to access does not exist.');
        }
        /**
         * @var FeedSourceInterface $objSource
         */
        $objSource = $this->container->get('hh.news-bundle.news_feed_generator')->getFeedSource($objFeed->news_source);
        $objChannels = $objSource->getChannels();
        $arrChannels = [];
        while ($objChannels->next())
        {
            $arrChannel = $objChannels->row();
            if (!isset($arrChannel['name']) && isset($arrChannel['title']))
            {
                $arrChannel['name'] = $arrChannel['title'];
            }
            $arrChannels[] = $arrChannel;
        }
        switch ($_format)
        {
            default:
            case "json":
                return new JsonResponse($arrChannels);
        }
    }

    /**
     * Generates Feed by type
     *
     * @param string|id $alias
     *
     * @return Response
     *
     * @Route("/share/{alias}.{_format}", name="hh_newsbundle_dynamicfeed", defaults={"_format"="xml"})
     * @Route("/share/{alias}", defaults={"_format"="xml"})
     */
    public function dynamicFeedByAliasAction($alias)
    {
        $this->container->get('contao.framework')->initialize();

        $objFeed = \NewsFeedModel::findByIdOrAlias($alias);
        if ($objFeed === null)
        {
            throw $this->createNotFoundException('The rss feed you try to access does not exist.');
        }
        $objFeed->feedName = $objFeed->alias ?: 'news' . $objFeed->id;

        $strFeed = $this->container->get('hh.news-bundle.news_feed_generator')->generateFeed($objFeed->row());
        return new Response($strFeed);
    }

    /**
     * Generate feed by alias and type id
     *
     * @param string|id $alias
     * @param string|id $id
     *
     * @return Response
     *
     * @Route("/share/{alias}/{id}.{_format}", name="hh_newsbundle_dynamicfeed_single", defaults={"_format"="xml"})
     * @Route("/share/{alias}/{id}.{_format}/{count}", defaults={"_format"="xml"})
     * @Route("/share/{alias}/{id}", defaults={"_format"="xml"})
     */
    public function dynamicFeedByAliasAndIdAction($alias, $id, $count = 0)
    {
        $this->container->get('contao.framework')->initialize();

        $objFeed = \NewsFeedModel::findByIdOrAlias($alias);
        if ($objFeed === null)
        {
            throw $this->createNotFoundException('The rss feed you try to access does not exist.');
        }
        $objFeed->feedName = $objFeed->alias ?: 'news' . $objFeed->id;
        if (is_numeric($id))
        {
            $id = intval($id);
        }
        /**
         * @var NewsFeedGenerator $objNewsGeneratior
         */
        $objNewsGeneratior = $this->container->get('hh.news-bundle.news_feed_generator');
        if (is_numeric($count))
        {
            $objNewsGeneratior->setMaxItems(intval($count));
        }
        $strFeed = $objNewsGeneratior->generateFeed($objFeed->row(), $id);
        return new Response($strFeed);
    }
}