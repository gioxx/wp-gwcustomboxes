<?php
/**
 * Plugin Name:         Custom Boxes per Gioxx's Wall
 * Plugin URI:          https://github.com/gioxx/wp-gwcustomboxes
 * Description:         Box personalizzati per gli articoli di Gioxx's Wall, ora aggiornabili tramite Git Updater.
 * Version:             0.31
 * Author:              Gioxx
 * Author URI:          https://gioxx.org
 * License:             GPL3
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
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
            add_filter( 'the_content', array( $this, 'filter_content' ) );
        }

        /**
         * Enqueue front-end styles
         */
        public function enqueue_styles() {
            wp_enqueue_style(
                'gwcustomboxes-styles',
                plugin_dir_url( __FILE__ ) . 'css/plg_customboxes.css',
                array(),
                '0.31'
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
            if ( is_singular( 'post' ) && !is_front_page() ) {
                $published = strtotime( $post->post_date_gmt );
                if ( $published && ( time() - $published ) > ( 5 * MONTH_IN_SECONDS ) ) {
                    $content .= $this->get_alert_box();
                }
            }

            // Possible box tags
            $boxes = array(
                'android-corner'        => 'androidcorner',
                'banco-prova-baby'      => 'bancoprovababy',
                'banco-prova-console'   => 'bancoprovaconsole',
                'banco-prova'           => 'bancoprova',
                'mrl'                   => 'mrl',
                'piccoli-passi'         => 'piccolipassi',
                'pillole'               => 'pillole',
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
                    <p class="timealert">L'articolo potrebbe non essere aggiornato</p>
                </div>
                <div class="gb-notice-text timealert">
                    <p class="timealert">
                        <?php printf(
                            esc_html( 'Questo post è stato scritto più di %d mesi fa, potrebbe non essere aggiornato. Per qualsiasi dubbio lascia un commento!' ),
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
                case 'androidcorner': ?>
                    <div class="gb-block-notice android">
                        <div class="gb-notice-title android">
                            <p>Android's Corner</p>
                        </div>
                        <div class="gb-notice-text android">
                            <p>
                                <i class="fab fa-android fa-pull-right fa-7x"></i> Android's Corner &egrave; il nome di una raccolta di articoli pubblicati su questi lidi che raccontano l'esperienza Android, consigli, applicazioni, novit&agrave; e qualsiasi altra cosa possa ruotare intorno al mondo del sistema operativo mobile di Google e sulla quale ho avuto possibilit&agrave; di mettere mano, di ritoccare, di far funzionare, una scusa come un'altra per darti una mano e scambiare opinioni insieme :-)<br/>
                                Se vuoi leggere gli altri articoli dedicati ad Android <a href="<?php echo esc_url( network_site_url( '/tag/android-corner' ) ); ?>">fai clic qui</a>.
                            </p>
                        </div>
                    </div>
                <?php break;

                case 'bancoprovababy': ?>
                    <div class="gb-block-notice bancoprova">
                        <div class="gb-notice-title bancoprova">
                            <p>Disclaimer (per un mondo pi&ugrave; pulito)</p>
                        </div>
                        <div class="gb-notice-text">
                            <p>
                                <i class="fa-solid fa-child fa-pull-right fa-7x"></i> Gli articoli che appartengono al tag &quot;<strong>Banco Prova Baby</strong>&quot; raccontano la mia personale esperienza con prodotti generalmente forniti da chi li realizza. In alcuni casi il prodotto descritto rimane a me, in altri viene restituito. In altri casi ancora sono io ad acquistarlo e decidere di pubblicare un articolo in seguito, solo per il piacere di farlo e di condividere con te le mie opinioni.
                            </p>
                            <p>
                                Ogni articolo rispetta - <strong>come sempre</strong> - i miei standard: <strong>nessuna marchetta</strong>, solo il mio parere, riporto i fatti, a prescindere dal giudizio finale.<br/>
                                Se vuoi leggere le altre recensioni del Banco Prova Baby <a href="<?php echo esc_url( network_site_url( '/tag/banco-prova-baby' ) ); ?>">fai clic qui</a>.
                            </p>
                            <?php if ( $state ) : ?>
                                <p>
                                    <strong>Prodotto</strong>: <em><?php echo esc_html( $state ); ?></em>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php break;

                case 'bancoprovaconsole': ?>
                    <div class="gb-block-notice bancoprova">
                        <div class="gb-notice-title bancoprova">
                            <p>Disclaimer (per un mondo pi&ugrave; pulito)</p>
                        </div>
                        <div class="gb-notice-text">
                            <p>
                                <i class="fab fa-xbox fa-pull-right fa-7x"></i> Gli articoli che appartengono al tag &quot;<strong>Banco Prova Console</strong>&quot; raccontano la mia personale esperienza con prodotti generalmente forniti da chi li realizza. In alcuni casi il prodotto descritto rimane a me, in altri viene restituito. In altri casi ancora sono io ad acquistarlo e decidere di pubblicare un articolo in seguito, solo per il piacere di farlo e di condividere con te le mie opinioni.
                            </p>
                            <p>
                                Ogni articolo rispetta - <strong>come sempre</strong> - i miei standard: <strong>nessuna marchetta</strong>, solo il mio parere, riporto i fatti, a prescindere dal giudizio finale.<br />
                                Se vuoi leggere le altre recensioni del Banco Prova Console <a href="<?php echo esc_url( network_site_url( '/tag/banco-prova-console' ) ); ?>">fai clic qui</a>.
                            </p>
                            <?php if ( $state ) : ?>
                                <p>
                                    <strong>Prodotto</strong>: <em><?php echo esc_html( $state ); ?></em>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php break;

                case 'bancoprova': ?>
                    <div class="gb-block-notice bancoprova">
                        <div class="gb-notice-title bancoprova">
                            <p>Disclaimer (per un mondo pi&ugrave; pulito)</p>
                        </div>
                        <div class="gb-notice-text">
                            <p>
                                <i class="fab fa-grav fa-pull-right fa-7x"></i> Gli articoli che appartengono al tag &quot;<strong>Banco Prova</strong>&quot; raccontano la mia personale esperienza con prodotti generalmente forniti da chi li realizza. In alcuni casi il prodotto descritto rimane a me, in altri viene restituito. In altri casi ancora sono io ad acquistarlo e decidere di pubblicare un articolo in seguito solo per il piacere di farlo e di condividere con te le mie opinioni.
                            </p>
                            <p>
                                Ogni articolo rispetta - <strong>come sempre</strong> - i miei standard: <strong>nessuna marchetta</strong>, solo il mio parere. Riporto i fatti a prescindere dal giudizio finale.<br/>
                                Se vuoi leggere le altre recensioni del Banco Prova <a href="<?php echo esc_url( network_site_url( '/tag/banco-prova' ) ); ?>">fai clic qui</a>.
                            </p>
                            <?php if ( $state ) : ?>
                                <p>
                                    <strong>Prodotto</strong>: <em><?php echo esc_html( $state ); ?></em>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php break;

                case 'mrl': ?>
                    <div class="gb-block-notice mrl">
                        <div class="gb-notice-title mrl">
                            <p>Milano Real Life (MRL)</p>
                        </div>
                        <div class="gb-notice-text mrl">
                            <img class="alignright" style="max-width:100px;" src="<?php echo esc_url( network_site_url( '/wp-content/uploads/MilanoRealLife.png' ) ); ?>" alt="MRL" title="MRL" />
                            <p>
                                &Egrave; il nome di una raccolta di articoli pubblicati sul mio blog, raccontano la vita di un &quot;perfetto nessuno&quot; che ha deciso di spostare abitudini e quotidianit&agrave; in una differente città rispetto a quella di origine.<br/>
                                Alla scoperta della caotica capitale lombarda mai tanto amata e odiata allo stesso tempo, per chi &egrave; nato qui e ancora oggi continua a viverci per volere o necessit&agrave;, per le centinaia di persone che invece vengono da fuori e vedono Milano come una piacevole alternativa o una costrizione imposta dalla propria vita lavorativa.<br />
                                La rubrica &quot;leggera&quot; di approfondimento alla quale però non fare l'abitudine, non siamo mica così affidabili da queste parti!
                            </p>
                            <p>
                                Se vuoi leggere gli altri articoli dedicati alla &quot;vita milanese&quot; <a href="<?php echo esc_url( network_site_url( '/tag/mrl' ) ); ?>">fai clic qui</a>.
                            </p>
                        </div>
                    </div>
                <?php break;

                case 'piccolipassi': ?>
                    <div class="gb-block-notice piccolipassi">
                        <div class="gb-notice-title piccolipassi">
                            <p>A piccoli passi</p>
                        </div>
                        <div class="gb-notice-text piccolipassi">
                            <img class="alignright" style="max-height: 120px;" title="A piccoli passi" alt="A piccoli passi" src="<?php echo esc_url( network_site_url( '/wp-content/uploads/2019/03/logo.png' ) ); ?>" />
                            <p>
                                A <em>piccoli passi</em> &egrave; una serie di articoli dedicata a chi non &egrave; solito districarsi tra termini tecnici e procedure troppo complesse. Righe di testo di facile comprensione corredate di immagini, semplici procedure che tutti possono imparare e mettere subito in pratica.<br />
                                Eredit&agrave; di un vecchio esperimento nel frattempo abbandonato e chiuso, ma con uno storico che non intendo perdere e che preferisco pubblicare nel corso del fine settimana.<br />
                                Se vuoi leggere gli altri &quot;<em>Piccoli passi</em>&quot; <a href="<?php echo esc_url( network_site_url( '/tag/piccoli-passi' ) ); ?>">fai clic qui</a>.
                            </p>
                        </div>
                    </div>
                <?php break;

                case 'pillole': ?>
                    <div class="gb-block-notice pillole">
                        <div class="gb-notice-title pillole">
                            <p>Pillole</p>
                        </div>
                        <div class="gb-notice-text pillole">
                            <p>
                                <i class="fas fa-bolt fa-pull-right fa-5x"></i> Le pillole sono articoli di veloce lettura dedicati a notizie, script o qualsiasi altra cosa possa essere &quot;divorata e messa in pratica&quot; con poco. Uno spazio del blog riservato agli articoli &quot;a bruciapelo&quot;!<br />
                                Se vuoi leggere le altre pillole <a href="<?php echo esc_url( network_site_url( '/tag/pillole' ) ); ?>">fai clic qui</a>.
                            </p>
                        </div>
                    </div>
                <?php break;

                case 'sponsored': ?>
                    <div class="gb-block-notice sponsored">
                        <div class="gb-notice-title sponsored">
                            <p><?php echo esc_html( 'Sponsored' ); ?></p>
                        </div>
                        <div class="gb-notice-text sponsored">
                            <p>
                                <i class="fas fa-file-invoice-dollar fa-pull-right fa-7x"></i> La regia si prende una piccola pausa e ti lascia ai consigli per gli acquisti, articoli scritti sempre e comunque dal proprietario della baracca (o da ospiti di vecchia data) ma - contrariamente al solito - sponsorizzati.<br />
                                Il giudizio &egrave; e sar&agrave; sempre imparziale come il resto delle pubblicazioni.<br />
                                D'accordo pagare le spese di questo blog ma mai vendere giudizi positivi se non meritati. Nel caso in cui venga richiesta esplicita modifica dell'articolo e/o del giudizio sarà mia cura rimanere quanto più neutrale possibile.
                            </p>
                            <?php if ( $state ) : ?>
                                <p>
                                    <strong>Tipo di sponsorizzazione</strong>: <em><?php echo esc_html( $state ); ?></em>
                                </p>
                            <?php endif; ?>
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
