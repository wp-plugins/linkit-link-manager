<?php

$linkit_modes = array(
	'save' => 'Save', 
	'add' => 'Add New Link', 
	'edit' => 'Edit', 
	'delete' => 'Delete',
	'discard' => 'Discard');

add_action('admin_menu', 'rlink_linkit_menu');

function rlink_linkit_home_link($title)
{
	if(isset($title) != true)
	{
		$title = 'Continue &gt;&gt;';
	}
	
	$url = $_SERVER['PHP_SELF'] . '?page=' . $_REQUEST['page'];
	
	echo '<a href="' . $url . '">' . $title . '</a>';
}

function normalizeREQUEST()
{
	foreach($_REQUEST as $key => $val)
	{
		$_REQUEST[$key] = stripslashes($val);
	}
}

function rlink_linkit_div_msg($msg)
{
	echo '<div style="padding:5px;border:1px solid black;margin:50px;background-color:#FFFF88;width:80%;text-align:center;font-size:16pt;font-weight:bold;">';
	echo $msg;
	echo '</div>';
	
	echo '<div style="width:80%;margin:0 auto;text-align:left;"><h3>';
	rlink_linkit_home_link();
	echo '</h3></div>';
}

function rlink_linkit_menu() 
{
  add_options_page('LinkIt Options', 'LinkIt Options', 8, __FILE__, 'rlink_linkit_options');
}

function rlink_linkit_save()
{
	global $linkit_modes, $wpdb;
	
	$id = $_REQUEST['id'];
	$regex = $_REQUEST['regex'];
	$displayName = $_REQUEST['displayName'];
	$target = $_REQUEST['target'];
	$link = $_REQUEST['link'];
	$template = $_REQUEST['template'];

	if(isset($id) && strlen($id) > 0)
	{
		$sql = 
			'update rlink_linkit_links ' .
			"set regex=%s, displayName=%s, target=%s, link=%s, linkTemplate=%s where id=%s;";
		
		$sql = $wpdb->prepare($sql, $regex, $displayName, $target, $link, $template, $id);
	}
	else
	{
		$sql = 
			'insert into rlink_linkit_links (regex,displayName,target,link,linkTemplate) ' .
			"values(%s, %s, %s, %s, %s);";
		
		$sql = $wpdb->prepare($sql, $regex, $displayName, $target, $link, $template);
	}
	
	$err = $wpdb->query($sql);
	
	rlink_linkit_div_msg('Saved!');
}

function rlink_linkit_edit($new)
{
	global $linkit_modes, $wpdb;
	
	if($new == false)
	{
		$id = $_REQUEST['id'];
	}
	
	$link->regex = '/\[My Link\]/';
	$link->displayName = 'My Link';
	$link->target = '_blank';
	$link->link = 'http://www.google.com';
	$link->linkTemplate = '<a target="%target%" href="%link%">%displayName%</a>';
	
	if($new == false)
	{
		$sql = "select * from rlink_linkit_links where id='" . $id . "';";
		
		$wpdb->escape($sql);
		
		$link = $wpdb->get_row($sql);
	}
	
	htmlentities($link->linkTemplate);
	
	if($new == true)
	{
?>
<h1>Add New Link</h1>
<hr />
<?php 
	}
	else
	{
?>
<h1>Edit Link</h1>
<hr />
<?php 
	}
?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table>
<tr>
<td>Regular Expression</td>
<td><input name="regex" type="text" value="<?php echo $link->regex; ?>"/></td>
</tr>

<tr>
<td>Link Display Name</td>
<td><input name="displayName" type="text" value="<?php echo $link->displayName; ?>"/></td>
</tr>

<tr>
<td>Target</td>
<td>
<select name="target" id="themeSelect">
<?php 
$options = array('_self', '_blank', '_parent', '_new');

foreach($options as $opt)
{
	$sel = '';
	
	if($opt == $link->target)
	{
		$sel = 'selected';
	}
	
	echo '<option ' . $sel . '>' . $opt . '</option>';
}
?>
</select>
</td>

<tr>
<td>Link</td>
<td><input name="link" type="text" style="width:100%" value="<?php echo $link->link; ?>"/></td>
</tr>

<tr>
<td>Link Template</td>
<td><textarea name="template" rows="5" cols="80"><?php echo $link->linkTemplate; ?></textarea></td>
</tr>

<tr>
<td>
</td>
<td>
<table>
<tr><td><input name="mode" type="submit" value="<?php echo $linkit_modes['save']; ?>"/></td>
<td>&nbsp;&nbsp;<?php rlink_linkit_home_link('Discard Changes'); ?></td></tr>
</table>
</td>
</tr>

</table>

</form>
<?php 
}

function rlink_linkit_delete()
{
	global $wpdb;
	
	$id = $_REQUEST['id'];
	
	$sql = 'delete from rlink_linkit_links where id=%d';
	
	$sql = $wpdb->prepare($sql, $id);
	
	$msg = 'Deleted Link (id=' . $id . ')';
	
	$success = $wpdb->query($sql);
	
	if($success === false)
	{
		$msg = 'Delete failed with a database error.';
	}
	
	rlink_linkit_div_msg($msg);
}

function rlink_linkit_list()
{
	global $linkit_modes, $wpdb;
	
	$links = $wpdb->get_results("select * from rlink_linkit_links;");
	
?>
<script type="text/javascript">

function confirmDelete()
{
	var ret = confirm("Delete this link?");

	return ret;
}
</script>
<?php 

	echo '<table>';
	echo '<tr><td colspan="9">';
	
?>
<h1>LinkIt Links</h1>
<hr />
<p style="width:60%;">
Click the "Add" button below to get started. Each regular expression will be used to insert links into
the content of posts and pages. The display name, link, target, and template come together to allow 
precise control over the final link display.
</p>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<input name="mode" type="submit" value="<?php echo $linkit_modes['add']; ?>" />
</form>
<?php 
	
	echo '</td></tr><tr><td>';
	
	if(count($links) > 0)
	{
		echo '<table cellspacing="10px" style="border:1px solid black;background-color:#EEEEEE;margin:0px;"><tr><th>Regex</th><th>Display Name</th><th>Link</th></tr>';
		
		foreach($links as $link)
		{
			echo 
				'<tr><td>' . 
				$link->regex . 
				'</td><td>' . 
				$link->displayName . 
				'</td><td>' .
				$link->link .
				'</td><td>' .
				'<a href="' . $_SERVER['REQUEST_URI'] . '&mode=' . $linkit_modes['edit'] . '&id=' . $link->id . '">Edit</a>' .
				'</td><td>' .
				'<a href="' . $_SERVER['REQUEST_URI'] . '&mode=' . $linkit_modes['delete'] . '&id=' . $link->id . '" onclick="return confirmDelete()">Delete</a>' .
				'</td></tr>';
		}
		
		echo '</table>';
	}
	
	echo '</td></tr></table>';
}

function rlink_linkit_options() 
{
	global $upcomingEvents_pluginroot, $wpdb, $linkit_modes;
	
	normalizeREQUEST();
	
	$mode = $_REQUEST['mode'];
	
	if($mode == $linkit_modes['edit'])
	{
		rlink_linkit_edit(false);
	}
	else if($mode == $linkit_modes['add'])
	{
		rlink_linkit_edit(true);
	}
	else if($mode == $linkit_modes['save'])
	{
		rlink_linkit_save();
	}
	else if($mode == $linkit_modes['delete'])
	{
		rlink_linkit_delete();
	}
	else 
	{
		rlink_linkit_list();
	}
}

?>