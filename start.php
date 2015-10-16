<?php

namespace AU\SuggestedFriends;

const PLUGIN_ID = 'suggested_friends';

require_once __DIR__ . '/lib/functions.php';
require_once __DIR__ . '/lib/events.php';

elgg_register_event_handler('init', 'system', __NAMESPACE__ . '\\init');

/**
 * plugin init
 */
function init() {
	elgg_extend_view('css/elgg', 'css/suggested_friends');
	
	elgg_register_page_handler('suggested_friends', __NAMESPACE__ . '\\suggested_friends_page_handler');

	elgg_register_widget_type('suggested_friends', elgg_echo('suggested_friends:people:you:may:know'), elgg_echo('suggested_friends:widget:description'), array('dashboard', 'profile'));

	elgg_register_event_handler('pagesetup', 'system', __NAMESPACE__ . '\\pagesetup');
	
	
	elgg_load_js('lightbox');
	elgg_load_css('lightbox');
	
	elgg_register_ajax_view('suggested_friends/mutual');
	elgg_register_ajax_view('suggested_friends/groups');
}

/**
 * server our pages through resource views
 * 
 * @param type $page
 * @return boolean
 */
function suggested_friends_page_handler($page) {
	
	elgg_gatekeeper();

	$friends = $groups = 0;
	switch ($page[0]) {
		case 'friends':
			$friends = 10;
			break;
		case 'groups':
			$groups = 10;
			break;
		default:
			$friends = $groups = 10;
			break;
	}
	
	$page_owner = elgg_get_logged_in_user_entity();
	elgg_set_page_owner_guid($page_owner->guid);
	
	$content = elgg_view('resources/suggested_friends/list', array(
		'owner' => $page_owner,
		'friends' => $friends,
		'groups' => $groups
	));

	if ($content) {
		echo $content;
		return true;
	}

	return false;
}
