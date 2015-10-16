<?php

namespace AU\SuggestedFriends;

$people = $vars['people'];

if (is_array($people) && sizeof($people) > 0) {

	foreach ($people as $person) {

		$person_link = elgg_view('output/url', array(
			'text' => $person['entity']->name,
			'href' => $person['entity']->getURL()
		));


		$mutuals = '';
		if ($person['mutuals']) {
			$text = ($person['mutuals'] > 1) ? elgg_echo('suggested_friends:num:mutual', array($person['mutuals'])) : elgg_echo('suggested_friends:num:mutual:1');
			$mutuals = elgg_view('output/url', array(
				'text' => $text,
				'href' => 'ajax/view/suggested_friends/mutual?guid=' . $person['entity']->guid,
				'class' => 'elgg-lightbox'
			));
		}

		$groups = '';
		if ($person['groups']) {
			$groups = elgg_view('output/url', array(
				'text' => elgg_echo('suggested_friends:num:groups', array($person['groups'])),
				'href' => 'ajax/view/suggested_friends/groups?guid=' . $person['entity']->guid,
				'class' => 'elgg-lightbox'
			));
		}

		$icon = elgg_view_entity_icon($person['entity'], 'small');
		
		$info = $person_link;
		if ($mutuals || $groups) {
			$value = $mutuals;
			if ($groups) {
				if ($value) {
					$value .= ', ';
				}
				$value .= $groups;
			}
			$info .= '<div class="elgg-subtext">' . $value . '</div>';
		}

		echo elgg_view('page/components/image_block', array(
			'image' => $icon,
			'body' => $info
		));
	}
} else {
	echo elgg_echo('suggested_friends:people:not:found');
}
