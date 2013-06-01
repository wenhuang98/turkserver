<?php

if ( !defined('APPDIR') )
	die();

global $cookie, $list_number, $experiment_name;

// get the active list number:
// note that list_number() may have to set a cookie, so no output should occur before this point.
$list_number = list_number( $experiment_name );

if ( !file_exists( APPDIR . '/data/' . $experiment_name . '.html' ) ||
	!is_readable( APPDIR . '/data/' . $experiment_name . '.html' ) )
	die( "Error: template file could not be loaded." );
$template = file_get_contents( APPDIR . '/data/' . $experiment_name . '.html' );

$fields = read_data( $experiment_name, $list_number );

?><html>
<head>
<title><?php echo $experiment['title']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<meta name="generator" content="turkserver" />
<!-- Generated by turkserver <https://github.com/mitcho/turkserver> -->
<?php
// used by MTurk:
// <script type='text/javascript' src='https://s3.amazonaws.com/mturk-public/externalHIT_v1.js'></script>
// <script language="Javascript">turkSetAssignmentID();</script>
?>
</head>

<body>

<form name="mturk_form" method="post" id="mturk_form" action="<?php echo APPURL . $experiment_name; ?>">
<?php
// For some reason the assignmentId field is lowercase in Turk, but then gets stored
// into a column called AssignmentId in the results. I suppose we can do that too.
?>
<input type="hidden" value="<?php echo new_id();?>" name="assignmentId" id="assignmentId" />
<?php
// The worker ID is not included as a hidden field in Turk, but we use it here as a
// check to make sure that cookieing worked, between experiment load and submission.
?>
<input type="hidden" value="<?php echo $cookie['workerid'];?>" name="turkserver[workerid]" id="turkserver_workerid" />
<?php
// The list ID (similar to a Turk HIT ID) is similarly not included in Turk, but we use
// it here to make sure we know which list to save the results against.
?>
<input type="hidden" value="<?php echo $list_number;?>" name="turkserver[list_number]" id="turkserver_list_number" />

<?php
$template_fields = preg_replace( '!^.*$!', '\\${$0}', array_keys($fields) );
echo str_replace( $template_fields, array_values($fields), $template );
?>

<p><input type="submit" id="submitButton" value="Submit" /></p>
</form>
</body>
</html>