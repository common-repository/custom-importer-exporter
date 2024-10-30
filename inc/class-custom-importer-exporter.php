<?php

if (!defined('ABSPATH')) { exit; }// Exit if accessed directly 

if (!class_exists('CustomImporterExporter')) {
	
	class CustomImporterExporter {

		function __construct() {
			$parent_slug = CIE_SLUG_TERM;
			add_menu_page(CIE_PLUGIN_NAME, CIE_PLUGIN_NAME, 'manage_options', $parent_slug, function() {
				require_once dirname(__FILE__) . '/term.php';
			}, 'dashicons-welcome-widgets-menus');
			$menu_name_term = __('Term', 'custom-importer-exporter');
			$menu_name_post_type = __('Post', 'custom-importer-exporter');
			$hook_term = add_submenu_page($parent_slug, $menu_name_term, $menu_name_term, 'manage_options', CIE_SLUG_TERM, function() {
				require_once dirname(__FILE__) . '/term.php';
			});
			add_action("load-$hook_term", array($this, 'exportTerms'));
			add_action("load-$hook_term", array($this, 'adminStyle'));
			add_action("load-$hook_term", array($this, 'adminScript'));

			$hook_post = add_submenu_page($parent_slug, $menu_name_post_type, $menu_name_post_type, 'manage_options', CIE_SLUG_POST_TYPE, function() {
				require_once dirname(__FILE__) . '/post-type.php';
			});
			add_action("load-$hook_post", array($this, 'exportPostType'));
			add_action("load-$hook_post", array($this, 'adminStyle'));
			add_action("load-$hook_post", array($this, 'adminScript'));
		}

		/**
		 * Export term information
		 */
		function exportTerms() {
			if (isset($_GET['download']) && isset($_GET['export_taxonomy'])) {
				$taxonomy_list = $_GET['export_taxonomy'];
				$json_data = [];
				foreach ($taxonomy_list as $taxonomy) {
					$args = [
						'hide_empty' => false
					];
					$terms = get_terms($taxonomy, $args);
					foreach ($terms as $term) {

						// Create data for output
						$export_data = [];
						$export_data['name'] = $term->name;
						$export_data['slug'] = $term->slug;
						$export_data['taxonomy'] = $term->taxonomy;
						$export_data['parent'] = $term->parent;
						$export_data['description'] = $term->description;

						$json_data[] = $export_data;
					}
				}
				$filename = 'terms_' . date('Ymd-His') . '.json';
				header('Content-Description: File Transfer');
				header('Content-Disposition: attachment; filename=' . $filename);
				header('Content-Type: application/json; charset=utf-8', true);

				$json = json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				echo $json;
				die();
			}
		}

		/*
		 * Export processing for each post type
		 */
		function exportPostType() {
			if (isset($_GET['download']) && isset($_GET['export_posttype'])) {
				$post_type_list = $_GET['export_posttype'];
				$json_data = [];
				foreach ($post_type_list as $post_type) {
					$post_data = [];
					$args = [
						'numberposts' => 0,
						'post_type' => $post_type
					];
					$posts = get_posts($args);
					foreach ($posts as $post) {
						// meta info
						$post_id = $post->ID;
						$post_meta = get_post_meta($post_id);
						$export_post_meta = [];
						foreach ($post_meta as $key => $value) {
							$data = [];
							$data['meta_key'] = $key;
							$data['meta_value'] = $value;
							$export_post_meta[] = $data;
						}

						// Post information for export
						$export_post_data = [];
						$export_post_data['post_author'] = $post->post_author;
						$export_post_data['post_date'] = $post->post_date;
						$export_post_data['post_date_gmt'] = $post->post_date_gmt;
						$export_post_data['post_content'] = $post->post_content;
						$export_post_data['post_title'] = $post->post_title;
						$export_post_data['post_excerpt'] = $post->post_excerpt;
						$export_post_data['post_status'] = $post->post_status;
						$export_post_data['comment_status'] = $post->comment_status;
						$export_post_data['post_password'] = $post->post_password;
						$export_post_data['post_name'] = $post->post_name;
						$export_post_data['to_ping'] = $post->to_ping;
						$export_post_data['pinged'] = $post->pinged;
						$export_post_data['post_parent'] = $post->post_parent;
						$export_post_data['menu_order'] = $post->menu_order;
						$export_post_data['post_type'] = $post->post_type;
						$export_post_data['post_mime_type'] = $post->post_mime_type;
						$export_post_data['comment_count'] = $post->comment_count;
						$export_post_data['filter'] = $post->filter;


						$post_data['meta'] = $export_post_meta;
						$post_data['post'] = $export_post_data;

						$json_data[] = $post_data;
					}
				}

				$filename = 'post_' . date('Ymd-His') . '.json';
				header('Content-Description: File Transfer');
				header('Content-Disposition: attachment; filename=' . $filename);
				header('Content-Type: application/json; charset=utf-8', true);

				$json = json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				echo $json;
				die();
			}
		}
		
		/**
		 * Add style sheet
		 */
		function adminStyle() {
			wp_enqueue_style('custom-importer-exporter', CIE_PLUGIN_URL . '/css/custom-importer-exporter.css');
		}

		/**
		 * Add Script
		 */
		function adminScript() {
			
		}
	}
}
