<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2017 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\NewsBundle\Choices;


use Dav\LawyerSearchBundle\Component\RestClient;
use HeimrichHannot\NewsBundle\Module\ModuleNewsListFilter;
use HeimrichHannot\NewsBundle\NewsFilter\NewsFilterModule;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

abstract class AbstractChoice
{
    /**
     * Context data
     * @var array
     */
    protected $data = [];

    /**
     * Current file cache
     *
     * @var FilesystemAdapter
     */
    protected $cache;

    /**
     * Current cache key name
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * Current filter module
     * @var NewsFilterModule
     */
    protected $filter;

    /**
     * Configuration object
     *
     * @var mixed
     */
    protected $config;

    public function __construct(NewsFilterModule $filter, $data = [])
    {
        $cacheKey = 'choice.' . str_replace('Choice', '', (new \ReflectionClass($this))->getShortName());
        $cacheKey .= $filter->getAlias();

        $this->cache    = new FilesystemAdapter('', 0, \System::getContainer()->get('kernel')->getCacheDir());
        $this->cacheKey = $cacheKey;
        $this->data     = $data;
        $this->filter   = $filter;
    }

    public static function create(NewsFilterModule $filter, $data = [])
    {
        return new static($filter, $data);
    }

    public function getChoices()
    {
        $cache = $this->cache->getItem($this->cacheKey);

        if (!$cache->isHit() || empty($cache->get())) {
            $choices = $this->collectChoices();

            if (!is_array($choices)) {
                $choices = [];
            }

            // TODO: clear cache on delegated field save_callback
            $cache->expiresAfter(\DateInterval::createFromDateString('4 hour'));
            $cache->set($choices);

            $this->cache->save($cache);
        }

        return $cache->get();
    }

    /**
     * @return array
     */
    abstract protected function collectChoices();
}