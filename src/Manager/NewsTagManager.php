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


use Codefog\TagsBundle\Manager\DefaultManager;
use Codefog\TagsBundle\Model\TagModel;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;

class NewsTagManager extends DefaultManager
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var string
     */
    private $sourceTable;

    /**
     * @var string
     */
    private $sourceField;

    /**
     * DefaultManager constructor.
     *
     * @param ContaoFrameworkInterface $framework
     * @param string                   $sourceTable
     * @param string                   $sourceField
     */

    public function __construct(ContaoFrameworkInterface $framework, string $sourceTable, string $sourceField)
    {
        $this->framework   = $framework;
        $this->sourceTable = $sourceTable;
        $this->sourceField = $sourceField;
        parent::__construct($framework, $sourceTable, $sourceField);
    }

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

        if (isset($config['save_callback']) && is_array($config['save_callback']))
        {
            array_unshift($config['save_callback'], ['codefog_tags.listener.tag_manager', 'onFieldSave']);
        }
        else
        {
            $config['save_callback'][] = ['codefog_tags.listener.tag_manager', 'onFieldSave'];
        }
    }
}