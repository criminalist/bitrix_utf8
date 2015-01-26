<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Форум");
?><?$APPLICATION->IncludeComponent(
	"bitrix:forum",
	"",
	Array(
		"THEME" => "blue",
		"SHOW_TAGS" => "Y",
		"SEO_USER" => "Y",
		"SHOW_FORUM_USERS" => "Y",
		"SHOW_SUBSCRIBE_LINK" => "N",
		"SHOW_AUTH_FORM" => "Y",
		"SHOW_NAVIGATION" => "Y",
		"SHOW_LEGEND" => "Y",
		"SHOW_STATISTIC_BLOCK" => array("STATISTIC","BIRTHDAY","USERS_ONLINE"),
		"SHOW_FORUMS" => "Y",
		"SHOW_FIRST_POST" => "N",
		"SHOW_AUTHOR_COLUMN" => "N",
		"TMPLT_SHOW_ADDITIONAL_MARKER" => "",
		"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
		"PAGE_NAVIGATION_TEMPLATE" => "forum",
		"PAGE_NAVIGATION_WINDOW" => "5",
		"AJAX_POST" => "N",
		"WORD_WRAP_CUT" => "23",
		"WORD_LENGTH" => "50",
		"USE_LIGHT_VIEW" => "Y",
		"SEF_MODE" => "Y",
		"CHECK_CORRECT_TEMPLATES" => "Y",
		"FID" => array(),
		"USER_PROPERTY" => array(),
		"USER_FIELDS" => array(),
		"FORUMS_PER_PAGE" => "10",
		"TOPICS_PER_PAGE" => "10",
		"MESSAGES_PER_PAGE" => "10",
		"TIME_INTERVAL_FOR_USER_STAT" => "10",
		"DATE_FORMAT" => "d.m.Y",
		"DATE_TIME_FORMAT" => "d.m.Y H:i:s",
		"USE_NAME_TEMPLATE" => "N",
		"IMAGE_SIZE" => "500",
		"ATTACH_MODE" => array(),
		"ATTACH_SIZE" => "90",
		"EDITOR_CODE_DEFAULT" => "N",
		"SEND_MAIL" => "E",
		"SEND_ICQ" => "A",
		"SET_NAVIGATION" => "Y",
		"SET_TITLE" => "Y",
		"SET_DESCRIPTION" => "Y",
		"SET_PAGE_PROPERTY" => "Y",
		"USE_RSS" => "Y",
		"SHOW_FORUM_ANOTHER_SITE" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"CACHE_NOTES" => "",
		"CACHE_TIME_USER_STAT" => "60",
		"CACHE_TIME_FOR_FORUM_STAT" => "3600",
		"RSS_TYPE_RANGE" => array("RSS1","RSS2","ATOM"),
		"RSS_CACHE" => "1800",
		"RSS_COUNT" => "30",
		"SHOW_VOTE" => "N",
		"RATING_ID" => array(),
		"SEF_FOLDER" => "/forum/",
		"SEF_URL_TEMPLATES" => Array(
			"index" => "index.php",
			"list" => "forum#FID#/",
			"read" => "forum#FID#/topic#TID#/",
			"message" => "messages/forum#FID#/topic#TID#/message#MID#/",
			"help" => "help/",
			"rules" => "rules/",
			"message_appr" => "messages/approve/forum#FID#/topic#TID#/",
			"message_move" => "messages/move/forum#FID#/topic#TID#/message#MID#/",
			"rss" => "rss/#TYPE#/#MODE#/#IID#/",
			"search" => "search/",
			"subscr_list" => "subscribe/",
			"active" => "topic/new/",
			"topic_move" => "topic/move/forum#FID#/topic#TID#/",
			"topic_new" => "topic/add/forum#FID#/",
			"topic_search" => "topic/search/",
			"user_list" => "users/",
			"profile" => "user/#UID#/edit/",
			"profile_view" => "user/#UID#/",
			"user_post" => "user/#UID#/post/#mode#/",
			"message_send" => "user/#UID#/send/#TYPE#/",
			"pm_list" => "pm/folder#FID#/",
			"pm_edit" => "pm/folder#FID#/message#MID#/user#UID#/#mode#/",
			"pm_read" => "pm/folder#FID#/message#MID#/",
			"pm_search" => "pm/search/",
			"pm_folder" => "pm/folders/"
		),
		"VARIABLE_ALIASES" => Array(
			"index" => Array(),
			"list" => Array(),
			"read" => Array(),
			"message" => Array(),
			"help" => Array(),
			"rules" => Array(),
			"message_appr" => Array(),
			"message_move" => Array(),
			"rss" => Array(),
			"search" => Array(),
			"subscr_list" => Array(),
			"active" => Array(),
			"topic_move" => Array(),
			"topic_new" => Array(),
			"topic_search" => Array(),
			"user_list" => Array(),
			"profile" => Array(),
			"profile_view" => Array(),
			"user_post" => Array(),
			"message_send" => Array(),
			"pm_list" => Array(),
			"pm_edit" => Array(),
			"pm_read" => Array(),
			"pm_search" => Array(),
			"pm_folder" => Array(),
		)
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>