<?php
class ModelCatalogMostviewed extends Model {
	public function getMostViewed() {
	$limit=$this->config->get('module_mostviewed_number');
		$query=$this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE status = '1' ORDER BY viewed DESC LIMIT $limit ");
		
		return $query->rows;
	}
}
