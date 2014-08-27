<?php

function template_misery()
{
	global $context, $txt;

	echo '
	<h3 class="category_header hdicon cat_img_eye">', $txt['mc_misery'], '</h3>
	<div class="windowbg">
		<div class="content modbox">
			<ul>';

	foreach ($context['misery_users'] as $user)
	{
		echo '
				<li class="smalltext">',
					sprintf(!empty($user['last_login']) ? $txt['mc_seen'] : $txt['mc_seen_never'], $user['link'], $user['last_login']), '
				</li>';
	}

	// Don't have any miserable users right now?
	if (empty($context['misery_users']))
		echo '
				<li>
					<strong class="smalltext">', $txt['mc_misery_none'], '</strong>
				</li>';

	echo '
			</ul>
		</div>
	</div>';
}
