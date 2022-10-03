<?php
declare(strict_types=1);

namespace GeorgRinger\NewsImporticsxml\Mapper;

use GeorgRinger\NewsImporticsxml\Domain\Model\Dto\TaskConfiguration;
use PicoFeed\Config\Config;
use PicoFeed\Parser\Item;
use PicoFeed\Reader\Reader;
use SimpleXMLElement;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "news_importicsxml" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class XmlMapper extends AbstractMapper implements MapperInterface
{

    /**
     * @param TaskConfiguration $configuration
     * @return array
     */
    public function map(TaskConfiguration $configuration)
    {
        if ($configuration->getCleanBeforeImport()) {
            $this->removeImportedRecordsFromPid($configuration->getPid(), $this->getImportSource());
        }

        $data = [];

        $readerConfig = new Config();
        $readerConfig->setContentFiltering(false);
        $reader = new Reader($readerConfig);
        $resource = $reader->discover($configuration->getPath());

        $parser = $reader->getParser(
            $resource->getUrl(),
            $resource->getContent(),
            $resource->getEncoding()
        );

        $items = $parser->execute()->getItems();

        foreach ($items as $item) {
            $id = strlen($item->getId()) > 100 ? md5($item->getId()) : $item->getId();
            /** @var Item $item */

            $singleItem = [
                'import_source' => $this->getImportSource(),
                'import_id' => $id,
                'crdate' => $GLOBALS['EXEC_TIME'],
                'cruser_id' => isset($GLOBALS['BE_USER'], $GLOBALS['BE_USER']->user) ? $GLOBALS['BE_USER']->user['uid'] : 0,
                'type' => 0,
                'hidden' => 0,
                'pid' => $configuration->getPid(),
                'title' => $item->getTitle(),
                'bodytext' => $this->cleanup($item->getContent()),
                'author' => $item->getAuthor(),
                'datetime' => $item->getDate()->getTimestamp(),
                'categories' => $this->getCategories($item->xml, $configuration),
                '_dynamicData' => [
                    'reference' => $item,
                    'news_importicsxml' => [
                        'importDate' => date('d.m.Y h:i:s', $GLOBALS['EXEC_TIME']),
                        'feed' => $configuration->getPath(),
                        'url' => $item->getUrl(),
                        'guid' => $item->getTag('guid'),
                    ]
                ],
            ];
            $this->addRemoteFiles($singleItem, $item->xml, $id);
            if ($configuration->isPersistAsExternalUrl()) {
                $singleItem['type'] = 2;
                $singleItem['externalurl'] = $item->getUrl();
            }
            if ($configuration->isSetSlug()) {
                $singleItem['path_segment'] = $this->generateSlug($singleItem, $configuration->getPid());
            }

            $data[] = $singleItem;
        }
        return $data;
    }

    protected function addRemoteFiles(array &$singleItem, \SimpleXMLElement $xml, string $id)
    {
        $extensions = [
            'image/jpg' => 'jpg',
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        ];

        foreach ($xml->enclosure as $enclosure) {
            $url = (string)$enclosure->attributes()['url'];
            $mimeType = (string)$enclosure->attributes()['type'];

            if (!empty($url) && isset($extensions[$mimeType])) {
                GeneralUtility::mkdir_deep(Environment::getPublicPath() . '/uploads/tx_newsimporticsxml/');
                $file = 'uploads/tx_newsimporticsxml/' . $id . '_' . md5($url) . '.' . $extensions[$mimeType];
                if (is_file(Environment::getPublicPath() . '/' . $file)) {
                    $status = true;
                } else {
                    $content = GeneralUtility::getUrl($url);
                    $status = GeneralUtility::writeFile(Environment::getPublicPath() . '/' . $file, $content);
                }
                if ($status) {

                    if (in_array($extensions[$mimeType], ['gif', 'jpeg', 'jpg', 'png'], true)) {
                        $singleItem['media'][] = [
                            'image' => $file,
                            'showinpreview' => true
                        ];
                    } else {
                        $singleItem['related_files'][] = [
                            'file' => $file
                        ];
                    }
                }
            }
        }
    }

    /**
     * @param SimpleXMLElement $xml
     * @param TaskConfiguration $configuration
     * @return array
     */
    protected function getCategories(SimpleXMLElement $xml, TaskConfiguration $configuration)
    {
        $categoryIds = $categoryTitles = [];
        $categories = $xml->category;
        if ($categories) {
            foreach ($categories as $cat) {
                $categoryTitles[] = (string)$cat;
            }
        }
        if (!empty($categoryTitles)) {
            if (!$configuration->getMapping()) {
                $this->logger->info('Categories found during import but no mapping assigned in the task!');
            } else {
                $categoryMapping = $configuration->getMappingConfigured();
                foreach ($categoryTitles as $title) {
                    if (!isset($categoryMapping[$title])) {
                        $this->logger->warning(sprintf('Category mapping is missing for category "%s"', $title));
                    } else {
                        $categoryIds[] = $categoryMapping[$title];
                    }
                }
            }
        }

        return $categoryIds;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function cleanup($content): string
    {
        $search = ['<br />', '<br>', '<br/>', LF . LF];
        $replace = [LF, LF, LF, LF];
        return str_replace($search, $replace, $content);
    }

    /**
     * @return string
     */
    public function getImportSource(): string
    {
        return 'newsimporticsxml_xml';
    }
}
