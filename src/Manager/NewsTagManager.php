<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Manager;


use Codefog\TagsBundle\Collection\CollectionInterface;
use Codefog\TagsBundle\Manager\DefaultManager;
use Codefog\TagsBundle\Model\TagModel;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;

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

        if (($model = $adapter->findByAlias($value)) === null) {
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