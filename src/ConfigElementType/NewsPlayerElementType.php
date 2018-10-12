<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewsBundle\ConfigElementType;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\File;
use Contao\FilesModel;
use Contao\ImageSizeModel;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\NewsBundle\Model\NewsModel;
use HeimrichHannot\ReaderBundle\ConfigElementType\ConfigElementType;
use HeimrichHannot\ReaderBundle\Item\ItemInterface;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use Psr\Log\LogLevel;

class NewsPlayerElementType implements ConfigElementType
{
    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    /**
     * @param ItemInterface            $item
     * @param ReaderConfigElementModel $readerConfigElement
     *
     * @return string
     */
    public function addToItemData(ItemInterface $item, ReaderConfigElementModel $readerConfigElement)
    {
        $newsModel = $this->framework->getAdapter(NewsModel::class)->findByPk($item->getRaw()['id']);

        if (null === $newsModel) {
            return '';
        }

        if (!$newsModel->player || 'none' == $newsModel->player) {
            return '';
        }

        global $objPage;

        $isVideo = false;
        $sources = [];

        switch ($newsModel->player) {
            case 'internal':

                $uuid = StringUtil::deserialize($newsModel->playerSRC);

                if (!is_array($uuid) || empty($uuid)) {
                    return '';
                }

                $files = $this->framework->getAdapter(FilesModel::class)->findMultipleByUuidsAndExtensions($uuid, ['mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv', 'm4a', 'mp3', 'wma', 'mpeg', 'wav', 'ogg']);

                if (null === $files) {
                    return '';
                }

                // Pre-sort the array by preference
                if (in_array($files->first()->extension, ['mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv'], true)) {
                    $isVideo = true;
                    $sources = ['mp4' => null, 'm4v' => null, 'mov' => null, 'wmv' => null, 'webm' => null, 'ogv' => null];
                } else {
                    $isVideo = false;
                    $sources = ['m4a' => null, 'mp3' => null, 'wma' => null, 'mpeg' => null, 'wav' => null, 'ogg' => null];
                }

                $files->reset();

                // Convert the language to a locale (see #5678)
                $language = str_replace('-', '_', $objPage->language);

                // Pass File objects to the template
                foreach ($files as $file) {
                    $arrMeta = StringUtil::deserialize($file->meta);

                    if (is_array($arrMeta) && isset($arrMeta[$language])) {
                        $strTitle = $arrMeta[$language]['title'];
                    } else {
                        $strTitle = $file->name;
                    }

                    if (!isset($GLOBALS['TL_MIME'][$file->extension])) {
                        continue;
                    }

                    try {
                        $newFile = new File($file->path);
                    } catch (\Exception $exception) {
                        System::getContainer()->get('monolog.logger.contao')->log(LogLevel::ERROR, $exception->getMessage());

                        return '';
                    }
                    $newFile->title = StringUtil::specialchars($strTitle);
                    $newFile = $newFile->getModel()->row();
                    $newFile['mime'] = $GLOBALS['TL_MIME'][$file->extension][0];
                    $sources[$file->extension] = $newFile;
                }

                break;
            case 'external':
                $paths = StringUtil::trimsplit('|', $newsModel->playerUrl);

                if (!is_array($paths) || empty($paths)) {
                    return '';
                }

                $extension = pathinfo($paths[0], PATHINFO_EXTENSION);

                // Pre-sort the array by preference
                if (in_array($extension, ['mp4', 'm4v', 'mov', 'wmv', 'webm', 'ogv'], true)) {
                    $isVideo = true;
                    $sources = ['mp4' => null, 'm4v' => null, 'mov' => null, 'wmv' => null, 'webm' => null, 'ogv' => null];
                } else {
                    $isVideo = false;
                    $sources = ['m4a' => null, 'mp3' => null, 'wma' => null, 'mpeg' => null, 'wav' => null, 'ogg' => null];
                }

                // set source by extension
                foreach ($paths as $path) {
                    $extension = pathinfo($path, PATHINFO_EXTENSION);

                    if (!isset($GLOBALS['TL_MIME'][$extension])) {
                        continue;
                    }

                    $file = [];
                    $file['mime'] = $GLOBALS['TL_MIME'][$extension][0];
                    $file['path'] = System::getContainer()->get('huh.utils.url')->addURIScheme($path);
                    $sources[$extension] = $file;
                }

                break;
        }

        $templateData['poster'] = false;
        $posterSRC = $newsModel->posterSRC ?: $readerConfigElement->posterSRC;

        // Optional poster
        if ('' != $posterSRC) {
            if (null !== ($poster = $this->framework->getAdapter(FilesModel::class)->findByUuid($posterSRC))) {
                $templateData['poster'] = $poster->path;
            }
        }

        $size = StringUtil::deserialize($readerConfigElement->imgSize, true);

        if ($isVideo) {
            $templateData['size'] = 'width="640" height="360"';

            if ($size[0] > 0 || $size[1] > 0) {
                $templateData['size'] = 'width="'.$size[0].'" height="'.$size[1].'"';
            } else {
                if (is_numeric($size[2])) {
                    /** @var ImageSizeModel $imageModel */
                    $imageModel = $this->framework->getAdapter(ImageSizeModel::class);
                    $imageSize = $imageModel->findByPk($size[2]);

                    if (null !== $imageSize) {
                        $templateData['size'] = 'width="'.$imageSize->width.'" height="'.$imageSize->height.'"';
                    }
                }
            }
        } else {
            if ('' != $templateData['poster']) {
                $image = ['singleSRC' => $templateData['poster'], 'size' => serialize([640, 360])];

                if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2])) {
                    $image['size'] = $readerConfigElement->imgSize;
                }
                $templateData['image'] = [];
                System::getContainer()->get('huh.utils.image')->addToTemplateData('image', 'published', $templateData, $image, null, null, null, $poster);
            }
        }

        $templateData['files'] = array_values(array_filter($sources));
        $templateData['isVideo'] = $isVideo;

        $item->setFormattedValue('newsPlayer', $templateData);
    }
}
