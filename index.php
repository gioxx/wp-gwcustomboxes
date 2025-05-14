<?php
/**
 * Plugin Name:         Custom Boxes per Gioxx's Wall
 * Plugin URI:          https://github.com/gioxx/wp-gwcustomboxes
 * Description:         Box personalizzati per gli articoli di Gioxx's Wall, ora aggiornabili tramite Git Updater.
 * Version:             0.29
 * Author:              Gioxx
 * Author URI:          https://gioxx.org
 * License:             GPL3
 * Text Domain:         wp-gwcustomboxes
 *
 * GitHub Plugin URI:   gioxx/wp-gwcustomboxes
 * Primary Branch:      main
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'GWCustomBoxes' ) ) {
    final class GWCustomBoxes {
        /**
         * Initialization
         */
        public function __construct() {
            add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
            add_filter( 'the_content', array( $this, 'filter_content' ) );
        }

        /**
         * Load plugin textdomain for translations
         */
        public function load_textdomain() {
            load_plugin_textdomain(
                'wp-gwcustomboxes',
                false,
                dirname( plugin_basename( __FILE__ ) ) . '/languages/'
            );
        }

        /**
         * Enqueue front-end styles
         */
        public function enqueue_styles() {
            wp_enqueue_style(
                'gwcustomboxes-styles',
                plugin_dir_url( __FILE__ ) . 'css/plg_customboxes.css',
                array(),
                '0.29'
            );
        }

        /**
         * Append custom boxes to post content
         *
         * @param string $content Original post content.
         * @return string Modified content.
         */
        public function filter_content( $content ) {
            if ( ! is_singular() && ! is_home() && ! is_archive() ) {
                return $content;
            }

            global $post;

            // Determine prodotto state if relevant
            $statoProdotto = '';
            $tags_for_state = array( 'banco-prova', 'banco-prova-baby', 'banco-prova-console', 'sponsored' );
            if ( has_tag( $tags_for_state, $post ) ) {
                $meta = get_post_meta( $post->ID, 'statoprodotto', true );
                if ( ! empty( $meta ) ) {
                    $statoProdotto = sanitize_text_field( $meta );
                }
            }

            // Old post alert (posts older than 5 months), disabled for front page
            if ( is_singular() && !is_front_page() ) {
                $published = strtotime( $post->post_date_gmt );
                if ( $published && ( time() - $published ) > ( 5 * MONTH_IN_SECONDS ) ) {
                    $content .= $this->get_alert_box();
                }
            }

            // Possible box tags
            $boxes = array(
                'piccoli-passi'         => 'piccolipassi',
                'banco-prova'           => 'bancoprova',
                'banco-prova-baby'      => 'bancoprovababy',
                'banco-prova-console'   => 'bancoprovaconsole',
                'android-corner'        => 'androidcorner',
                'mrl'                   => 'mrl',
                'pillole'               => 'pillole',
                'press-start-milano'    => 'pstartmilano',
                'sponsored'             => 'sponsored',
            );

            foreach ( $boxes as $tag => $boxKey ) {
                if ( has_tag( $tag, $post ) ) {
                    $content .= $this->get_box_html( $boxKey, $statoProdotto );
                }
            }

            return $content;
        }

        /**
         * Get HTML for the old-post alert box
         *
         * @return string
         */
        private function get_alert_box() {
            ob_start(); ?>
            <div class="gb-block-notice timealert">
                <div class="gb-notice-title">
                    <p class="timealert"><?php esc_html_e( "L'articolo potrebbe non essere aggiornato", 'wp-gwcustomboxes' ); ?></p>
                </div>
                <div class="gb-notice-text timealert">
                    <p class="timealert">
                        <?php printf(
                            esc_html__( 'Questo post è stato scritto più di %d mesi fa, potrebbe non essere aggiornato. Per qualsiasi dubbio lascia un commento!', 'wp-gwcustomboxes' ),
                            5
                        ); ?>
                    </p>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }

        /**
         * Return HTML for a given box type
         *
         * @param string $type Box key.
         * @param string $state Stato prodotto.
         * @return string
         */
        private function get_box_html( $type, $state ) {
            ob_start();
            switch ( $type ) :
                case 'piccolipassi': ?>
                    <div class="gb-block-notice piccolipassi">
                        <div class="gb-notice-title piccolipassi">
                            <p><?php esc_html_e( 'A piccoli passi', 'wp-gwcustomboxes' ); ?></p>
                        </div>
                        <div class="gb-notice-text piccolipassi">
                            <img class="alignright" style="max-height:120px;" title="<?php esc_attr_e( 'A piccoli passi...', 'wp-gwcustomboxes' ); ?>" alt="<?php esc_attr_e( 'A piccoli passi...', 'wp-gwcustomboxes' ); ?>" src="<?php echo esc_url( network_site_url( '/wp-content/uploads/2019/03/logo.png' ) ); ?>" />
                            <p><?php esc_html_e( "Serie di articoli per chi muove i primi passi nel mondo tech.", 'wp-gwcustomboxes' ); ?> <a href="<?php echo esc_url( network_site_url( '/tag/piccoli-passi' ) ); ?>"><?php esc_html_e( 'fai clic qui', 'wp-gwcustomboxes' ); ?></a>.</p>
                        </div>
                    </div>
                <?php break;

                case 'bancoprova': ?>
                    <div class="gb-block-notice bancoprova">
                        <div class="gb-notice-title bancoprova">
                            <p><?php esc_html_e( 'Disclaimer (per un mondo più pulito)', 'wp-gwcustomboxes' ); ?></p>
                        </div>
                        <div class="gb-notice-text">
                            <p><i class="fab fa-grav fa-pull-right fa-7x"></i> <?php esc_html_e( 'Articoli con esperienza prodotto, pro e contro.', 'wp-gwcustomboxes' ); ?> <a href="<?php echo esc_url( network_site_url( '/tag/banco-prova' ) ); ?>"><?php esc_html_e( 'fai clic qui', 'wp-gwcustomboxes' ); ?></a>.
                                <?php if ( $state ) : ?>
                                    <br><strong><?php esc_html_e( 'Prodotto:', 'wp-gwcustomboxes' ); ?></strong> <em><?php echo esc_html( $state ); ?></em>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php break;

                case 'bancoprovababy': ?>
                    <div class="gb-block-notice bancoprova">
                        <div class="gb-notice-title bancoprova">
                            <p><?php esc_html_e( 'Disclaimer (per un mondo più pulito)', 'wp-gwcustomboxes' ); ?></p>
                        </div>
                        <div class="gb-notice-text">
                            <p><i class="fa-solid fa-child fa-pull-right fa-7x"></i> <?php esc_html_e( 'Articoli "Banco Prova Baby" con pro e contro.', 'wp-gwcustomboxes' ); ?> <a href="<?php echo esc_url( network_site_url( '/tag/banco-prova-baby' ) ); ?>"><?php esc_html_e( 'fai clic qui', 'wp-gwcustomboxes' ); ?></a>.
                                <?php if ( $state ) : ?>
                                    <br><strong><?php esc_html_e( 'Prodotto:', 'wp-gwcustomboxes' ); ?></strong> <em><?php echo esc_html( $state ); ?></em>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php break;

                case 'bancoprovaconsole': ?>
                    <div class="gb-block-notice bancoprova">
                        <div class="gb-notice-title bancoprova">
                            <p><?php esc_html_e( 'Disclaimer (per un mondo più pulito)', 'wp-gwcustomboxes' ); ?></p>
                        </div>
                        <div class="gb-notice-text">
                            <p><i class="fab fa-xbox fa-pull-right fa-7x"></i> <?php esc_html_e( 'Articoli "Banco Prova Console" con pro e contro.', 'wp-gwcustomboxes' ); ?> <a href="<?php echo esc_url( network_site_url( '/tag/banco-prova-console' ) ); ?>"><?php esc_html_e( 'fai clic qui', 'wp-gwcustomboxes' ); ?></a>.
                                <?php if ( $state ) : ?>
                                    <br><strong><?php esc_html_e( 'Prodotto:', 'wp-gwcustomboxes' ); ?></strong> <em><?php echo esc_html( $state ); ?></em>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php break;

                case 'androidcorner': ?>
                    <div class="gb-block-notice android">
                        <div class="gb-notice-title android">
                            <p><?php esc_html_e( "Android's Corner", 'wp-gwcustomboxes' ); ?></p>
                        </div>
                        <div class="gb-notice-text android">
                            <p><i class="fab fa-android fa-pull-right fa-7x"></i> <?php esc_html_e( "Articoli sull'esperienza Android.", 'wp-gwcustomboxes' ); ?> <a href="<?php echo esc_url( network_site_url( '/tag/android-corner' ) ); ?>"><?php esc_html_e( 'fai clic qui', 'wp-gwcustomboxes' ); ?></a>.</p>
                        </div>
                    </div>
                <?php break;

                case 'mrl': ?>
                    <div class="gb-block-notice mrl">
                        <div class="gb-notice-title mrl">
                            <p><?php esc_html_e( 'Milano Real Life (MRL)', 'wp-gwcustomboxes' ); ?></p>
                        </div>
                        <div class="gb-notice-text mrl">
                            <img class="alignright" style="max-width:100px;" src="<?php echo esc_url( network_site_url( '/wp-content/uploads/MilanoRealLife.png' ) ); ?>" alt="MRL" title="MRL" />
                            <p><?php esc_html_e( 'Articoli sulla vita a Milano.', 'wp-gwcustomboxes' ); ?> <a href="<?php echo esc_url( network_site_url( '/tag/mrl' ) ); ?>"><?php esc_html_e( 'fai clic qui', 'wp-gwcustomboxes' ); ?></a>.</p>
                        </div>
                    </div>
                <?php break;

                case 'pillole': ?>
                    <div class="gb-block-notice pillole">
                        <div class="gb-notice-title pillole">
                            <p><?php esc_html_e( 'Pillole', 'wp-gwcustomboxes' ); ?></p>
                        </div>
                        <div class="gb-notice-text pillole">
                            <p><i class="fas fa-bolt fa-pull-right fa-5x"></i> <?php esc_html_e( 'Articoli rapidi e pratici.', 'wp-gwcustomboxes' ); ?> <a href="<?php echo esc_url( network_site_url( '/tag/pillole' ) ); ?>"><?php esc_html_e( 'fai clic qui', 'wp-gwcustomboxes' ); ?></a>.</p>
                        </div>
                    </div>
                <?php break;

                case 'pstartmilano': ?>
                    <img src="<?php echo esc_url( network_site_url( '/wp-content/uploads/PressStart-GioxxsWall.png' ) ); ?>" alt="Press Start Milano" style="padding-bottom:15px;" />
                <?php break;

                case 'sponsored': ?>
                    <div class="gb-block-notice sponsored">
                        <div class="gb-notice-title sponsored">
                            <p><?php esc_html_e( 'Sponsored', 'wp-gwcustomboxes' ); ?></p>
                        </div>
                        <div class="gb-notice-text sponsored">
                            <p><i class="fas fa-file-invoice-dollar fa-pull-right fa-7x"></i> <?php esc_html_e( 'Articolo sponsorizzato, giudizio imparziale.', 'wp-gwcustomboxes' ); ?>
                                <?php if ( $state ) : ?>
                                    <br><strong><?php esc_html_e( 'Tipo di sponsorizzazione:', 'wp-gwcustomboxes' ); ?></strong> <em><?php echo esc_html( $state ); ?></em>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php break;

                default:
                    // no box
            endswitch;

            return ob_get_clean();
        }
    }

    new GWCustomBoxes();
}
