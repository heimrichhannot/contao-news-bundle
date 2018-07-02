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


use Contao\DataContainer;

class News extends \Contao\News
{
    const XHR_READER_SURVEY_RESULT_ACTION = 'showReadersSurveyResultAction';

    const XHR_GROUP = 'hh_news_bundle';

    const XHR_PARAMETER_ID    = 'id';
    const XHR_PARAMETER_ITEMS = 'items';



}