<?php

namespace Journal;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Loader;

class Recommended
{
    protected string $propertyCode;
    protected int $iblockId;
    protected int $limit;
    protected array $order = [
        'SORT' => 'ASC',
        'ID' => 'DESC',
    ];

    public function __construct(int $iblockId, string $propertyCode = 'RECOMMENDED', int $limit = 4)
    {
        Loader::includeModule('iblock');
        $this->iblockId = $iblockId;
        $this->propertyCode = $propertyCode;
        $this->limit = $limit;
    }

    public function setOrder(array $order): void
    {
        $this->order = $order;
    }

    public function recalculate(): void
    {
        $resource = $this->getElements(null, false, false);
        while ($elementData = $resource->Fetch()) {
            \CIBlockElement::SetPropertyValuesEx(
                $elementData['ID'],
                $this->iblockId,
                [$this->propertyCode => $this->calculateElement($elementData)]
            );
            $cache = new \CPHPCache();
            $cache->Clean(
                "cache_{$elementData['IBLOCK_ID']}_{$elementData['ID']}",
                '/catalog/recommended'
            );
        }
    }

    protected function getElementsFilter(array $filter = null, bool $activeOnly = true): array
    {
        $defaultFilter = ['IBLOCK_ID' => $this->iblockId];
        if ($activeOnly) {
            $defaultFilter['ACTIVE'] = 'Y';
        }
        return array_merge($filter ?? [], $defaultFilter);
    }

    protected static function invertOrder(array $order): array
    {
        foreach ($order as $key => &$value) {
            $value = mb_strtoupper(
                preg_replace(
                    '/[a-z,]/i',
                    null,
                    $value
                )
            );
            switch ($value) {
                case 'ASC':
                    $value = 'DESC';
                    break;
                case 'NULLS,ASC':
                    $value = 'DESC,NULLS';
                    break;
                case 'ASC,NULLS':
                    $value = 'NULLS,DESC';
                    break;
                case 'DESC':
                    $value = 'ASC';
                    break;
                case 'NULLS,DESC':
                    $value = 'ASC,NULLS';
                    break;
                case 'DESC,NULLS':
                    $value = 'NULLS,ASC';
                    break;
                default:
                    unset($order[$key]);
                    break;
            }
        }
        return $order;
    }

    protected function getElements(array $filter = null, bool $invert = false, bool $activeOnly = true): \CAllDBResult
    {
        return \CIBlockElement::GetList(
            $invert ? $this->invertOrder($this->order) : $this->order,
            $this->getElementsFilter($filter, $activeOnly),
            false,
            false,
            ['ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID']
        );
    }

    protected function calculateElement(array $calculateElementData): array
    {
        try {
            $recommendIds = [];

            // Получаем текущий раздел
            $sectionData = SectionTable::getList([
                'filter' => ['=ID' => intval($calculateElementData['IBLOCK_SECTION_ID'])],
                'select' => ['ID', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL']
            ])->fetch();

            if (!$sectionData) {
                return [];
            }

            // Получим все элементы текущего раздела
            $sectionElementsResource = $this->getElements([
                'SECTION_ID' => $sectionData['ID'],
                'INCLUDE_SUBSECTIONS' => 'Y',
            ]);

            // Если их больше чем нужно, выбираем соседей
            if ($sectionElementsResource->SelectedRowsCount() > ($this->limit + 1)) {
                $sectionSideElementsResource = \CIBlockElement::GetList(
                    ['SORT' => 'ASC', 'NAME' => 'ASC'],
                    $this->getElementsFilter([
                        'SECTION_ID' => $sectionData['ID'],
                        'INCLUDE_SUBSECTIONS' => 'Y',
                    ]),
                    false,
                    [
                        'nElementID' => intval($calculateElementData['ID']),
                        'nPageSize' => ceil($this->limit / 2),
                    ],
                    ['ID', 'IBLOCK_ID', 'NAME', 'SORT']
                );

                while ($elementData = $sectionSideElementsResource->Fetch()) {
                    if (intval($elementData['ID']) === intval($calculateElementData['ID'])) {
                        continue;
                    } elseif (count($recommendIds) >= $this->limit) {
                        break;
                    }
                    $recommendIds[] = intval($elementData['ID']);
                }
                unset($elementData);
            }

            // Добираем, если не хватает
            if (count($recommendIds) < $this->limit) {
                while ($elementData = $sectionElementsResource->Fetch()) {
                    if (intval($elementData['ID']) === intval($calculateElementData['ID'])) {
                        continue;
                    } elseif (in_array(intval($elementData['ID']), $recommendIds)) {
                        continue;
                    } elseif (count($recommendIds) >= $this->limit) {
                        break;
                    }
                    $recommendIds[] = intval($elementData['ID']);
                }
                unset($elementData);
            }

            // Шагаем по разделам вверх
            $sectionStepData = $sectionData;
            while ($sectionStepData && $sectionStepData['DEPTH_LEVEL'] > 1 && (count($recommendIds) < $this->limit)) {
                // Выбираем всех соседей, включая текущий
                $sideSectionsResource = SectionTable::getList([
                    'filter' => [
                        'ACTIVE' => 'Y',
                        '=IBLOCK_SECTION_ID' => intval($sectionStepData['IBLOCK_SECTION_ID']),
                    ],
                    'select' => ['ID', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL'],
                    'order' => ['SORT' => 'ASC', 'NAME' => 'ASC']
                ]);

                // Если раздел не имеет соседей, пытаемся получить все элементы, не включая подразделы, и добираем из них
                // Потом шаг вверх
                if ($sideSectionsResource->getSelectedRowsCount() === 1) {
                    $outElementsResource = $this->getElements([
                        'SECTION_ID' => intval($sectionStepData['IBLOCK_SECTION_ID']),
                        'INCLUDE_SUBSECTIONS' => 'N',
                    ]);
                    while ($elementData = $outElementsResource->Fetch()) {
                        if (intval($elementData['ID']) === intval($calculateElementData['ID'])) {
                            continue;
                        } elseif (in_array(intval($elementData['ID']), $recommendIds)) {
                            continue;
                        } elseif (count($recommendIds) >= $this->limit) {
                            break;
                        }
                        $recommendIds[] = intval($elementData['ID']);
                    }
                    unset($elementData);
                    $sectionStepData = SectionTable::getList([
                        'filter' => [
                            'ACTIVE' => 'Y',
                            '=ID' => intval($sectionStepData['IBLOCK_SECTION_ID']),
                        ],
                        'select' => ['ID', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL']
                    ])->fetch();
                    continue;
                }

                // Выбираем левый и правый раздел
                $sideSectionsMap = ['before' => false, 'after' => false];
                $isFindCurrentSection = false;
                while ($sideSectionData = $sideSectionsResource->fetch()) {
                    if (intval($sideSectionData['ID']) === intval($sectionStepData['ID'])) {
                        $isFindCurrentSection = true;
                        continue;
                    } elseif (!$isFindCurrentSection) {
                        $sideSectionsMap['before'] = $sideSectionData['ID'];
                        continue;
                    }
                    $sideSectionsMap['after'] = $sideSectionData['ID'];
                    break;
                }

                // Получаем левые элементы в обратном порядке
                if ($sideSectionsMap['before']) {
                    $sideBeforeSectionElementsResource = $this->getElements([
                        'SECTION_ID' => intval($sideSectionsMap['before']),
                        'INCLUDE_SUBSECTIONS' => 'Y',
                    ], true);
                }

                // Получаем правые элементы
                if ($sideSectionsMap['after']) {
                    $sideAfterSectionElementsResource = $this->getElements([
                        'SECTION_ID' => intval($sideSectionsMap['after']),
                        'INCLUDE_SUBSECTIONS' => 'Y',
                    ], false);
                }

                while (true) {
                    if (
                        isset($sideBeforeSectionElementsResource)
                        && ($sideBeforeSectionElementData = $sideBeforeSectionElementsResource->Fetch())
                    ) {
                        if (!in_array(intval($sideBeforeSectionElementData['ID']), $recommendIds)) {
                            $recommendIds[] = intval($sideBeforeSectionElementData['ID']);
                        }
                        $break = false;
                    } else {
                        $break = true;
                    }

                    if (count($recommendIds) >= $this->limit) {
                        break;
                    }

                    if (
                        isset($sideAfterSectionElementsResource)
                        && ($sideAfterSectionElementData = $sideAfterSectionElementsResource->Fetch())
                    ) {
                        if (!in_array(intval($sideAfterSectionElementData['ID']), $recommendIds)) {
                            $recommendIds[] = intval($sideAfterSectionElementData['ID']);
                        }
                        $break = false;
                    } else {
                        $break = boolval($break);
                    }

                    if ($break || count($recommendIds) >= $this->limit) {
                        break;
                    }
                }

                unset($sideSectionsMap, $isFindCurrentSection, $sideBeforeSectionElementData, $sideAfterSectionElementData);

                // Пытаемся получить все элементы родительского раздела, включая подразделы, и добираем из них
                if (count($recommendIds) < $this->limit) {
                    $allElementsResource = $this->getElements([
                        'SECTION_ID' => intval($sectionStepData['IBLOCK_SECTION_ID']),
                        'INCLUDE_SUBSECTIONS' => 'Y',
                    ]);
                    while ($elementData = $allElementsResource->Fetch()) {
                        if (intval($elementData['ID']) === intval($calculateElementData['ID'])) {
                            continue;
                        } elseif (in_array(intval($elementData['ID']), $recommendIds)) {
                            continue;
                        } elseif (count($recommendIds) >= $this->limit) {
                            break;
                        }
                        $recommendIds[] = intval($elementData['ID']);
                    }
                    unset($elementData);
                }

                // Шагаем вверх
                $sectionStepData = SectionTable::getList([
                    'filter' => [
                        'ACTIVE' => 'Y',
                        '=ID' => intval($sectionStepData['IBLOCK_SECTION_ID']),
                    ],
                    'select' => ['ID', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL']
                ])->fetch();
            }

            return $recommendIds;
        } catch (\Throwable $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
