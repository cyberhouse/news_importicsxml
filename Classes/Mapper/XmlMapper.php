<?php
namespace GeorgRinger\NewsImporticsxml\Mapper;

/**
 * This file is part of the "news_importicsxml" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use GeorgRinger\NewsImporticsxml\Domain\Model\Dto\TaskConfiguration;
use PicoFeed\Parser\Item;
use PicoFeed\Reader\Reader;
use SimpleXMLElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class XmlMapper extends AbstractMapper implements MapperInterface
{

    /**
     * @param TaskConfiguration $configuration
     * @return array
     */
    public function map(TaskConfiguration $configuration)
    {
        $data = [];

        $reader = new Reader();
        $resource = $reader->discover($configuration->getPath());

        $parser = $reader->getParser(
            $resource->getUrl(),
            $resource->getContent(),
            $resource->getEncoding()
        );

        $items = $parser->execute()->getItems();

        foreach ($items as $item) {
            /** @var Item $item */
            $singleItem = [
                'import_source' => $this->getImportSource(),
                'import_id' => $item->getId(),
                'crdate' => $GLOBALS['EXEC_TIME'],
                'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
                'type' => 1,
                'pid' => $configuration->getPid(),
                'title' => $item->getTitle(),
                'bodytext' => $this->cleanup($item->getContent()),
                'author' => $item->getAuthor(),
                'media' => $this->getRemoteFile($item->getEnclosureUrl(), $item->getEnclosureType(), $item->getId()),
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
            if ($configuration->isPersistAsExternalUrl()) {
                $singleItem['type'] = 2;
                $singleItem['externalurl'] = $item->getUrl();
            }

            $data[] = $singleItem;
        }

        return $data;
    }

    protected function getRemoteFile($url, $mimeType, $id)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/gif' => 'gif',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        ];

        $media = [];
        if (!empty($url) && isset($extensions[$mimeType])) {
            $file = 'uploads/tx_newsimporticsxml/' . $id . '_' . md5($url) . '.' . $extensions[$mimeType];
            if (is_file(PATH_site . $file)) {
                $status = true;
            } else {
                $content = GeneralUtility::getUrl($url);
                $status = GeneralUtility::writeFile(PATH_site . $file, $content);
            }

            if ($status) {
                $media[] = [
                    'image' => $file,
                    'showinpreview' => true
                ];
            }
        }
        return $media;
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
    protected function cleanup($content)
    {
        $search = ['<br />', '<br>', '<br/>', LF . LF];
        $replace = [LF, LF, LF, LF];
        $out = str_replace($search, $replace, $content);
        return $out;
    }

    /**
     * @return string
     */
    public function getImportSource()
    {
        return 'newsimporticsxml_xml';
    }
}
