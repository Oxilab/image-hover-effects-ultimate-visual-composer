<?php

namespace OXI_FLIP_BOX_PLUGINS\Includes\Admin\Pages\Tabs;

class Changelog {

    public function render() {

        // Full changelog array
        $logs = [
            [
                'version' => '3.1.0',
                'date' => '08-10-2026',
                'sections' => [
                    'new' => [
                        'New Flipbox block for the block editor (Gutenberg): add it, choose a flip box, and see a live preview right in the editor (hover it to see it flip). It shows exactly what the shortcode shows, supports wide and full width, and links to the flip box\'s editor.',
                        'New Flip Trigger option in General Settings: flip on hover (default, unchanged for existing flip boxes) or only on click or tap. Works for all 29 designs, on touch screens and with the keyboard.',
                        'Added a Clone button next to Edit on every flip box item in the editor preview: it copies the item to the end of the list without reloading the page.',
                        'Added a copy button to the shortcode and the PHP code in the editor\'s Shortcode panel: one click copies it.',
                        'New getting started video tutorial on the Getting Started page and in the editor\'s Support tab.',
                        'Custom CSS now has a real code editor (the same one as WordPress\'s Additional CSS): line numbers, syntax colors and indenting. Line breaks are kept when you save; CSS you saved before loads exactly as it was.',
                        'Added a Danger zone to the Settings page: delete all flip boxes, items and settings at once after typing DELETE to confirm, or choose to remove all Flipbox data when the plugin is deleted.',
                    ],
                    'fix' => [
                        'Fixed flip boxes flashing and shaking for a moment after a page reload (most visible when hovering right away): the box no longer animates into place while the page loads.',
                        'Removing a template from the Create New list now only removes Flipbox\'s own entry.',
                        'Saving in the editor now updates the preview in place, so an element you are looking at in the browser\'s developer tools (Inspect) stays there instead of disappearing.',
                    ],
                    'enhancement' => [
                        'The flip box editor now saves without reloading the page: Save changes, saving an item, Rename and Delete update the preview in place and confirm with a short message.',
                        'Editing a flip box item now opens its dialog instantly, without reloading the page first.',
                        'Deleting a flip box item now asks in a clear confirmation dialog that names the item, instead of the browser\'s plain alert.',
                        'Redesigned the "How to use?" menu: every guide now shows its tool\'s own logo (Elementor, WPBakery, WordPress) and a short description, plus a quick link to all documentation.',
                        'Refreshed the flip box editor: a header showing which flip box you are editing with a quick way back, cleaner tabs and settings panels, clearer "Add a flip box" and "Reorder flip boxes" buttons, "Save changes" and "Rename" buttons, accordion arrows on every panel, and tidier dialogs.',
                        'Redesigned the Import Templates page as a template library: live previews of every design and one click "Add to Create New".',
                        'Redesigned the Create New page: live previews of every design with a "Use this design" button, so you name the flip box and start editing in one step.',
                        'Redesigned the Flip Box page: one click copy for the shortcode and PHP code, newest flip boxes first, quick search, item counts, and clear dialogs for import, clone and delete.',
                        'Redesigned the Settings page with grouped cards, on/off switches, a live save status for every option, a License card and quick links to docs and support.',
                        'Redesigned the Account page with the plugin header menu, a clear license overview, a plan badge, tidy billing details and an easy to read payments list.',
                    ],
                ],
            ],
            [
                'version' => '3.0.3',
                'date' => '04-09-2026',
                'sections' => [
                    'new' => [
                        'Added a "How to use?" documentation menu to the plugin header with quick links for the shortcode, Elementor, WPBakery and the WordPress widget.',
                    ],
                    'enhancement' => [
                        'Any notice now appears below the plugin\'s own menu bar instead of above it.',
                    ],
                ],
            ],
            [
                'version' => '3.0.2',
                'date' => '21-06-2026',
                'sections' => [
                    'new' => [
                        'Added Divi Builder module for Flipbox.',
                        'Added Flipbox shortcode support inside Divi (Text/Code modules and the native module).',
                        'Added the plugin icon to the Elementor widget.',
                    ],
                    'fix' => [
                        'Fixed Flipbox styles not loading inside the Divi Visual Builder.',
                    ],
                    'enhancement' => [
                        'Improved page builder compatibility (Divi 5).',
                    ],
                ],
            ],
            [
                'version' => '3.0.1',
                'date' => '25-05-2026',
                'sections' => [
                    'fix' => [
                        'Fixed Style 4 back side content clipped on mobile.',
                        'Fixed Style 4 background image distortion on mobile.',
                    ],
                    'enhancement' => [
                        'Compatible with WordPress 6.8.',
                    ],
                ],
            ],
            [
                'version' => '3.0.0',
                'date' => '14-04-2026',
                'sections' => [
                    'fix' => [
                        'Fixed dimension calculation issue for PHP 8.',
                        'Fixed PHP 8 fatal TypeError on backend.',
                    ],
                    'enhancement' => [
                        'PHP 8 compatibility across all 29 styles.',
                        'Rebranded to Oxilab with backward compatibility.',
                        'Demo images now load from CDN.',
                    ],
                    'remove' => [
                        'Removed external API calls.',
                    ],
                ],
            ],
			[
                'version' => '2.10.7',
                'date' => '24-03-2026',
                'sections' => [
                    'fix' => [
                        'Fixed elementor addon issue.',
                        'Fixed image loading issue.',
                    ],
                ],
            ],
            [
                'version' => '2.10.6',
                'date' => '13-12-2025',
                'sections' => [
                    'remove' => [
                        'Removed admin Support and Comments panel.',
                        'Removed related filter registration to avoid missing callbacks.',
                        'Flip Box list: Removed bottom import box.',
                    ],
                    'enhancement' => [
                        'Improved Create page UX: centered Import Templates button and removed box.',
                        'Added top-right Import More Templates button in Create header.',
                        'Flip Box list: Added Shortcode label with Add New and Import buttons above table.',
                        'Flip Box list: Replaced Shortcode/PHP inputs with copyable Shortcode chip UI.',
                        'Flip Box list: Import button now opens JSON import modal.',
                    ],
                    'new' => [
                        'Elementor: Added Flipbox widget under General with ID control.',
                    ],
                ],
            ],
            [
                'version' => '2.10.5',
                'date' => '05-10-2025',
                'sections' => [
                    'fix' => [
                        'Fixed critical error: Call to a member function get_row() on null in Public_Helper.php.',
                        'Fixed critical error: Installation class not found during plugin activation and upgrades.',
                    ],
                    'enhancement' => [
                        'Added safety checks for database initialization in shortcode rendering.',
                        'Improved plugin stability and error handling.',
                    ],
                ],
            ],
            [
                'version' => '2.10.4',
                'date' => '08-09-2025',
                'sections' => [
                    'fix' => [
                        'Fixed Visual composer fatal error issue get_row().',
                    ],
                ],
            ],
			[
                'version' => '2.10.3',
                'date' => '07-09-2025',
                'sections' => [
                    'fix' => [
                        'Fixed Visual composer fatal error issue.',
                        'Fixed Widget fatal error issue.',
                    ],
                ],
            ],
            [
                'version' => '2.10.2',
                'date' => '05-09-2025',
                'sections' => [
                    'fix' => [
                        'Fixed 1596 characters of unexpected output during activation issue.',
                    ],
                ],
            ],
            [
                'version' => '2.10.1',
                'date' => '05-09-2025',
                'sections' => [
                    'enhancement' => [
						'Improved plugin structure for better readability and maintainability.',
						'Updated the Getting Started page for clearer instructions.',
					],
                    'fix' => [
                        'Fixed security issue.',
                    ],
                ],
            ],
            [
                'version' => '2.10.0',
                'date' => '15-08-2025',
                'sections' => [
                    'new' => [
                        'Added Getting Started page.',
                        'Added Freemius for license management.',
                    ],
                ],
            ],
            [
                'version' => '2.9.8',
                'date' => '10-07-2025',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.8.',
                    ],
                    'fix' => [
                        'Fixed shortcode list issue.',
                    ],
                ],
            ],
            [
                'version' => '2.9.7',
                'date' => '20-06-2025',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.7.',
                    ],
                    'fix' => [
                        'Fixed data table search issue.',
                    ],
                ],
            ],
            [
                'version' => '2.9.6',
                'date' => '05-05-2025',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.6.2.',
                    ],
                    'fix' => [
                        'Fixed Ajax bugs.',
                    ],
                ],
            ],
            [
                'version' => '2.9.5',
                'date' => '01-04-2025',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.4.3.',
                    ],
                    'fix' => [
                        'Fixed Ajax bugs.',
                    ],
                ],
            ],
            [
                'version' => '2.9.3',
                'date' => '12-02-2025',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.3.0.',
                    ],
                    'fix' => [
                        'Fixed Ajax bugs.',
                    ],
                ],
            ],
            [
                'version' => '2.9.2',
                'date' => '25-01-2025',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.2.2.',
                    ],
                    'fix' => [
                        'Fixed some settings issues.',
                    ],
                ],
            ],
            [
                'version' => '2.9.1',
                'date' => '10-01-2025',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.2.0.',
                    ],
                    'fix' => [
                        'Fixed Ajax bugs.',
                    ],
                ],
            ],
            [
                'version' => '2.9.0',
                'date' => '15-12-2024',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.1.1.',
                    ],
                    'fix' => [
                        'Fixed echo bugs.',
                    ],
                ],
            ],
            [
                'version' => '2.8.5',
                'date' => '01-11-2024',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.0.3.',
                        'Fixed some settings issues.',
                    ],
                ],
            ],
            [
                'version' => '2.8.4',
                'date' => '15-10-2024',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.0.1.',
                        'Fixed some settings issues.',
                    ],
                ],
            ],
            [
                'version' => '2.8.3',
                'date' => '01-10-2024',
                'sections' => [
                    'enhancement' => [
                        'Tested compatibility with WordPress 6.0.0.',
                    ],
                    'new' => [
                        'Added alt tag for images.',
                    ],
                ],
            ],
            [
                'version' => '2.8.2',
                'date' => '15-09-2024',
                'sections' => [
                    'fix' => [
                        'Solved HTML issues.',
                    ],
                ],
            ],
            [
                'version' => '2.8.0',
                'date' => '01-09-2024',
                'sections' => [
                    'new' => [
                        'Added support for HTML tags.',
                    ],
                ],
            ],
            [
                'version' => '2.7.1',
                'date' => '15-08-2024',
                'sections' => [
                    'fix' => [
                        'Fixed new Flipbox issues.',
                    ],
                ],
            ],
            [
                'version' => '2.7.0',
                'date' => '01-08-2024',
                'sections' => [
                    'enhancement' => [
                        'Updated Flipbox modules.',
                    ],
                ],
            ],
        ];
        ?>
        
        <div id="what-new" class="content-what-new">
            <div class="content-heading">
                <h2>
                    <?php echo __( 'Exploring the', 'oxi-flip-box-plugin' ); ?> 
                    <mark><?php echo __( 'Latest Updates', 'oxi-flip-box-plugin' ); ?></mark>
                </h2>
                <p>
                    <?php echo __( 'Dive into the recent changelog for fresh insights about new features and improvements.', 'oxi-flip-box-plugin' ); ?>
                </p>
            </div>

            <?php foreach ( $logs as $log ) : ?>
                <div class="log">
                    <div class="log-header" style="cursor:pointer;">
                        <span class="log-version"><?php echo esc_html( $log['version'] ); ?></span>
                        <span class="log-date">(<?php echo esc_html( $log['date'] ); ?>)</span>
                        <i class="dashicons dashicons-arrow-down-alt2"></i>
                    </div>
                    <div class="log-body" style="display:none;">
                        <?php foreach ( $log['sections'] as $section => $items ) : ?>
                            <div class="log-section <?php echo esc_attr( $section ); ?>">
                                <h3>
                                    <?php
                                        $section_titles = [
                                            'new' => __( 'New Features', 'oxi-flip-box-plugin' ),
                                            'fix' => __( 'Bug Fixes', 'oxi-flip-box-plugin' ),
                                            'enhancement' => __( 'Improvements', 'oxi-flip-box-plugin' ),
                                            'remove' => __( 'Deprecations', 'oxi-flip-box-plugin' ),
                                        ];
                                        echo $section_titles[ $section ];
										?>
                                </h3>
                                <?php foreach ( $items as $item ) : ?>
                                    <div class="log-item log-item-<?php echo esc_attr( $section ); ?>">
                                        <?php
                                            $section_icons = [
                                                'new' => 'dashicons-plus-alt2',
                                                'fix' => 'dashicons-saved',
                                                'enhancement' => 'dashicons-star-filled',
                                                'remove' => 'dashicons-trash',
                                            ];
											?>
                                        <i class="dashicons <?php echo esc_attr( $section_icons[ $section ] ); ?>"></i>
                                        <?php echo esc_html( $item ); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
    }
}