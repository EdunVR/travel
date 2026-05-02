<?php
if (class_exists('IndeedImport') && !class_exists('UapIndeedImport')):

class UapIndeedImport extends IndeedImport{

	/*
	 * @param string ($entity_name)
	 * @param string ($entity_opt)
	 * @param object ($xml_object)
	 * @return none
	 */
	protected function do_import_custom_table($entity_name, $entity_opt, &$xml_object){
		global $wpdb;
		$table = $wpdb->prefix . $entity_name;

		if (!$xml_object->$entity_name->Count()){
			return;
		}

		switch ($entity_name){
			case 'uap_affiliates':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['id'] ) || !isset( $array['uid'] ) || !isset( $array['rank_id'] ) || !isset( $array['start_data'] ) || !isset( $array['status'] ) ){
						continue;
					}
					$insert_string = $wpdb->prepare("VALUES( %s, %s, %s, %s, %s )",
				 $this->sanitize( $array['id'] ),
				 $this->sanitize( $array['uid'] ),
				 $this->sanitize( $array['rank_id'] ),
				 $this->sanitize( $array['start_data'] ),
				 $this->sanitize( $array['status'] )
					);
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_banners':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['name'] ) || !isset( $array['description'] ) || !isset( $array['url'] ) || !isset( $array['image'] ) || !isset( $array['status'] ) || !isset( $array['DATE'] ) ){
						continue;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['name'] ) . "',
											'" . $this->sanitize( $array['description'] ) . "',
											'" . $this->sanitize( $array['url'] ). "',
											'" . $this->sanitize( $array['image'] ) . "',
											'" . $this->sanitize( $array['status'] ) . "',
											'" . $this->sanitize( $array['DATE'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_notifications':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['type'] ) || !isset( $array['rank_id'] ) || !isset( $array['subject'] )
					|| !isset( $array['message'] ) || !isset( $array['pushover_message'] ) || !isset( $array['pushover_status'] ) || !isset( $array['status'] ) ){
						continue;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['type'] ) . "',
											'" . $this->sanitize( $array['rank_id'] ) . "',
											'" . $this->sanitize( $array['subject'] ) . "',
											'" . $this->sanitize( $array['message'] ) . "',
											'" . $this->sanitize( $array['pushover_message'] ) . "',
											'" . $this->sanitize( $array['pushover_status'] ) . "',
											'" . $this->sanitize( $array['status'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_ranks':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['slug'] ) || !isset( $array['label'] ) || !isset( $array['amount_type'] )
					|| !isset( $array['amount_value'] ) || !isset( $array['bonus'] ) || !isset( $array['pay_per_click'] ) || !isset( $array['cpm_commission'] ) || !isset( $array['sign_up_amount_value'] ) || !isset( $array['lifetime_amount_type'] ) || !isset( $array['lifetime_amount_value'] ) || !isset( $array['reccuring_amount_type'] ) || !isset( $array['mlm_amount_type'] ) || !isset( $array['mlm_amount_value'] ) || !isset( $array['achieve'] ) || !isset( $array['settings'] ) || !isset( $array['rank_order'] )  || !isset( $array['status'] ) ){
						break;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['slug'] ) . "',
											'" . $this->sanitize( $array['label'] ) . "',
											'" . $this->sanitize( $array['amount_type'] ) . "',
											'" . $this->sanitize( $array['amount_value'] ) . "',
											'" . $this->sanitize( $array['bonus'] ) . "',
											'" . $this->sanitize( $array['pay_per_click'] ) . "',
											'" . $this->sanitize( $array['cpm_commission'] ) . "',
											'" . $this->sanitize( $array['sign_up_amount_value'] ) . "',
											'" . $this->sanitize( $array['lifetime_amount_type'] ) . "',
											'" . $this->sanitize( $array['lifetime_amount_value'] ) . "',
											'" . $this->sanitize( $array['reccuring_amount_type'] ) . "',
											'" . $this->sanitize( $array['reccuring_amount_value'] ) . "',
											'" . $this->sanitize( $array['mlm_amount_type'] ) . "',
											'" . $this->sanitize( $array['mlm_amount_value'] ) . "',
											'" . $this->sanitize( $array['achieve'] ) . "',
											'" . $this->sanitize( $array['settings'] ) . "',
											'" . $this->sanitize( $array['rank_order'] ) . "',
											'" . $this->sanitize( $array['status'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_offers':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['name'] )  || !isset( $array['start_date'] ) || !isset( $array['end_date'] )
					|| !isset( $array['amount_type'] ) || !isset( $array['amount_value'] ) || !isset( $array['settings'] ) || !isset( $array['status'] ) ){
						continue;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['name'] ) . "',
											'" . $this->sanitize( $array['start_date'] ) . "',
											'" . $this->sanitize( $array['end_date'] ) . "',
											'" . $this->sanitize( $array['amount_type'] ) . "',
											'" . $this->sanitize( $array['amount_value'] ) . "',
											'" . $this->sanitize( $array['settings'] ) . "',
											'" . $this->sanitize( $array['status'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_offers_affiliates_reference':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0  || !isset( $array['offer_id'] ) || !isset( $array['affiliate_id'] )
					|| !isset( $array['source'] ) || !isset( $array['products'] ) ){
						continue;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['offer_id'] ) . "',
											'" . $this->sanitize( $array['affiliate_id'] ) . "',
											'" . $this->sanitize( $array['source'] ) . "',
											'" . $this->sanitize( $array['products'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_mlm_relations':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['affiliate_id'] ) || !isset( $array['parent_affiliate_id'] ) ){
						continue;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['affiliate_id'] ) . "',
											'" . $this->sanitize( $array['parent_affiliate_id'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_ranks_history':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0  || !isset( $array['affiliate_id'] ) || !isset( $array['prev_rank_id'] )
					|| !isset( $array['rank_id'] ) || !isset( $array['add_date'] ) ){
						continue;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['affiliate_id'] ) . "',
											'" . $this->sanitize( $array['prev_rank_id'] ) . "',
											'" . $this->sanitize( $array['rank_id'] ) . "',
											'" . $this->sanitize( $array['add_date'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_landing_commissions':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['affiliate_id'] ) || !isset( $array['prev_rank_id'] ) || !isset( $array['rank_id'] ) || !isset( $array['add_date'] ) ){
						continue;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['affiliate_id'] ) . "',
											'" . $this->sanitize( $array['prev_rank_id'] ) . "',
											'" . $this->sanitize( $array['rank_id'] ) . "',
											'" . $this->sanitize( $array['add_date'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_coupons_code_affiliates':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['code'] ) || !isset( $array['affiliate_id'] ) || !isset( $array['type'] ) || !isset( $array['settings'] ) || !isset( $array['status'] ) ){
						continue;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['code'] ) . "',
											'" . $this->sanitize( $array['affiliate_id'] ) . "',
											'" . $this->sanitize( $array['type'] ) . "',
											'" . $this->sanitize( $array['settings'] ) . "',
											'" . $this->sanitize( $array['status'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_reports':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['affiliate_id'] ) || !isset( $array['email'] ) || !isset( $array['period'] )  || !isset( $array['last_sent'] ) ){
						continue;
					}
					$insert_string = "VALUES( '" . $this->sanitize($array['affiliate_id'] ) . "',
											'" . $this->sanitize( $array['email'] ) . "',
											'" . $this->sanitize( $array['period'] ) . "',
											'" . $this->sanitize( $array['last_sent'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
			case 'uap_ref_links':
				foreach ($xml_object->$entity_name->children() as $meta_key=>$object){
					$array = (array)$object;
					if ( count( $array ) === 0 || !isset( $array['affiliate_id'] ) || !isset( $array['url'] ) || !isset( $array['status'] ) ){
						continue;
					}
					$insert_string = "VALUES(null,
											'" . $this->sanitize( $array['affiliate_id'] ) . "',
											'" . $this->sanitize( $array['url'] ) . "',
											'" . $this->sanitize( $array['status'] ) . "'
					)";
					$this->do_basic_insert($table, $insert_string);
				}
				break;
		}
	}


	/*
	 * @param string (table name)
	 * @param string (insert values)
	 * @return none
	 */
	private function do_basic_insert($table='', $insert_values=''){
		global $wpdb;
		$query = "INSERT IGNORE INTO $table $insert_values;";
		$wpdb->query( $query );
	}

	public function sanitize( $value='' )
	{
			return sanitize_text_field( addslashes($value) );
	}

}

endif;
