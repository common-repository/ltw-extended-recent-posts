<?php
/*
Plugin Name: LTW Extended Recent Posts
Plugin URI: http://www.lessthanweb.com/wordpress-plugins/extended-recent-posts
Description: A widget that shows the excerpt and post date of the recent posts after the post title.
Version: 1.0
Author: LessThanWeb
Author URI: http://www.lessthanweb.com
Text Domain: ltw_erp_td
License: GPL2
*/
/*  Copyright 2012  LessThanWeb  (email : contact@lessthanweb.com)

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
*/

class lessthanweb_extended_recent_posts extends WP_Widget
{
	const LTWERP = 'ltw_erp_td';

	private $limit_number_of_words = 55;
	private $ellipsis = '...';

	/**
	 * Constructor
	 *
	 * @since	1.0
	 * @param	void
	 * @return	void
	 *
	 */
	public function __construct()
	{
		parent::__construct(
			'ltw_erp',
			__('LTW Extended Recent Posts', self::LTWERP),
			array('description' => __('Shows excerpt of the content and date after the post title.', self::LTWERP))
		);
	}

	/**
	 * Output shown in the sidebars on the front end of the blog.
	 *
	 * @since	1.0
	 * @param	array	$args
	 * @param	array	$instance
	 * @return	void
	 *
	 */
	public function widget($args, $instance)
	{
		global $post;

		//	Change args to variables
		extract($args);

		//	Set title if there is none
		$title = apply_filters('widget_title', $instance['title']);

		//	Set word limiter
		$this->limit_number_of_words = $instance['limit_number_of_words'];

		//	Call the filter for excerpt length
		add_filter('excerpt_length', array($this, 'custom_excerpt_length'), 999);

		//	Set custom ellipsis
		$this->ellipsis = $instance['ellipsis'];

		//	Call the filter for custom ellipsis
		add_filter('excerpt_more', array($this, 'custom_ellipsis'));

		//	Output the before widget html code
		echo $before_widget;

		//	If there is title, output it else skip it!
		if (strlen($title) > 0)
		{
			echo $before_title.$title.$after_title;
		}

		$tmp_post = $post;

		//	Output the recent posts
		$posts = get_posts(array('numberposts' => $instance['number_of_posts']));
?>
		<ul>
<?php
		//	Loop over the posts
		foreach ($posts as $post)
		{
			setup_postdata($post);
?>
			<li>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				<?php the_excerpt(); ?>
<?php
			if ($instance['show_date'] != FALSE)
			{
?>
				<span>Posted on <time datetime="<?php esc_attr_e(get_the_date('c')); ?>" pubdate><?php esc_html_e(get_the_date().' '.get_the_time()); ?></time></span>
<?php
			}
?>
			</li>
<?php
		}

?>
		</ul>
<?php

		$post = $tmp_post;

		//	Output the after the widget html code
		echo $after_widget;
	}

	/**
	 * Save widget options.
	 *
	 * @since	1.0
	 * @param	array	$new_instance
	 * @param	array	$old_instance
	 * @return	array
	 *
	 */
	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number_of_posts'] = strlen($new_instance['number_of_posts']) > 0 ? intval($new_instance['number_of_posts']) : 5;
		$instance['limit_number_of_words'] = strlen($new_instance['limit_number_of_words']) > 0 ? intval($new_instance['limit_number_of_words']) : 55;
		$instance['ellipsis'] = $new_instance['ellipsis'];
		$instance['show_date'] = $new_instance['show_date'];

		return $instance;
	}

	/**
	 * Output admin widget options form.
	 *
	 * @since	1.0
	 * @param	array	$instance
	 * @return	void
	 *
	 */
	public function form($instance = array())
	{
		//	Set for field default values
		$instance['title'] = isset($instance['title']) == TRUE ? $instance['title'] : __('Recent Posts', self::LTWERP);
		$instance['number_of_posts'] = isset($instance['number_of_posts']) == TRUE ? $instance['number_of_posts'] : 5;
		$instance['limit_number_of_words'] = isset($instance['limit_number_of_words']) == TRUE ? $instance['limit_number_of_words'] : 55;
		$instance['ellipsis'] = isset($instance['ellipsis']) == TRUE ? $instance['ellipsis'] : '...';
		$instance['show_date'] = isset($instance['show_date']) == TRUE ? $instance['show_date'] : FALSE;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', self::LTWERP); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php esc_attr_e($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number_of_posts'); ?>"><?php _e('Number of posts to show:', self::LTWERP); ?></label>
			<input size="3" id="<?php echo $this->get_field_id('number_of_posts'); ?>" name="<?php echo $this->get_field_name('number_of_posts'); ?>" type="text" value="<?php esc_attr_e($instance['number_of_posts']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit_number_of_words'); ?>"><?php _e('Limit number of words:', self::LTWERP); ?></label>
			<input size="3" id="<?php echo $this->get_field_id('limit_number_of_words'); ?>" name="<?php echo $this->get_field_name('limit_number_of_words'); ?>" type="text" value="<?php esc_attr_e($instance['limit_number_of_words']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('ellipsis'); ?>"><?php _e('Ellipsis:', self::LTWERP); ?></label>
			<input size="3" id="<?php echo $this->get_field_id('ellipsis'); ?>" name="<?php echo $this->get_field_name('ellipsis'); ?>" type="text" value="<?php esc_attr_e($instance['ellipsis']); ?>" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" <?php checked((bool)$instance['show_date'], true); ?>>
			<label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Show date', self::LTWERP); ?></label>
		</p>
<?php
	}

	/**
	 * Sets the custom excerpt length.
	 *
	 * @since	1.0
	 * @param	void
	 * @return	integer
	 *
	 */
	public function custom_excerpt_length()
	{
		return $this->limit_number_of_words;
	}

	/**
	 * Sets the custom ellipsis.
	 *
	 * @since	1.0
	 * @param	void
	 * @return	integer
	 *
	 */
	public function custom_ellipsis()
	{
		return $this->ellipsis;
	}
}

/**
 * Register the damn widget. ;)
 *
 * @since	1.0
 * @param	void
 * @return	void
 *
 */
function lessthanweb_extended_recent_posts_init()
{
	register_widget('lessthanweb_extended_recent_posts');
}

add_action('widgets_init', 'lessthanweb_extended_recent_posts_init');
?>