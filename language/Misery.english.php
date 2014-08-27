<?php

// Permissions
$txt['permissionname_misery'] = 'Add user to the miserable user list';
$txt['permissionhelp_misery'] = 'Allows members in this group to mark other users as problem users (adding them to the miserable-user list)';

// Misc
$txt['cannot_misery'] = 'You are not permitted to use this feature.';
$txt['cannot_misery_yourself'] = 'How much more misery can you take!';

// Profile
$txt['add_to_misery'] = 'Add to Misery List';

// Settings
$txt['misery'] = 'Misery';
$txt['misery_enabled'] = 'Enable Misery';
$txt['misery_desc'] = 'The options here apply to users added to the misery list as an encouragement to make them leave.  The % chance of misery actions should be sufficiently subtle enough to avoid suspicion.';

$txt['misery_EveryPage'] = 'Misery for every page load';
$txt['misery_EveryPage_desc'] = 'These settings apply on every page load.  A user may be hit with any of these on any page.';
$txt['misery_randomdelay'] = 'Chance the user will experence a random-length delay, giving the appearance of a slow connection.';
$txt['misery_randomdelay_help'] = 'Use this with caution if a user was truly malicious, they could take advantage to rapidly request web resources and exhaust all available threads temporarily taking your site offline. See Dos attack http://en.wikipedia.org/wiki/Denial-of-service_attack';
$txt['misery_randomdelay_min'] = 'Minimum loading delay a user could experience';
$txt['misery_randomdelay_max'] = 'Maximum loading delay a user could experience';
$txt['misery_serverbusy'] = 'Chance the user will receive a "server is busy" message';
$txt['misery_dbbusy'] = 'Chance the user will receive a "Database Temporarily Unavailable" message';
$txt['misery_blankscreen'] = 'Chance the user will receiving a white screen';
$txt['misery_serverhttperror'] = 'Chance the user will receiving an http error like 401, 404, 507, etc';
$txt['misery_popup'] = 'Chance the user will receiving a Javascript popup';
$txt['misery_popup_message'] = 'Message to display to user in the popup<br />To add the user\'s name, use {membername}. A generic message is used if this is activated but left empty';
$txt['misery_popup_message_default'] = "{membername}, The site requires cookies to be enabled for the best experiance.\n\nPlease enable cookies in your browser.\n\nselect OK to continue";
$txt['misery_redirect_page'] = 'Chance the user will be redirected somewhere else<br />The forum front page is default.';
$txt['misery_redirect'] = 'URL to redirect users to, upon meeting the above rule';
$txt['misery_logout'] = 'Change the user will be automatically logged out';

$txt['misery_Forms'] = 'Form specific misery';
$txt['misery_Forms_desc'] = 'These settings apply to specific form actions, such as posting and PM\'s';
$txt['misery_posterror'] = 'Chance the user will get an error on posting';
$txt['misery_posterror_help'] = 'This will create various errors on posting, requiring the user to try again.  Examples are missing subject, missing message, message to long, session error, even the chance that the post text or post will be lost.';
$txt['misery_pmerror'] = 'Chance the user will get an error on sending a PM';
$txt['misery_pmerror_help'] = 'This will create various errors on sending, requiring the user to try again.  Examples are missing subject, missing message, message to long, session error, even the chance that the pm text or pm will be lost.';
$txt['misery_emailerror'] = 'Chance the user will get an error on sending a Email';

$txt['misery_FeatureSpecific'] = 'Feature specific misery';
$txt['misery_FeatureSpecific_desc'] = 'These settings apply if the user selects the individual action.';
$txt['misery_search'] = 'Chance the user will find "Search" disabled';
$txt['misery_unreadreplies'] = 'Chance the user will find "Show new replies to your posts" disabled';
$txt['misery_show_posts'] = 'Chance the user will find "Show this user\'s posts" disabled';
$txt['misery_unread'] = 'Chance the user will find "Show unread posts since last visit" disabled';
$txt['misery_recent'] = 'Chance the user will find "Show recent posts" disabled';

// Moderation center
$txt['mc_misery'] = 'Miserable Users';
$txt['mc_misery_none'] = 'No users are currently on the misery list';
$txt['mc_misery_desc'] = 'These users are marked as problematic, for whom access to the forum may be restricted as an encouragement to make them leave.';