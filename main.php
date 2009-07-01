<?php
/*  Copyright 2009  R-Link Research and Consulting, Inc.  (email : zach@rlinkconsulting.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*
Plugin Name: LinkIt
Plugin URI: http://www.codezach.com
Description: Auto-insert links into posts via associative text.
Version: 0.1
Author: R-Link Research and Consulting, Inc.
Author URI: http://www.rlinkconsulting.com
*/

//**************************************************************
// Globals
//**************************************************************
global $linkit_pluginroot, $linkit_curLink;

$linkit_rootdir = dirname(dirname(dirname(dirname(__FILE__))));
$linkit_pluginroot = dirname(__FILE__);
$linkit_webdir = '/wp-content/plugins/' . basename($linkit_pluginroot);

//**************************************************************
// Options
//**************************************************************

//--------------------------------------------------------------
// Get link array
//--------------------------------------------------------------
function rlink_linkit_links_array()
{
	global $wpdb;
	
	$sql = 'select * from rlink_linkit_links;';
	
	$ret = $wpdb->get_results($sql);
	
	return $ret;
}

//**************************************************************
// Includes
//**************************************************************
$include = $linkit_pluginroot . '/options.php';
require_once $include;

//**************************************************************
// Initialize
//**************************************************************

register_activation_hook(__FILE__,'rlink_linkit_install');
register_deactivation_hook(__FILE__,'rlink_linkit_uninstall');


add_action('wp_head', 'rlink_linkit_do_head');
add_filter('the_content', 'rlink_linkit_do_content');

//**************************************************************
// Execute
//**************************************************************

//--------------------------------------------------------------
// Install
//--------------------------------------------------------------
function rlink_linkit_install()
{	
	global $wpdb;
	
	$tableName = "rlink_linkit_links";
	
	if($wpdb->get_var("show tables like '$tableName'") != $tableName) 
	{ 
		$sql = 
			"CREATE TABLE `rlink_linkit_links` (
				`id` int(11) NOT NULL auto_increment,
	  			`displayName` varchar(256) default NULL,
	  			`regex` varchar(256) default NULL,
				`target` varchar(32) default NULL,
				`link` varchar(1024) default NULL,
				`linkTemplate` varchar(512) default NULL,
				PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	      dbDelta($sql);
	      
	      add_option("rlink_linkit_db_version", "1.0");
	}
}

//--------------------------------------------------------------
// Uninstall
//--------------------------------------------------------------
function rlink_linkit_uninstall()
{	
}

//--------------------------------------------------------------
// Header
//--------------------------------------------------------------
function rlink_linkit_do_head()
{
}


//--------------------------------------------------------------
// Content
//--------------------------------------------------------------
function rlink_linkit_do_content($content)
{
	global $linkit_curLink, $linkit_pluginroot, $linkit_webdir;
	
	$links = rlink_linkit_links_array();
	
	$ret = $content;
	
	foreach($links as $link)
	{
		$linkit_curLink = $link;
		
		$ret = preg_replace_callback($link->regex, "rlink_linkit_cb", $ret);
	}
	
	return $ret;
}

//--------------------------------------------------------------
// Content Callback
//--------------------------------------------------------------
function rlink_linkit_cb($matches)
{
	global $linkit_rootdir, $linkit_curLink;
	
	$link = $linkit_curLink;
	
	$ret = str_replace('%target%', $link->target, $link->linkTemplate);
	
	$ret = str_replace('%link%', $link->link, $ret);
	
	$ret = str_replace('%displayName%', $link->displayName, $ret);
	
	return $ret;
}


?>