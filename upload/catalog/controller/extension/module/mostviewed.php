<?php
class ControllerExtensionModuleMostviewed extends Controller {
	public function index() {
		$this->load->language('extension/module/mostviewed');

		$this->load->model('catalog/product');
		$this->load->model('catalog/mostviewed');
		$this->load->model('tool/image');

		$data['products'] = array();
		
	$results = $this->model_catalog_mostviewed->getMostViewed();
	if(!empty($results)){
			foreach ($results as $result) {
				$product_info = $this->model_catalog_product->getProduct($result['product_id']);
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_'.$this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_'.$this->config->get('config_theme') . '_image_thumb_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_'.$this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_'.$this->config->get('config_theme') . '_image_thumb_height'));
					}if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'rating'      => $rating,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
        }

		if ($data['products']) {
			return $this->load->view('extension/module/mostviewed', $data);
		}
	}
}
