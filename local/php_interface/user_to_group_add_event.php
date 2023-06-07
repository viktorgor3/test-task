<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandlerCompatible('main', 'OnAfterUserAdd', Array("UserEvenHandlerHelper", "OnAfterUserAddHandler"));
$eventManager->addEventHandlerCompatible('main', 'OnAfterUserUpdate', Array("UserEvenHandlerHelper", "OnAfterUserUpdateHandler"));



class UserEvenHandlerHelper
{
    const CONTENT_GROUP_ID = 21;
    public function OnAfterUserAddHandler(&$arFields)
    {

        if (!empty($arFields['GROUP_ID'])){
            if (in_array(self::CONTENT_GROUP_ID, $arFields['GROUP_ID'])){

                $groupId = $arFields['GROUP_ID'];


                $result = \Bitrix\Main\UserGroupTable::getList(array(
                    'filter' => array('GROUP_ID'=>$groupId,'USER.ACTIVE'=>'Y'),
                    'select' => array('USER_ID','NAME'=>'USER.NAME','LAST_NAME'=>'USER.LAST_NAME', 'USER_EMAIL' => 'USER.EMAIL'), // выбираем идентификатор п-ля, имя и фамилию
                ));

                $userName = $arFields['NAME'].' '.$arFields['LAST_NAME'];
                while ($arGroup = $result->fetch()) {
                   $resUsers[] = $arGroup['USER_EMAIL'];
                }

                $SITE_ID = 's1';
                $EVENT_TYPE = 'USER_ADD_TO_CONTENT_GROUP';
                $arFeedForm = array(
                    'EMAIL_TO' => implode(",", $resUsers),
                    "USER_NAME" => htmlspecialcharsEx($userName),
                );

                if ($result = CEvent::Send($EVENT_TYPE, $SITE_ID, $arFeedForm )){
                    $log = date('Y-m-d H:i:s') . ' ' . print_r($arFeedForm, true);
                    file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
                }
            }
        }
    }
    
    public function OnAfterUserUpdateHandler(&$arFields)
    {
        if (!empty($arFields['GROUP_ID'])){
            foreach ($arFields['GROUP_ID'] as $group){
                if ($group['GROUP_ID'] == self::CONTENT_GROUP_ID)
                    $groupId = $group['GROUP_ID'];
            }
        }

        if ($groupId){

            $groupUsers = \Bitrix\Main\UserGroupTable::getList(array(
                'filter' => array('GROUP_ID'=>$groupId,'USER.ACTIVE'=>'Y'),
                'select' => array('USER_ID','NAME'=>'USER.NAME','LAST_NAME'=>'USER.LAST_NAME', 'USER_EMAIL' => 'USER.EMAIL'), // выбираем идентификатор п-ля, имя и фамилию
            ));

            while ($arGroup = $groupUsers->fetch()) {
                $resUsers[] = $arGroup['USER_EMAIL'];
            }

            $userName = $arFields['NAME'].' '.$arFields['LAST_NAME'];
            $SITE_ID = 's1';
            $EVENT_TYPE = 'USER_ADD_TO_CONTENT_GROUP';
            $arFeedForm = array(
                'EMAIL_TO' => implode(",", $resUsers),
                "USER_NAME" => htmlspecialcharsEx($userName),
            );

            if ($result = CEvent::Send($EVENT_TYPE, $SITE_ID, $arFeedForm )){
                $log = date('Y-m-d H:i:s') . ' ' . print_r($arFeedForm, true);
                file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);
            }
        }
    }
}
