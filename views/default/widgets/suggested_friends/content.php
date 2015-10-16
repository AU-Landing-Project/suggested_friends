<?php

namespace AU\SuggestedFriends;

$widget = $vars['entity'];

$friends = $widget->look_in_friends == 'no' ? 0 : 3;
$groups = $widget->look_in_groups == 'no' ? 0 : 3;
$num_display = $widget->num_display != null ? $widget->num_display : 3;

if (!elgg_is_logged_in()) {
	echo elgg_echo('suggested_friends:people:not:found');
	return;
}

$people = get_suggestions(elgg_get_logged_in_user_guid(), $friends, $groups);

// limit our number of people
while (count($people) > $num_display) {
	array_pop($people);
}

echo elgg_view('suggested_friends/people', array('people' => $people));
?>
<div class="clearfloat"></div>
<div class="widget_more_wrapper">
	<?php
	echo elgg_view('output/url', array(
		'text' => elgg_echo('suggested_friends:see:more'),
		'href' => 'suggested_friends'
	));
	?>
</div>
