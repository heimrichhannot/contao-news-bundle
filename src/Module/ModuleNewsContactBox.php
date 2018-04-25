<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Module;

use HeimrichHannot\NewsBundle\Model\NewsModel;
use Patchwork\Utf8;

class ModuleNewsContactBox extends \ModuleNews
{
    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'mod_news_contact_box';

    /**
     * Display a wildcard in the back end.
     *
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.Utf8::strtoupper($GLOBALS['TL_LANG']['FMD']['newsreader'][0]).' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        // Set the item from the auto_item parameter
        if (!isset($_GET['items']) && \Config::get('useAutoItem') && isset($_GET['auto_item'])) {
            \Input::setGet('items', \Input::get('auto_item'));
        }

        // Do not index or cache the page if no news item has been specified
        if (!\Input::get('items')) {
            return '';
        }

        $this->news_archives = $this->sortOutProtected(\StringUtil::deserialize($this->news_archives));

        // Do not index or cache the page if there are no archives
        if (!is_array($this->news_archives) || empty($this->news_archives)) {
            return '';
        }

        return parent::generate();
    }

    protected function compile()
    {
        // Get the news item
        $objArticle = NewsModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->news_archives);

        if (null === $objArticle || !$objArticle->addContactBox) {
            return '';
        }

        /**
         * @var \Twig_Environment
         */
        $twig = \System::getContainer()->get('twig');

        $contactMembers = $this->getContactMembers($objArticle);
        $contactLinks = $this->getContactLinks($objArticle);

        if (null !== $contactMembers) {
            $this->Template->contact_box = $twig->render(
                '@HeimrichHannotContaoNews/news/contact_box.html.twig',
                ['contactMembers' => $contactMembers, 'contactLinks' => $contactLinks, 'contactBox' => $objArticle->contactBox_header]
            );
        }
    }

    protected function getContactMembers($objArticle)
    {
        $contactMembers = null;
        $contacts = deserialize($objArticle->contactBox_members, true);
        foreach ($contacts as $contactId) {
            $contact = \Contao\MemberModel::findByPk($contactId);
            $contactMembers[] = [
                'first_name' => '' == $contact->firstname ? null : $contact->firstname,
                'last_name' => '' == $contact->lastname ? null : $contact->lastname,
                'phone' => '' == $contact->phone ? null : $contact->phone,
                'fax' => '' == $contact->fax ? null : $contact->fax,
                'email' => '' == $contact->email ? null : $contact->email,
                'website' => '' == $contact->website ? null : $contact->website,
            ];
        }

        return $contactMembers;
    }

    protected function getContactLinks($objArticle)
    {
        $contactLinks = deserialize($objArticle->contactBox_links, true);

        if (empty($contactLinks)) {
            $contactLinks = null;
        }

        return $contactLinks;
    }
}
