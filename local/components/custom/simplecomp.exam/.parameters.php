<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentParameters = [
    'PARAMETERS' => [
        'PRODUCTS_IBLOCK_ID' => [
            'NAME' => GetMessage('SIMPLE_COMPONENT_PRODUCT_IBLOCK_ID'),
            'TYPE' => 'STRING'
        ],
        'NEWS_IBLOCK_ID' => [
            'NAME' => GetMessage('SIMPLE_COMPONENT_NEWS_IBLOCK_ID'),
            'TYPE' => 'STRING'
        ],
        'USER_FIELD_CODE' => [
            'NAME' => GetMessage('SIMPLE_COMPONENT_USER_FIELD_CODE'),
            'TYPE' => 'STRING'
        ],
        'CACHE_TIME'  =>  ["DEFAULT"=>3600]
    ]
];