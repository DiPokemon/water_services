<?php
class WaterServicesModel {

public $id;
public $title;
public $img_title;
public $description;
public $alt;
public $image_attachment_id;

/**
 * Run
 */
public function __construct(){

}



public function get($id){
	global $wpdb;

	$query = "SELECT * FROM `" . WATER_SERVICES_DB_TABLE_NAME . "` WHERE id = '" . $id . "' LIMIT 1";
	$row = $wpdb->get_row($query, 'OBJECT');

	$this->id 		    = $id;
	$this->title     = is_null($row->title) ? '' : $row->title;
	$this->img_title = is_null($row->img_title) ? '' : $row->img_title;
	$this->description	    = is_null($row->description) ? '' : $row->description;
	$this->alt    = is_null($row->alt) ? '' : $row->alt;
	$this->image_attachment_id = is_null($row->image_attachment_id) ? '' : $row->image_attachment_id;

	return $this;
}

public function get_list(){
	global $wpdb;

	$query = "SELECT * FROM `" . WATER_SERVICES_DB_TABLE_NAME . "`";
	$list = $wpdb->get_results($query, 'OBJECT_K');

	if ( $list )
		return $list;
	else
		return [];
}

public function get_image_attachment_filepath($image_attachment_id){
	if (is_null($image_attachment_id) || empty($image_attachment_id) || ($image_attachment_id == 0))
		return plugins_url( WATER_SERVICES_PLUGIN_NAME . '/static/images/no-avatar.png' ) ;
	else
		return wp_get_attachment_url($image_attachment_id);
}



public function save(){
	global $wpdb;

	if (is_null($this->id))
		$this->add();
	else
		$this->edit();

	return $this;
}

public function delete($id){
	global $wpdb;

	return  $wpdb->delete(
		WATER_SERVICES_DB_TABLE_NAME,
				[
					'id' => $id
				]
			);
}

protected function add(){
	global $wpdb;

	$rows_affected = $wpdb->insert(
		WATER_SERVICES_DB_TABLE_NAME,
				[
					'title' 		=> $this->title,
					'img_title'  => $this->img_title,
					'description' 		=> $this->description,
					'alt' 		=> $this->alt,
					'image_attachment_id' => $this->image_attachment_id
				]
			);
	return $rows_affected;
}

protected function edit(){
	global $wpdb;

	return 	$wpdb->update(
		WATER_SERVICES_DB_TABLE_NAME,
				[
					'title' 		=> $this->title,
					'img_title'  => $this->img_title,
					'description' 		=> $this->description,
					'alt' 		=> $this->alt,
					'image_attachment_id' => $this->image_attachment_id
				],
				[
					'id' => $this->id
				]
			);
}

}
