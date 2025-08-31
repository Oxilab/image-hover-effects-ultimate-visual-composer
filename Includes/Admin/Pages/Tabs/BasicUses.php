<?php

namespace OXI_FLIP_BOX_PLUGINS\Includes\Admin\Pages\Tabs;

class BasicUses {

	public function render() {
		?>
		<div id="help" class="wpkin-help getting-started-content active">
			<div class="content-heading heading-questions">
				<h2>
					<?php _e("Using Flipbox", "oxi-flip-box-plugin"); ?>
					<mark><?php _e("Image Overlay", "oxi-flip-box-plugin"); ?></mark>
				</h2>
				<p><?php _e("Here are some common questions and guides to help you get started with Flipbox – Awesome Image Overlay.", "oxi-flip-box-plugin"); ?></p>
			</div>

			<section class="section-faq">

				<!-- FAQ Item 1 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("How do I install the Flipbox plugin?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("You can install Flipbox like any other WordPress plugin. Go to Plugins → Add New → Upload Plugin, upload the .zip file, and activate. For a step-by-step guide, check the documentation:", "oxi-flip-box-plugin"); ?>
							<a href="https://wpkindemos.com/flipbox/docs/installations/how-to-install-the-plugin/" target="_blank" rel="noopener noreferrer">
								<?php _e("Installation Guide", "oxi-flip-box-plugin"); ?>
							</a>
						</p>
					</div>
				</div>

				<!-- FAQ Item 2 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("How can I create my first Flipbox?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("After activation, go to Flipbox → Add New from your WordPress dashboard. Choose a layout, upload front & back images, add content, and customize the flip animation. Once done, save and copy the shortcode to display it anywhere on your site.", "oxi-flip-box-plugin"); ?>
						</p>
					</div>
				</div>

				<!-- FAQ Item 3 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("How do I display a Flipbox on my page or post?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("Each Flipbox you create generates a shortcode. Simply copy the shortcode and paste it inside any post, page, or widget area where you want the Flipbox to appear. Compatible with Gutenberg, Elementor, WPBakery, Divi, and more.", "oxi-flip-box-plugin"); ?>
						</p>
					</div>
				</div>

				<!-- FAQ Item 4 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("Can I customize Flipbox animations and styles?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("Yes! Flipbox provides multiple flip animations (horizontal, vertical, 3D effects, fade) along with customizable colors, fonts, borders, and overlays. You can design them directly from the settings panel without writing any code.", "oxi-flip-box-plugin"); ?>
						</p>
					</div>
				</div>

				<!-- FAQ Item 5 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("Does Flipbox work with my page builder?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("Absolutely! Flipbox shortcodes work seamlessly with Elementor, Gutenberg, WPBakery (Visual Composer), Divi, Beaver Builder, and all major page builders.", "oxi-flip-box-plugin"); ?>
						</p>
					</div>
				</div>

				<!-- FAQ Item 6 -->
				<div class="faq-item">
					<div class="faq-header" style="cursor:pointer">
						<i class="dashicons dashicons-arrow-down-alt2"></i>
						<h3>
							<?php _e("Where can I get support if I face an issue?", "oxi-flip-box-plugin"); ?>
						</h3>
					</div>
					<div class="faq-body" style="display:none;">
						<p>
							<?php _e("For free users, you can ask questions in the WordPress.org support forum. For Pro users, premium email support is available. You can also contact us directly:", "oxi-flip-box-plugin"); ?>
							<a href="https://wpkin.com/contact-us/" target="_blank" rel="noopener noreferrer">
								<?php _e("Contact Support", "oxi-flip-box-plugin"); ?>
							</a>
						</p>
					</div>
				</div>

			</section>
		</div>
		<?php
	}
}
