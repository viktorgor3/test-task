<?php

class SimpleCatalog extends \CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        global $APPLICATION;

        $arResult = &$this->arResult;

        $arParams['CACHE_TIME'] = intval($this->arParams['CACHE_TIME']);
        //echo '<pre>'; print_r($arParams); echo '</pre>';
        if(!\Bitrix\Main\Loader::includeModule('iblock')) {
            throw new \Bitrix\Main\SystemException("IBLOCK_MODULE_ERROR_CONNECTION");
        }
        $CACHE_ID = SITE_ID."|".$APPLICATION->GetCurPage()."|";
        foreach ($this->arParams as $k => $v)
            if (strncmp("~", $k, 1))
                $CACHE_ID .= ",".$k."=".$v;
        $CACHE_ID .= "|".$USER->GetGroups();




        if($this->startResultCache($arParams["CACHE_TIME"], $CACHE_ID, "/".SITE_ID.$this->GetRelativePath())) {

            if (!$iblock_product = (int)$this->arParams['PRODUCTS_IBLOCK_ID'])
                return false;

            if (!$iblock_news = (int)$this->arParams['NEWS_IBLOCK_ID'])
                return false;

            if (!$user_field = trim($this->arParams['USER_FIELD_CODE']))
                return false;


            $arSections = [];
            $arNewsIds = [];
            $entity = \Bitrix\Iblock\Model\Section::compileEntityByIblock($iblock_product);

            $resSections = $entity::getList([
                "filter" => [
                    "IBLOCK_ID" => $iblock_product,
                    "ACTIVE" => "Y",
                    "GLOBAL_ACTIVE" => "Y"
                ],
                "select" => [
                    'ID',
                    'NAME',
                    $user_field
                ],
            ]);

            while ($arSection = $resSections->fetch()) {
                $arSections[$arSection['ID']] = [
                    'NAME' => $arSection['NAME'],
                    'NEWS' => $arSection[$user_field]
                ];

                foreach ($arSection[$user_field] as $newsId){
                    if (!in_array($newsId, $arNewsIds))
                        $arNewsIds[] = $newsId;
                }
            }

            $arNews = [];
            $resNews = \Bitrix\Iblock\ElementTable::getList(array(
                'filter' => [
                    "IBLOCK_ID" => $iblock_news,
                    "ID" => $arNewsIds,
                    "ACTIVE" => "Y",
                ],
                'select' => [
                    'ID',
                    'NAME',
                    'ACTIVE_FROM',
                ],
            ))->fetchAll();

            foreach ($resNews as $news){
                $arNews[$news['ID']] = [
                    'NAME' => $news['NAME'],
                    'ACTIVE_FROM' => $news['ACTIVE_FROM'],
                    'SECTIONS' => [],
                    'PRODUCTS' => []
                ];
            }


            $arProducts = [];
            $resProducts = \Bitrix\Iblock\Elements\ElementTestProductsTable::getList(array(
                'filter' => [
                    "IBLOCK_ID" => $iblock_product,
                    "IBLOCK_SECTION_ID" => array_keys($arSections),
                    "ACTIVE" => "Y",
                ],
                'select' => [
                    'ID',
                    'NAME',
                    'IBLOCK_SECTION',
                    'PRICE',
                    'MATERIAL',
                    'ARTNUMBER'
                ],
            ))->fetchCollection();


            foreach ($resProducts as $product){
                $arProducts[$product->getId()] = [
                    'NAME' => $product->getName(),
                    'PRICE' => $product->getPrice()->getValue(),
                    'MATERIAL' => $product->getMaterial()->getValue(),
                    'ARTNUMBER' => $product->getArtnumber()->getValue()
                ];
                $iblock_section_id = $product->getIblockSection()->getId();
                foreach ($arSections[$product->getIblockSection()->getId()]['NEWS'] as $newsId){
                    $arNews[$newsId]['PRODUCTS'][] = $product->getId();

                    if (!in_array($iblock_section_id, $arNews[$newsId]['SECTIONS'])){
                        $arNews[$newsId]['SECTIONS'][] = $product->getIblockSection()->getId();
                    }
                }
            }

            $arResult['ITEMS'] = $arNews;
            $arResult['ALL_PRODUCTS'] = $arProducts;
            $arResult['ALL_SECTIONS'] = $arSections;
            $arResult['COUNT_PRODUCTS'] = count($arProducts);

            $this->setResultCacheKeys(['COUNT_PRODUCTS']);

            if(empty($arResult)) {
                $this->abortResultCache();
            }

            $this->includeComponentTemplate();
        }
        //$APPLICATION->SetTitle(GetMessage('SIMPLE_COMPONENT_TITLE').$arResult['COUNT_PRODUCTS']);
    }
}