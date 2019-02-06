<?php

/*
 * Copyright (c) 2019 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Item;

class DefaultListItem extends \HeimrichHannot\ListBundle\Item\DefaultItem
{
    use NewsItemTrait;
    const SESSION_SEEN_NEWS = 'SESSION_SEEN_NEWS';
}
