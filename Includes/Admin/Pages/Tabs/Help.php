<?php

namespace OXI_FLIP_BOX_PLUGINS\Includes\Admin\Pages\Tabs;

class Help {

	public function render() {
		?>
		<div id="help" class="wpkin-help getting-started-content active">
			<div class="content-heading heading-questions">
				<h2>
					<?php _e("Frequently Asked", "oxi-flip-box-plugin"); ?>
					<mark><?php _e("Questions", "oxi-flip-box-plugin"); ?></mark>
				</h2>
			</div>

			<section class="section-faq">

				<!-- FAQ Item 1 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("I have a pre-sale question. How can I get support?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("For any pre-sale inquiries, please contact us directly by submitting a form here:", "oxi-flip-box-plugin"); ?>
							<a href="https://wpkin.com/contact-us/" target="_blank" rel="noopener noreferrer">
								<?php _e("Contact Us", "oxi-flip-box-plugin"); ?>
							</a>
						</p>
					</div>
				</div>

				<!-- FAQ Item 2 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("How do I install the Flipbox plugin?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("Go to Plugins → Add New → Upload Plugin, choose the Flipbox .zip file, install and activate. You can also install it directly from the WordPress plugin directory by searching for 'Flipbox – Awesomes Image Overlay'.", "oxi-flip-box-plugin"); ?>
							<a href="https://wpkindemos.com/flipbox/docs/installations/how-to-install-the-plugin/" target="_blank" rel="noopener noreferrer">
								<?php _e("Read Installation Guide", "oxi-flip-box-plugin"); ?>
							</a>
						</p>
					</div>
				</div>

				<!-- FAQ Item 3 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("I purchased the PRO version, but it still shows the free plan. What should I do?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("Deactivate the free plugin first (your data will not be lost). Then upload, install, and activate the PRO version. Finally, enter your license key from your account area to unlock PRO features.", "oxi-flip-box-plugin"); ?>
						</p>
					</div>
				</div>

				<!-- FAQ Item 4 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("How do I display a Flipbox on my website?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("Each Flipbox you create generates a shortcode. Copy this shortcode and paste it inside any post, page, or widget area where you want the Flipbox to appear. Works with Gutenberg, Elementor, WPBakery, Divi, and other builders.", "oxi-flip-box-plugin"); ?>
						</p>
					</div>
				</div>

			</section>

			<div class="content-heading heading-help">
				<h2>
					<?php _e("Need", "oxi-flip-box-plugin"); ?>
					<mark><?php _e("Help?", "oxi-flip-box-plugin"); ?></mark>
				</h2>
				<p>
					<?php _e("Read our documentation or contact us directly for support.", "oxi-flip-box-plugin"); ?>
				</p>
			</div>

			<div class="facebook-cta">
				<div class="cta-content">
					<h2><?php _e("Support", "oxi-flip-box-plugin"); ?></h2>
					<p>
						<?php _e("Join our community and get help from other Flipbox users. Share your ideas, solve problems, and learn tips to get the most out of Flipbox.", "oxi-flip-box-plugin"); ?>
					</p>
				</div>
				<div class="cta-btn">
					<a href="https://wordpress.org/support/plugin/image-hover-effects-ultimate-visual-composer/" class="wpkin-btn btn-primary" target="_blank" rel="noopener noreferrer">
						<i class="dashicons dashicons-sos"></i>
						<?php _e("Get Support", "oxi-flip-box-plugin"); ?>
					</a>
				</div>
			</div>
		</div>

<?php
	}
}
