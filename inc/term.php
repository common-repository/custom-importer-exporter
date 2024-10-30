<?php
/**
 * Term import / export page
 */
if (!defined('ABSPATH') || !current_user_can('import')) { exit; }// Exit if accessed directly
if (isset($_FILES['import_file']) && is_uploaded_file($_FILES['import_file']['tmp_name']) && check_admin_referer('post-term', 'nonce')) {
	$json = file_get_contents($_FILES["import_file"]["tmp_name"]);
	$json_data = json_decode($json);
	$import_count = 0;
	$error = "";
	foreach ($json_data as $data) {
		if (!taxonomy_exists($data->taxonomy)) {
			$error .= "<div>".sprintf(__('Taxonomy %s is not exists', 'custom-importer-exporter'), $data->taxonomy) . "</div>";
			continue;
		}
		$term = term_exists($data->slug, $data->taxonomy);
		if (!$term) {
			wp_insert_term(
					$data->name, $data->taxonomy, 
					[
						'description' => $data->description,
						'slug' => $data->slug,
						'parent' => $data->parent
					]
			);
		} else {
			wp_update_term(
					$term['term_id'], $data->taxonomy, 
					[
						'description' => $data->description,
						'slug' => $data->slug,
						'parent' => $data->parent
					]
			);
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
			<h2><?= __('Term Export', 'custom-importer-exporter') ?></h2>
			<p><?= __('Export term information', 'custom-importer-exporter') ?></p>
<?php
$args = [
	'public' => true,
	'_builtin' => false
];
$output = 'objects';
$operator = 'and';
$taxonomies = get_taxonomies($args, $output, $operator);
?>
			<form method="get">
				<input type="checkbox" name="export_taxonomy[]" value="category"<p><?= __('Category', 'custom-importer-exporter') ?></p>
				<input type="checkbox" name="export_taxonomy[]" value="post_tag"<p><?= __('Tag', 'custom-importer-exporter') ?></p>
<?php
foreach ($taxonomies as $taxonomy) :
?>
				<input type="checkbox" name="export_taxonomy[]" value="<?= $taxonomy->name ?>"<p><?= $taxonomy->name ?></p>
<?php
endforeach;
?>
				<input type="hidden" name="page" value="<?= CIE_SLUG_TERM ?>">
				<input type="hidden" name="download" value="true">
				<div class="button_wrap">
					<input type="submit" value="<?= __('Export', 'custom-importer-exporter') ?>" class="button button-primary">
				</div>
			</form>
		</div>
	</div>

	<div class="box">
		<div class="contents">
			<h2><?= __('Term Import', 'custom-importer-exporter') ?></h2>
			<p><?= __('Import the term information', 'custom-importer-exporter') ?></p>

			<form method="POST" enctype="multipart/form-data">
				<input type="file" name="import_file">
				<div class="button_wrap">
					<input type="submit" value="<?= __('Import', 'custom-importer-exporter') ?>" class="button button-primary">
				</div>
				<?php wp_nonce_field('post-term','nonce'); ?>
			</form>
		</div>
	</div>

</div>