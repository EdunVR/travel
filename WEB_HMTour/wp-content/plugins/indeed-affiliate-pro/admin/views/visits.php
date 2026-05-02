<?php
		wp_enqueue_script( 'indeed_csv_export', UAP_URL . 'assets/js/csv_export.js', ['jquery'] );
?>
<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php esc_html_e('Clicks', 'uap');?></span></div>

		<?php if (!empty($data['subtitle'])):?>
			<h4><?php echo esc_uap_content($data['subtitle']);?></h4>
		<?php endif;?>

	<div class="uap-special-box">
		<?php echo esc_uap_content($data['filter']);?>
	</div>

	<div class="uap-special-buttons-users">
		<?php
				$filters = [
					'start' 									=> empty($_REQUEST['udf']) ? '' : $_REQUEST['udf'],
					'end' 										=> empty($_REQUEST['udu']) ? '' : $_REQUEST['udu'],
					'status' 									=> isset($_REQUEST['u_sts']) ? $_REQUEST['u_sts'] : -1,
					'affiliate_username'			=> isset($_REQUEST['aff_u']) ? $_REQUEST['aff_u'] : '',
				];
		?>
		<div class="uap-special-button js-uap-export-csv" data-filters='<?php if ( isset($_REQUEST) ){
			echo serialize($filters);
		}?>' data-export_type="visits">
				<i class="fa-uap fa-export-csv"></i><?php esc_html_e( 'Export CSV', 'uap' );?>
		</div>
	</div>

	<?php if (!empty($data['listing_items'])) : ?>

			<div class="uap-referrals-list-numbers-wrapper">
				<strong><?php esc_html_e('Number of Clicks to Display:', 'uap');?></strong>
				<select name="uap_limit" data-url="<?php echo esc_url($data['base_list_url'] . '&uap_limit=');?>" class="uap-js-visits-list-limit-number" >
					<?php
						foreach ($this->items_per_page as $value){
							$selected = ($value==$limit) ? 'selected' : '';
							?>
							<option value="<?php echo esc_attr($value);?>" <?php echo esc_attr($selected);?>><?php echo esc_html($value);?></option>
							<?php
						}
					?>
				</select>
			</div>
			<div class="uap-referrals-list-pagination-wrapper">
				<?php
					if (!empty($data['pagination'])) :
						echo esc_uap_content($data['pagination']);
					endif;
				?>
			</div>

			<form  method="post" id="form_visits">
				<div id="uap_delete" class="uap-delete-wrapp">
					<input type="submit" value="<?php esc_html_e('Delete', 'uap');?>" name="delete" class="button button-primary button-large">
				</div>
					<table class="wp-list-table widefat fixed tags uap-admin-tables">
						<thead>
							<tr>
								<th class="uap-table-check-col"><input type="checkbox" onClick="uapSelectAllCheckboxes( this, '.uap-delete-visit' );" /></th>
								<th><?php esc_html_e('IP', 'uap');?></th>
								<th><?php esc_html_e('Affiliate Username', 'uap');?></th>
								<th class="uap-table-visits-referral"><?php esc_html_e('Referral ID', 'uap');?></th>
								<th class="uap-visits-list-url"><?php esc_html_e('Destination URL', 'uap');?></th>
								<th class=""><?php esc_html_e('Comming From', 'uap');?></th>
								<th class="uap-table-visits-browser"><?php esc_html_e('Browser', 'uap');?></th>
								<th class="uap-table-visits-browser"><?php esc_html_e('Device', 'uap');?></th>
								<th><?php esc_html_e('Date', 'uap');?></th>
								<th><?php esc_html_e('Status', 'uap');?></th>
							</tr>
						</thead>

						<tbody class="ui-sortable uap-alternate">
							<?php foreach ($data['listing_items'] as $array) : ?>
								<tr onmouseover="uapDhSelector('#visit_<?php echo esc_attr($array['id']);?>', 1);" onmouseout="uapDhSelector('#visit_<?php echo esc_attr($array['id']);?>', 0);">
									<th><input type="checkbox" value="<?php echo esc_attr($array['id']);?>" name="delete_visits[]" class="uap-delete-visit"/></th>
									<td>
										<?php echo esc_html($array['ip']);?>
										<div id="visit_<?php echo esc_attr($array['id']);?>" class="uap-visibility-hidden">
											<a onclick="uapDeleteFromTable(<?php echo esc_attr($array['id']);?>, 'Visit', '#delete_visit_h', '#form_visits');" href="javascript:return false;" class="uap-color-red"><?php esc_html_e('Delete', 'uap');?></a>
										</div>
									</td>
									<td><?php
									echo esc_uap_content('<div class="uap-list-affiliates-name-label">');
										if (!empty($array['username'])){
												echo esc_html($array['username']);
										} else {
												esc_html_e('Unknown', 'uap');
										}
									echo esc_uap_content('</div>');
									?></td>
									<td><?php if (empty($array['referral_id'])){
										 echo esc_html('-');
									}else{
										 echo esc_html($array['referral_id']);
									}?>
									</td>
									<td><a href="<?php echo esc_url($array['url']);?>" target="_blank"><?php echo esc_url($array['url']);?></a></td>
									<td><a href="<?php echo esc_url($array['ref_url']);?>" target="_blank"><?php echo esc_url($array['ref_url']);?></a></td>
									<td><?php echo esc_html($array['browser']);?></td>
									<td><i class="<?php echo esc_uap_content("fa-uap fa-" . $array['device'] . "-uap");?>"></i></td>
									<td class="uap-general-date-color"><?php echo uap_convert_date_to_us_format($array['visit_date']);?></td>
									<td><?php
										if (!empty($array['referral_id'])){
											 echo esc_uap_content('<div class="referral-status-verified">' . esc_html__('Converted', 'uap') . '</div>');
										}else{
											 echo esc_uap_content('<div class="referral-status-refuse">' . esc_html__('Just Visit', 'uap') . '</div>');
										}
									?></td>
								</tr>
							<?php endforeach;?>
						</tbody>
						<tfoot>
							<tr>
								<th><input type="checkbox" onClick="uapSelectAllCheckboxes( this, '.uap-delete-visit' );" /></th>
								<th><?php esc_html_e('IP', 'uap');?></th>
								<th><?php esc_html_e('Affiliate Username', 'uap');?></th>
								<th><?php esc_html_e('Referral ID', 'uap');?></th>
								<th><?php esc_html_e('Destination URL', 'uap');?></th>
								<th><?php esc_html_e('Comming From', 'uap');?></th>
								<th><?php esc_html_e('Browser', 'uap');?></th>
								<th><?php esc_html_e('Device', 'uap');?></th>
								<th><?php esc_html_e('Date', 'uap');?></th>
								<th><?php esc_html_e('Status', 'uap');?></th>
							</tr>
						</tfoot>
					</table>
				<div class="uap-delete-wrapp">
					<input type="submit" value="<?php esc_html_e('Delete', 'uap');?>" name="delete" class="button button-primary button-large">
				</div>
				<div class="uap-referrals-list-pagination-wrapper">
					<?php
						if (!empty($data['pagination'])) :
							echo esc_uap_content($data['pagination']);
						endif;
					?>
				</div>
				<input type="hidden" name="delete_visits[]" value="" id="delete_visit_h" />

				<input type="hidden" name="uap_admin_forms_nonce" value="<?php echo wp_create_nonce( 'uap_admin_forms_nonce' );?>" />

			</form>
		<?php else : ?>
			<table class="wp-list-table widefat fixed tags uap-admin-tables">
				<thead>
					<tr>
						<th><?php esc_html_e('IP', 'uap');?></th>
						<th><?php esc_html_e('Affiliate Username', 'uap');?></th>
						<th class="uap-table-visits-referral"><?php esc_html_e('Referral ID', 'uap');?></th>
						<th class="uap-visits-list-url"><?php esc_html_e('Destination URL', 'uap');?></th>
						<th class=""><?php esc_html_e('Comming From', 'uap');?></th>
						<th class="uap-table-visits-browser"><?php esc_html_e('Browser', 'uap');?></th>
						<th class="uap-table-visits-browser"><?php esc_html_e('Device', 'uap');?></th>
						<th><?php esc_html_e('Date', 'uap');?></th>
						<th><?php esc_html_e('Status', 'uap');?></th>
					</tr>
				</thead>

				<tbody class="ui-sortable uap-alternate">
					<tr>
						<td><?php esc_html_e('No Clicks Stored.', 'uap');?></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th><?php esc_html_e('IP', 'uap');?></th>
						<th><?php esc_html_e('Affiliate Username', 'uap');?></th>
						<th><?php esc_html_e('Referral ID', 'uap');?></th>
						<th><?php esc_html_e('Destination URL', 'uap');?></th>
						<th><?php esc_html_e('Comming From', 'uap');?></th>
						<th><?php esc_html_e('Browser', 'uap');?></th>
						<th><?php esc_html_e('Device', 'uap');?></th>
						<th><?php esc_html_e('Date', 'uap');?></th>
						<th><?php esc_html_e('Status', 'uap');?></th>
					</tr>
				</tfoot>
			</table>
		<?php endif;?>
</div>
