		<div class="uap-wrapper">
			<form method="post" >
				<input type="hidden" name="uap_admin_forms_nonce" value="<?php echo wp_create_nonce( 'uap_admin_forms_nonce' );?>" />

				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php esc_html_e('Custom Messages', 'uap');?></h3>
					<div class="inside">
						<div class="uap-form-line">
							<div class="uap-inside-item">
									<div class="row">
										<div class="col-xs-12">
										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Username is taken:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_username_taken_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_username_taken_msg']);?>"></div>
										</div>


										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Invalid Username:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_error_username_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_error_username_msg']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Email is taken:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_email_is_taken_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_email_is_taken_msg']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Invalid Email Address:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_invalid_email_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_invalid_email_msg']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Email Addresses did not Match:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_emails_not_match_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_emails_not_match_msg']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Password did not match:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_pass_not_match_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_pass_not_match_msg']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Password Only Characters and Digits:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_pass_letter_digits_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_pass_letter_digits_msg']);?>"></div>
										</div>

										<div class="uap-labels-special uap-special-msg-alert"><div class="uap-label-text"><?php esc_html_e('Error - Password Min Length:', 'uap');?></div>
											<div class="uap-input-text">
												<div class="uap-input-text"><input type="text" name="uap_register_pass_min_char_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_pass_min_char_msg']);?>">
													<div id="uap_msg_alert" class="uap-dashboard-mini-msg-alert"><?php esc_html_e('Where {X} will be the minimum length of password.', 'uap');?></div>

												</div>
											</div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Password Characters, Digits and minimum one uppercase letter:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_pass_let_dig_up_let_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_pass_let_dig_up_let_msg']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Pending User:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_pending_user_msg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_pending_user_msg']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - Empty Required Fields:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_err_req_fields" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_err_req_fields']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - ReCaptcha:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_err_recaptcha" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_err_recaptcha']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Error - TOS:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_err_tos" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_err_tos']);?>"></div>
										</div>

										<div class="uap-labels-special"><div class="uap-label-text"><?php esc_html_e('Success Message:', 'uap');?></div>
											<div class="uap-input-text"><input type="text" name="uap_register_success_meg" class="" value="<?php echo uap_correct_text($data['metas']['uap_register_success_meg']);?>"></div>
										</div>

									</div>
									</div>
									</div>
							</div>
							<div id="uap_save_changes" class="uap-submit-form">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'uap');?>" name="save" onClick="" class="button button-primary button-large" />
							</div>
						</div>
					</div>
			</form>
		</div>
