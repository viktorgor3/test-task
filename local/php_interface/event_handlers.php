<?
const IBLOCK_CODE = 'furniture_products_s1';

\Bitrix\Main\Loader::includeModule('iblock');

AddEventHandler('iblock', 'OnBeforeIBlockElementUpdate', 'iblockChangesChecking');

function getIblockId($code){

    $arIblockId = \Bitrix\Iblock\IblockTable::getList(array(
        'filter' => array('CODE' => $code),
        'select' => array('ID')
    ))->fetch();

    $iblockId = $arIblockId['ID'];

    return $iblockId;
}

function iblockChangesChecking(&$arFields){

    $iblock_id = getIblockId(IBLOCK_CODE);
    if ($arFields['IBLOCK_ID'] == $iblock_id && $arFields['ACTIVE'] == 'N'){
        $res = CIblockElement::getList([], ['IBLOCK_ID' => $iblock_id, 'ACTIVE' => 'Y', 'ID' => $arFields['ID'], '>SHOW_COUNTER' => 0], false, false, ['IBLOCK_ID', 'ID', 'SHOW_COUNTER']);

        if ($element = $res->fetch()){
            global $APPLICATION;
            $APPLICATION->throwException(str_replace('#COUNT#', $element['SHOW_COUNTER'], GetMessage('DEACTIVATION_ERROR')));
            return false;
        }
    }
}


