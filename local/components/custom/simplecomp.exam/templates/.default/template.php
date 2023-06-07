<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @var $arResult array
 */

echo '---<br/><br/>';
echo '<b>'.GetMessage('CATALOG').'</b><br/>';
echo '<ul>';
foreach ($arResult['ITEMS'] as $item){

    $str = '';
    foreach ($item['SECTIONS'] as $section) {
        $str .= ', ' . $arResult['ALL_SECTIONS'][$section]['NAME'];
    }
    echo '<li><b>'.$item['NAME'].'</b> - '.$item['ACTIVE_FROM'].' ('.mb_substr($str, 2).')';
    echo '<ul>';
        foreach ($item['PRODUCTS'] as $product){
            $arProduct = $arResult['ALL_PRODUCTS'][$product];
            echo '<li>'.$arProduct['NAME'].' - '.$arProduct['PRICE'].' - '.$arProduct['MATERIAL'].' - '.$arProduct['ARTNUMBER'].'</li>';
        }
    echo '</ul>';
    echo '</li>';
}
echo '</ul>';