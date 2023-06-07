<?

if (file_exists($_SERVER['DOCUMENT_ROOT'] ."/local/php_interface/event_handlers.php")) {
    require_once($_SERVER['DOCUMENT_ROOT'] ."/local/php_interface/event_handlers.php");
}

if (file_exists($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/user_to_group_add_event.php'))
    require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/user_to_group_add_event.php');