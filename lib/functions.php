<?php

namespace AU\SuggestedFriends;

/*
 * People usort callback
 */

function suggested_friends_sorter($a, $b) {
	if ($a['priority'] == $b['priority']) {
		return 0;
	}
	return ($a['priority'] < $b['priority']) ? 1 : -1;
}

/**
 *
 * Returns array of people containing entity, mutuals (friends), groups (shared) and priority
 * @param Int $guid
 * @param Int $friends_limit
 * @param Int $groups_limit
 * @return Array
 */
function get_suggestions($guid, $friends_of_friends_limit = 10, $groups_members_limit = 10) {

	$dbprefix = elgg_get_config('dbprefix');

	$guid = sanitize_int($guid);
	$suggestions = array();

	if ($friends_of_friends_limit) {
		// get some friends of friends
		$options = array(
			'selects' => array(
				'COUNT(fof.guid_two) as priority'
			),
			'type' => 'user',
			'joins' => array(
				"JOIN {$dbprefix}users_entity ue ON ue.guid = e.guid",
				"JOIN {$dbprefix}entity_relationships fr ON fr.guid_one = {$guid} AND fr.relationship = 'friend'",
				"JOIN {$dbprefix}entity_relationships fof ON fof.guid_one = fr.guid_two AND fof.relationship = 'friend'"
			),
			"wheres" => array(
				"ue.banned = 'no'",
				"e.guid NOT IN (SELECT f.guid_two FROM {$dbprefix}entity_relationships f WHERE f.guid_one = {$guid} AND f.relationship = 'friend')",
				"fof.guid_two = e.guid",
				"e.guid != {$guid}"
			),
			'group_by' => 'e.guid',
			'order_by' => 'priority desc, ue.last_action desc',
			'limit' => abs((int) $friends_of_friends_limit)
		);

		$fof = elgg_get_entities($options);

		foreach ($fof as $f) {
			$priority = (int) $f->getVolatileData('select:priority');
			$suggestions[$f->guid] = array(
				'entity' => $f,
				'mutuals' => $priority,
				'groups' => 0,
				'priority' => $priority
			);
		}
	}

	if ($groups_members_limit) {
		// get some mutual group members
		$options = array(
			'selects' => array(
				'COUNT(mog.guid_two) as priority'
			),
			'type' => 'user',
			'joins' => array(
				"JOIN {$dbprefix}users_entity ue ON ue.guid = e.guid",
				"JOIN {$dbprefix}entity_relationships g ON g.guid_one = {$guid} AND g.relationship = 'member'",
				"JOIN {$dbprefix}groups_entity ge ON ge.guid = g.guid_two", //ensure it's a group
				"JOIN {$dbprefix}entity_relationships mog ON mog.guid_two = g.guid_two AND mog.relationship = 'member'"
			),
			"wheres" => array(
				"ue.banned = 'no'",
				"e.guid NOT IN (SELECT f.guid_two FROM {$dbprefix}entity_relationships f WHERE f.guid_one = {$guid} AND f.relationship = 'friend')",
				"mog.guid_one = e.guid",
				"e.guid != {$guid}"
			),
			'group_by' => 'e.guid',
			'order_by' => 'priority desc, ue.last_action desc',
			'limit' => 3 //abs((int) $groups_members_limit)
		);

		// get members of groups
		$mog = elgg_get_entities($options);

		foreach ($mog as $m) {
			if (!isset($suggestions[$m->guid])) {
				$priority = (int) $m->getVolatileData('select:priority');
				$suggestions[$m->guid] = array(
					'entity' => $m,
					'mutuals' => 0,
					'groups' => $priority,
					'priority' => $priority
				);
			} else {
				$priority = (int) $m->getVolatileData('select:priority');
				$suggestions[$m->guid]['groups'] = $priority;
				$suggestions[$m->guid]['priority'] += $priority;
			}
		}
	}

	// sort by priority
	usort($suggestions, __NAMESPACE__ . '\\suggested_friends_sorter');

	return $suggestions;
}
