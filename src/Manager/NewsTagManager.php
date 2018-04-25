<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\Manager;

use Codefog\TagsBundle\Manager\DefaultManager;
use Codefog\TagsBundle\Model\TagModel;

class NewsTagManager extends DefaultManager
{
    /**
     * {@inheritdoc}
     */
    public function updateDcaField(array &$config): void
    {
        /** @var TagModel $adapter */
        $adapter = $this->framework->getAdapter(TagModel::class);

        $config['relation'] = array_merge(
            is_array($config['relation']) ? $config['relation'] : [],
            ['type' => 'haste-ManyToMany', 'load' => 'lazy', 'table' => $adapter->getTable()]
        );

        if (isset($config['save_callback']) && is_array($config['save_callback'])) {
            array_unshift($config['save_callback'], ['codefog_tags.listener.tag_manager', 'onFieldSave']);
        } else {
            $config['save_callback'][] = ['codefog_tags.listener.tag_manager', 'onFieldSave'];
        }
    }

    public function findByAlias(string $value, array $criteria = []): ?TagModel
    {
        /** @var TagModel $adapter */
        $adapter = $this->framework->getAdapter(TagModel::class);

        if (null === ($model = $adapter->findByAlias($value))) {
            return null;
        }

        $criteria = $this->getCriteria($criteria);

        // Check the source
        if ($model->source !== $criteria['source']) {
            return null;
        }

        return $model;
    }
}
