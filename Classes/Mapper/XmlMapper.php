<?php
namespace Cyberhouse\NewsImporticsxml\Mapper;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Cyberhouse\NewsImporticsxml\Domain\Model\Dto\TaskConfiguration;
use PicoFeed\Reader\Reader;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class XmlMapper extends AbstractMapper implements MapperInterface
{

    /**
     * @param TaskConfiguration $configuration
     * @return array
     */
    public function map(TaskConfiguration $configuration)
    {
        $data = array();
        
        $config = new Config();

        if ($GLOBALS['TYPO3_CONF_VARS']['HTTP']['proxy_host'] && $GLOBALS['TYPO3_CONF_VARS']['HTTP']['proxy_port']) {
            $config->setProxyHostname($GLOBALS['TYPO3_CONF_VARS']['HTTP']['proxy_host']);
            $config->setProxyPort($GLOBALS['TYPO3_CONF_VARS']['HTTP']['proxy_port']);
            if ($GLOBALS['TYPO3_CONF_VARS']['HTTP']['proxy_user']) {
                $config->setProxyUsername($GLOBALS['TYPO3_CONF_VARS']['HTTP']['proxy_user']);
            }
            if ($GLOBALS['TYPO3_CONF_VARS']['HTTP']['proxy_password']) {
                $config->setProxyPassword($GLOBALS['TYPO3_CONF_VARS']['HTTP']['proxy_password']);
            }
        }

        $reader = new Reader($config);
        $resource = $reader->discover($configuration->getPath());

        $parser = $reader->getParser(
            $resource->getUrl(),
            $resource->getContent(),
            $resource->getEncoding()
        );

        $items = $parser->execute()->getItems();

        foreach ($items as $item) {
            /** @var \PicoFeed\Parser\Item $item */
            $data[] = array(
                'import_source' => $this->getImportSource(),
                'import_id' => $item->getId(),
                'crdate' => $GLOBALS['EXEC_TIME'],
                'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
                'pid' => $configuration->getPid(),
                'title' => $item->getTitle(),
                'bodytext' => $this->cleanup($item->getContent()),
                'author' => $item->getAuthor(),
                'datetime' => $item->getDate()->getTimestamp(),
                'categories' => $this->getCategories($item->xml, $configuration),
                '_dynamicData' => array(
                    'reference' => $item,
                    'news_importicsxml' => array(
                        'importDate' => date('d.m.Y h:i:s', $GLOBALS['EXEC_TIME']),
                        'feed' => $configuration->getPath(),
                        'url' => $item->getUrl(),
                        'guid' => $item->getTag('guid'),
                    )
                ),
            );
        }

        return $data;
    }

    /**
     * @param \SimpleXMLElement $xml
     * @param TaskConfiguration $configuration
     * @return array
     */
    protected function getCategories(\SimpleXMLElement $xml, TaskConfiguration $configuration)
    {
        $categoryIds = $categoryTitles = array();
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
        $search = array('<br />', '<br>', '<br/>', LF . LF);
        $replace = array(LF, LF, LF, LF);
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
