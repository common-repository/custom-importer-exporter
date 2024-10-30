<?php
/**
 * Import / Export page for each post type
 */
if (!defined('ABSPATH') || !current_user_can('import')) { exit; }// Exit if accessed directly 
if (isset($_FILES['import_file']) && is_uploaded_file($_FILES['import_file']['tmp_name']) && check_admin_referer('post-type', 'nonce')) {
	$json = file_get_contents($_FILES["import_file"]["tmp_name"]);
	$json_data = json_decode($json);
	$import_count = 0;
	$error = "";
	foreach ($json_data as $data) {
		// 投稿情報
		$post_id = wp_insert_post($data->post);
		foreach ($data->meta as $meta) {
			add_post_meta($post_id, $meta->meta_key, $meta->meta_value[0]);
		}
		$import_count ++;
	}
	
	if (!empty($error)) {
		echo '<div class="notice is-dismissible error"><p>'. $error .'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.__('Hide the notification', 'custom-importer-exporter').'</span></button></div>';
	}
	echo '<div class="notice is-dismissible updated"><p>' . $import_count .__(' items imported', 'custom-importer-exporter').'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.__('Hide the notification', 'custom-importer-exporter').'</span></button></div>';
}
?>
<div class="box_wrap">
	<div class="box">
		<div class="contents">
			<h2><?= __('Post Export', 'custom-importer-exporter') ?></h2>
			<p><?= __('Export post information for each post type', 'custom-importer-exporter') ?></p>
			<form method="get">
<?php
$post_types = get_post_types(array('show_ui' => true),'objects');
foreach ($post_types as $post_type_object) :
	if ($post_type_object->name == 'attachment') {
		continue;
	}
?>
				<input type="checkbox" name="export_posttype[]" value="<?= $post_type_object->name ?>"<p><?= $post_type_object->labels->name ?></p>
<?php
endforeach;
?>
				<input type="hidden" name="page" value="<?= CIE_SLUG_POST_TYPE ?>">
				<input type="hidden" name="download" value="true">
				<div class="button_wrap">
					<input type="submit" value="<?= __('Export', 'custom-importer-exporter') ?>" class="button button-primary">
				</div>
			</form>
		</div>
	</div>

	<div class="box">
		<div class="contents">
			<h2><?= __('Post Import', 'custom-importer-exporter') ?></h2>
			<p><?= __('Import posting information', 'custom-importer-exporter') ?></p>

			<form method="POST" enctype="multipart/form-data">
				<input type="file" name="import_file">
				<div class="button_wrap">
					<input type="submit" value="<?= __('Import', 'custom-importer-exporter') ?>" class="button button-primary">
				</div>
				<?php wp_nonce_field('post-type','nonce'); ?>
			</form>
		</div>
	</div>

</div>