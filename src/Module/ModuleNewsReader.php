<?php
/**
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Module;


use Contao\CoreBundle\Exception\PageNotFoundException;

class ModuleNewsReader extends \ModuleNewsReader
{
    /**
     * Generate the module
     */
    protected function compile()
    {
        $this->Template->articles = '';
        $this->Template->referer  = 'javascript:history.go(-1)';
        $this->Template->back     = $GLOBALS['TL_LANG']['MSC']['goBack'];

        // Get the news item
        $objArticle = \NewsModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->news_archives);

        if (null === $objArticle) {
            throw new PageNotFoundException('Page not found: ' . \Environment::get('uri'));
        }

        $arrArticle               = $this->parseArticle($objArticle);
        $this->Template->articles = $arrArticle;

        $bundles = \System::getContainer()->getParameter('kernel.bundles');

        // HOOK: comments extension required
        if ($objArticle->noComments || !isset($bundles['ContaoCommentsBundle'])) {
            $this->Template->allowComments = false;

            return;
        }

        /** @var \NewsArchiveModel $objArchive */
        $objArchive                    = $objArticle->getRelated('pid');
        $this->Template->allowComments = $objArchive->allowComments;

        // Comments are not allowed
        if (!$objArchive->allowComments) {
            return;
        }

        // Adjust the comments headline level
        $intHl               = min(intval(str_replace('h', '', $this->hl)), 5);
        $this->Template->hlc = 'h' . ($intHl + 1);

        $this->import('Comments');
        $arrNotifies = [];

        // Notify the system administrator
        if ($objArchive->notify != 'notify_author') {
            $arrNotifies[] = $GLOBALS['TL_ADMIN_EMAIL'];
        }

        // Notify the author
        if ($objArchive->notify != 'notify_admin') {
            /** @var \UserModel $objAuthor */
            if (($objAuthor = $objArticle->getRelated('author')) instanceof \UserModel && $objAuthor->email != '') {
                $arrNotifies[] = $objAuthor->email;
            }
        }

        $objConfig = new \stdClass();

        $objConfig->perPage        = $objArchive->perPage;
        $objConfig->order          = $objArchive->sortOrder;
        $objConfig->template       = $this->com_template;
        $objConfig->requireLogin   = $objArchive->requireLogin;
        $objConfig->disableCaptcha = $objArchive->disableCaptcha;
        $objConfig->bbcode         = $objArchive->bbcode;
        $objConfig->moderate       = $objArchive->moderate;

        $this->Comments->addCommentsToTemplate($this->Template, $objConfig, 'tl_news', $objArticle->id, $arrNotifies);
    }
}