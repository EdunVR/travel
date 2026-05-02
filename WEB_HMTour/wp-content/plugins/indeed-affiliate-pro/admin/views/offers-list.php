<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php esc_html_e('Offers', 'uap');?></span></div>
		<a href="<?php echo esc_url($data['url-add_edit']);?>" class="uap-add-new-like-wp"><i class="fa-uap fa-add-uap"></i><span><?php esc_html_e('Add New Offer', 'uap');?></span></a>
		<span class="uap-top-message"><?php esc_html_e('...create your Offers for specific Products or Affiliates', 'uap');?></span>
		<?php if (!empty($data['errors'])) : ?>
			<div class="uap-wrapp-the-errors"><?php echo esc_html($data['errors']);?></div>
		<?php endif;?>
		<?php if (!empty($data['listing_items'])) : ?>
			<form  method="post" id="form_offers">

				<input type="hidden" name="uap_admin_forms_nonce" value="<?php echo wp_create_nonce( 'uap_admin_forms_nonce' );?>" />

				<div class="uap-offer-items-wrap">
					<?php foreach ($data['listing_items'] as $arr) : ?>
						<?php
							$inside_data = unserialize($arr['settings']);
							$color = (empty($inside_data['color']))	? '000' : '' . $inside_data['color'];
							$disabled = (empty($arr['status'])) ? 'uap-disabled-box' : '';
						?>
					   <div class="uap-admin-dashboard-offer-box-wrap <?php echo esc_attr($disabled);?>">
					      <div class="uap-admin-dashboard-offer-box  uap-box-background-<?php echo esc_attr($color);?>" id="uap-b-item-<?php echo esc_attr($arr['id']);?>">
					         <div class="uap-admin-dashboard-offer-box-main">
					            <div class="uap-admin-dashboard-offer-box-title"><?php echo esc_attr($arr['name']);?></div>
					            <div class="uap-admin-dashboard-offer-box-content">
								<?php esc_html_e('Target Affiliates:', 'uap');?>
								<?php echo esc_html($arr['affiliates']);?>
								</div>
					            <div class="uap-admin-dashboard-offer-box-links-wrap">
					               <div class="uap-admin-dashboard-offer-box-links">
					                  <a href="<?php echo esc_url($data['url-add_edit'] . '&id=' . $arr['id']);?>" class="uap-admin-dashboard-offer-box-link"><?php esc_html_e('Edit', 'uap');?></a>
					                  <div onclick="uapDeleteFromTable(<?php echo esc_attr($arr['id']);?>, 'Offer', '#delete_offer_id', '#form_offers');" class="uap-admin-dashboard-offer-box-link"><?php esc_html_e('Delete', 'uap');?></div>
					               </div>
					            </div>
					         </div>
					         <div class="uap-admin-dashboard-offer-box-bottom">
					            <div class="uap-admin-dashboard-offer-box-files">
									<?php switch ($arr['amount_type']){
										case 'flat' : echo uap_format_price_and_currency($currency, $arr['amount_value']); break;
										case 'percentage' : echo esc_html($arr['amount_value']) . '%'; break;
									} ?>
					               <div class="uap-admin-dashboard-offer-box-dest">&nbsp;</div>
					            </div>
					            <div class="uap-admin-dashboard-offer-box-date"><span><?php esc_html_e('From', 'uap');?> </span><span><?php echo esc_html($arr['start_date']);?></span><br/><span><?php esc_html_e('to', 'uap');?> </span><span><?php echo esc_html($arr['end_date']);?></span></div>
					            <div class="clear"></div>
					         </div>
					      </div>
					   </div>
					<?php endforeach;?>
					<div class="uap-clear"></div>
				</div>
				<input type="hidden" name="delete_offers[]" value="" id="delete_offer_id" />
			</form>
		<?php else : ?>
			<h4 class="uap-missing-nmessage"><?php esc_html_e('No Offers to show. Please, add your first Offer. ', 'uap');?></h4>
		<?php endif;?>
</div>
