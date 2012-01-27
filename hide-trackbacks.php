<?php
/*
Plugin Name: Hide Trackbacks
Plugin URI: http://wp.me/p1vXha-4u
Description: Stops trackbacks and pingbacks from showing up as comments on your posts.
Version: 1.0.2
Author: Sander van Dragt
Author URI: http://amasan.co.uk/blog
License: GPL2

	  Copyright 2011  Sander van Dragt  (email : sander.vandragt@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

	Derived from original code by:
  Honey Singh 
  http://www.honeytechblog.com/how-to-remove-tracbacks-and-pings-from-wordpress-posts/

*/

class SVD_HideTrackbacks {

	// Initialisation
	function SVD_HideTrackbacks() {
		add_filter( 'the_posts',           array( &$this, 'filter_post_comments' ) );
		add_filter( 'comments_array',      array( &$this, 'filter_trackbacks' ) );
		add_filter( 'get_comments_number', array( &$this, 'filterCommentCount' ) );
	}


	// Updates the comment number for posts with trackbacks
	function filter_post_comments( $posts ) {
		foreach ( $posts as $key => $p ) {
			if ( $p->comment_count <= 0 ) { 
				return $posts; 
			}
			$posts[$key]->comment_count = $this->_count_comments( (int) $p->ID );
		}
		return $posts;
	}


	// Updates the count for comments and trackbacks
	function filter_trackbacks( $comms ) {
		global $comments;
		$comments = $this->_strip_trackbacks( $comms );
		return $comments;
	}


	// Strips out trackbacks/pingbacks
	function strip_trackback( $var ) {
		if ( $var->comment_type == 'trackback' || $var->comment_type == 'pingback' ) { 
			return false; 
		}
		return true;
	}


	// Return the correct comment count within the loop
	function filterCommentCount( $commentcount ) {
		$id = get_the_ID();
		return $this->_count_comments( $id );
	}

	// Helper  for  counting comments per post
	private function _count_comments( $id ) {
		$comments = get_approved_comments( $id );
		$comments = $this->_strip_trackbacks( $comments );
		return sizeof( $comments );
	}

	// helper for filtering out the trackbacks / pingbacks leaving comments only from list of comments
	private function _strip_trackbacks( $comms ) {
		if ( !is_array( $comms ) ) { 
			return;
		}
		return array_filter( $comms,  array( &$this, 'strip_trackback' ) );
	}
}

// Load the plugin
add_action( 'plugins_loaded', create_function( '', 'global $svd_hide_trackbacks; $svd_hide_trackbacks = new SVD_HideTrackbacks();' ) );