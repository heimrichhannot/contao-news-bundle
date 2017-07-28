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

class NewsFeedController extends Controller
{
    /**
     * Generates Feed by type
     *
     * @Route("/share/{type}", name="heimrichhannot_contao-newsbundle_dynamic_feed")
     */
    public function dynamicFeedByTypeAction($type)
    {
        
    }
}