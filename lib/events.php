<?php

namespace AU\SuggestedFriends;

/**
 * page setup event
 */
function pagesetup() {

	// add to site links
	if (elgg_is_logged_in()) {
		$item = new \ElggMenuItem('suggested_friends', elgg_echo('suggested_friends:new:people'), 'suggested_friends');
		elgg_register_menu_item('site', $item);
	}

	elgg_register_menu_item('page', array(
		'name' => 'suggested_friends_all',
		'text' => elgg_echo('suggested_friends:all'),
		'href' => 'suggested_friends',
		'contexts' => array(
			'suggested_friends'
		)
	));
	
	elgg_register_menu_item('page', array(
		'name' => 'suggested_friends_friends',
		'text' => elgg_echo('suggested_friends:friends:only'),
		'href' => 'suggested_friends/friends',
		'contexts' => array(
			'suggested_friends'
		)
	));
	
	elgg_register_menu_item('page', array(
		'name' => 'suggested_friends_groups',
		'text' => elgg_echo('suggested_friends:groups:only'),
		'href' => 'suggested_friends/groups',
		'contexts' => array(
			'suggested_friends'
		)
	));
}
