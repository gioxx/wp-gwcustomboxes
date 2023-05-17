<?php

/*
	Plugin Name: Custom Boxes for Gioxx's Wall
	Plugin URI: https://github.com/gioxx/wp-gwcustomboxes
	Description: Box personalizzati per gli articoli di Gioxx's Wall.
	Author: Gioxx
	Version: 0.19
	Author URI: https://gioxx.org
	License: GPL3
*/

defined( 'ABSPATH' ) || exit;

/*	Registro sorgente aggiornamento plugin e pagina dettaglio in Plugin Install
	Credits: https://rudrastyh.com/wordpress/self-hosted-plugin-update.html
*/
if ( !class_exists('plgUpdateChecker') ) {
	class plgUpdateChecker{
		public $plugin_slug;
		public $version;
		public $cache_key;
		public $cache_allowed;

		public function __construct() {
			$this->plugin_slug = plugin_basename( __DIR__ );
			$this->version = '1.0';
			$this->cache_key = 'customboxes_updater';
			$this->cache_allowed = false;

			add_filter( 'plugins_api', array( $this, 'info' ), 20, 3 );
			add_filter( 'site_transient_update_plugins', array( $this, 'update' ) );
			add_action( 'upgrader_process_complete', array( $this, 'purge' ), 10, 2 );
		}

		public function request() {
			$remote = get_transient( $this->cache_key );
			if( false === $remote || ! $this->cache_allowed ) {
				$remote = wp_remote_get(
					'https://gioxx.github.io/wp-gwcustomboxes/plg-customboxes.json',
					array(
						'timeout' => 10,
						'headers' => array(
							'Accept' => 'application/json'
						)
					)
				);

				if(
					is_wp_error( $remote )
					|| 200 !== wp_remote_retrieve_response_code( $remote )
					|| empty( wp_remote_retrieve_body( $remote ) )
				) {
					return false;
				}

				set_transient( $this->cache_key, $remote, DAY_IN_SECONDS );

			}
			$remote = json_decode( wp_remote_retrieve_body( $remote ) );
			return $remote;
		}


		function info( $res, $action, $args ) {
			// print_r( $action );
			// print_r( $args );

			// do nothing if you're not getting plugin information right now
			if( 'plugin_information' !== $action ) {
				return $res;
			}

			// do nothing if it is not our plugin
			if( $this->plugin_slug !== $args->slug ) {
				return $res;
			}

			// get updates
			$remote = $this->request();

			if( ! $remote ) {
				return $res;
			}

			$res = new stdClass();
			$res->name = $remote->name;
			$res->slug = $remote->slug;
			$res->version = $remote->version;
			$res->tested = $remote->tested;
			$res->requires = $remote->requires;
			$res->author = $remote->author;
			$res->author_profile = $remote->author_profile;
			$res->download_link = $remote->download_url;
			$res->trunk = $remote->download_url;
			$res->requires_php = $remote->requires_php;
			$res->last_updated = $remote->last_updated;
			$res->sections = array(
				'description' => $remote->sections->description,
				'installation' => $remote->sections->installation,
				'changelog' => $remote->sections->changelog
			);

			if( ! empty( $remote->banners ) ) {
				$res->banners = array(
					'low' => $remote->banners->low,
					'high' => $remote->banners->high
				);
			}

			return $res;
		}

		public function update( $transient ) {
			if ( empty($transient->checked ) ) {
				return $transient;
			}
			$remote = $this->request();

			if(
				$remote
				&& version_compare( $this->version, $remote->version, '<' )
				&& version_compare( $remote->requires, get_bloginfo( 'version' ), '<=' )
				&& version_compare( $remote->requires_php, PHP_VERSION, '<' )
			) {
				$res = new stdClass();
				$res->slug = $this->plugin_slug;
				$res->plugin = plugin_basename( __FILE__ ); // example: misha-update-plugin/misha-update-plugin.php
				$res->new_version = $remote->version;
				$res->tested = $remote->tested;
				$res->package = $remote->download_url;

				$transient->response[ $res->plugin ] = $res;
	    	}
			return $transient;
		}

		public function purge( $upgrader, $options ) {
			if (
				$this->cache_allowed
				&& 'update' === $options['action']
				&& 'plugin' === $options[ 'type' ]
			) {
				// just clean the cache when new plugin version is installed
				delete_transient( $this->cache_key );
			}
		}

	}
	new plgUpdateChecker();
}

add_filter( 'plugin_row_meta', function( $links_array, $plugin_file_name, $plugin_data, $status ) {
	if( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {
		$links_array[] = sprintf(
			'<a href="%s" class="thickbox open-plugin-details-modal">%s</a>',
			add_query_arg(
				array(
					'tab' => 'plugin-information',
					'plugin' => plugin_basename( __DIR__ ),
					'TB_iframe' => true,
					'width' => 772,
					'height' => 788
				),
				admin_url( 'plugin-install.php' )
			),
			__( 'View details' )
		);
	}
	return $links_array;
}, 25, 4 );

/*	Registro foglio di stile dei box personalizzati
	Credits: https://stackoverflow.com/questions/21759642/wordpress-load-a-stylesheet-through-plugin
*/
function CustomBoxesCSSLoad() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'CustomBoxesProd', $plugin_url . 'css/plg_customboxes.css' );
    //wp_enqueue_style( 'CustomBoxesDev', $plugin_url . 'css/plg_customboxes_xperiments.css' );
}
add_action( 'wp_enqueue_scripts', 'CustomBoxesCSSLoad' );

/* Elenco dei box (Switch $boxselection)
*/
function htmlContent($boxselection) {
	switch ($boxselection):
		case "pillole":
			$boxcontent =  '<div class="gb-block-notice pillole">';
			$boxcontent .= '	<div class="gb-notice-title pillole">';
			$boxcontent .= '		<p>Pillole</p>';
			$boxcontent .= '	</div>';
			$boxcontent .= '	<div class="gb-notice-text pillole">';
			$boxcontent .= '		<p>';
			$boxcontent .= '			<i class="fas fa-bolt fa-pull-right fa-5x" style="padding-right: 10px;"></i> Le pillole sono articoli di veloce lettura dedicati a notizie, script o qualsiasi altra cosa possa essere "<em>divorata e messa in pratica</em>" con poco. Uno spazio del blog riservato agli articoli "<strong><em>a bruciapelo</em></strong>"!<br />
				Se vuoi leggere le altre pillole <a href="' . network_site_url('/') . 'tag/pillole">fai clic qui</a>.';
			$boxcontent .= '		</p>';
			$boxcontent .= '	</div>';
			$boxcontent .= '</div>';
			break;
		case "pstartmilano":
			$boxcontent = '<img src="https://gioxx.org/wp-content/uploads/PressStart-GioxxsWall.png" style="padding-bottom: 15px;" />';
			break;
		default:
			$boxcontent = '';
	endswitch;
	return $boxcontent;
}

add_filter ('the_content', 'customboxes');
function customboxes($content) {
	# A piccoli passi - Gli articoli per chi deve ancora imparare
		$piccolipassi =  '<div class="gb-block-notice piccolipassi">';
		$piccolipassi .= '<div class="gb-notice-title piccolipassi"><p>A piccoli passi</p></div>';
		$piccolipassi .= '<div class="gb-notice-text piccolipassi"><img class="alignright" style="border: 0px none; max-height: 120px;" title="A piccoli passi, la serie di articoli dedicata a chi muove ancora i primi passi nel mondo della tecnologia" alt="A piccoli passi, la serie di articoli dedicata a chi muove ancora i primi passi nel mondo della tecnologia" src="https://gioxx.org/wp-content/uploads/2019/03/logo.png" />';
		$piccolipassi .= '<p><em>A piccoli passi</em> &egrave; una serie di articoli dedicata a chi non &egrave; solito districarsi tra termini tecnici e procedure troppo complesse. Righe di testo di facile comprensione corredate di immagini, semplici procedure che tutti possono imparare e mettere subito in pratica.<br />';
		$piccolipassi .= 'Eredit&agrave; di un vecchio esperimento nel frattempo abbandonato e chiuso, ma con uno storico che non intendo perdere e che preferisco pubblicare nel corso del fine settimana.<br />';
		$piccolipassi .= 'Se vuoi leggere gli altri "<em>Piccoli passi</em>" <a href="'.network_site_url('/').'tag/piccoli-passi">fai clic qui</a>.</p>';
		$piccolipassi .= '</div></div>';

	# Milano Real Life (MRL e Press Start)
	$mrl= '<div class="gb-block-notice mrl">
			<div class="gb-notice-title mrl"><p><strong>M</strong>ilano <strong>R</strong>eal <strong>L</strong>ife (<strong>MRL</strong>)</p></div>
			<div class="gb-notice-text mrl"><img class="alignright" style="border: 0px none; max-width: 100px;" title="MRL: Milano Real Life" alt="MRL: Milano Real Life" src="https://gioxx.org/wp-content/uploads/MilanoRealLife.png" />
				<p>&Egrave; il nome di una raccolta di articoli pubblicati sul mio blog, raccontano la vita di un &quot;<em>perfetto nessuno</em>&quot; che ha deciso di spostare abitudini e quotidianit&agrave; in una differente citt&agrave; rispetto a quella di origine.<br />
				Alla scoperta della caotica capitale lombarda mai tanto amata e odiata allo stesso tempo, per chi &egrave; nato qui e ancora oggi continua a viverci per volere o necessit&agrave;, per le centinaia di persone che invece vengono da fuori e vedono Milano come una piacevole alternativa o una costrizione imposta dalla propria vita lavorativa.<br />
				La rubrica &quot;<em>leggera</em>&quot; di approfondimento alla quale per&ograve; non fare l&#39abitudine, <em>non siamo mica cos&igrave; affidabili</em> da queste parti!<br />
				Se vuoi leggere gli altri articoli dedicati alla "<em>vita milanese</em>" <a href="'.network_site_url('/').'tag/mrl">fai clic qui</a>.</p></div>
		</div>';

	# Android's Corner
	$androidcorner= '<div class="gb-block-notice android">
		<div class="gb-notice-title android"><p>Android&#39s Corner</p></div>
		<div class="gb-notice-text android"><p><i class="fab fa-android fa-pull-right fa-7x" style="padding-right: 10px;"></i> <strong>Android</strong>&#39s Corner &egrave; il nome di una raccolta di articoli pubblicati <em>su questi lidi</em> che raccontano l&#39esperienza Android, consigli, applicazioni, novit&agrave; e qualsiasi altra cosa possa ruotare intorno al mondo del sistema operativo mobile di Google e sulla quale ho avuto possibilit&agrave; di mettere mano, di ritoccare, di far funzionare, una scusa come un&#39altra per darvi una mano e scambiare opinioni insieme :-)<br />
			Se vuoi leggere gli altri articoli dedicati ad Android <a href="'.network_site_url('/').'tag/android-corner">fai clic qui</a>.</p></div>
		</div>';

	# Progetto sponsorizzato (Categoria: Sponsored Project)
	global $wp_query;
	$logosponsor= get_post_meta(get_the_ID(), 'logosponsor', true);
	$nomesponsor= get_post_meta(get_the_ID(), 'nomesponsor', true);
	$descsponsor= get_post_meta(get_the_ID(), 'descsponsor', true);
	$hashsponsor= get_post_meta(get_the_ID(), 'hashsponsor', true);
		#Blocco sponsor
		$bloccosponsor= '<div style="border: #000000 solid 1px; padding-top: 10px; padding-bottom: 7px; padding-left: 10px; padding-right: 10px; background-color: #FFFFFF; margin-bottom: 10px; min-height: 100px;">';
		if (!empty($logosponsor)) { $bloccosponsor=$bloccosponsor.'<img class="alignright" style="border: 0px none; max-width: 250px;" title="Sponsor" alt="Sponsor" src="'.$logosponsor.'" />'; }
		if (!empty($nomesponsor)) { $bloccosponsor=$bloccosponsor.'Questo articolo nasce da un progetto in collaborazione con <strong>'.$nomesponsor.'</strong>.'; }
		if (!empty($hashsponsor)) { $bloccosponsor=$bloccosponsor.'Puoi seguire tutti gli sviluppi attraverso il tag &#35;<a href="'.network_site_url('/').'tag/'.$hashsponsor.'">'.$hashsponsor.'</a>.'; }
		if (!empty($descsponsor)) { $bloccosponsor=$bloccosponsor.'<br /><br />Di cosa si parla, in breve: <br /><em>'.$descsponsor.'</em>'; }
		$bloccosponsor=$bloccosponsor.'</div>';
	wp_reset_query();

	# Banco Prova / Console / Baby
	global $wp_query;
	$statoprodotto= get_post_meta(get_the_ID(), 'statoprodotto', true);
		#Blocco prodotto
		$bloccoprodotto= '<div class="gb-block-notice">
			<div class="gb-notice-title bancoprova"><p>Disclaimer (<em>per un mondo pi&ugrave; pulito</em>)</p></div>
			<div class="gb-notice-text"><p><i class="fab fa-grav fa-pull-right fa-7x" style="padding-right: 10px;"></i> Gli articoli che appartengono al tag &quot;<strong>Banco Prova</strong>&quot; raccontano la mia personale esperienza con prodotti generalmente forniti da chi li realizza. In alcuni casi il prodotto descritto rimane a me, in altri viene restituito. In altri casi ancora sono io ad acquistarlo e decidere di pubblicare un articolo in seguito, solo per il piacere di farlo e di condividere con te le mie opinioni.
			<br />Ogni articolo rispetta -<strong><em>come sempre</em></strong>- i miei standard: <strong>nessuna marchetta</strong>, solo il mio parere, riporto i fatti, a prescindere dal giudizio finale.<br />
			Se vuoi leggere le altre recensioni del Banco Prova <a href="'.network_site_url('/').'tag/banco-prova">fai clic qui</a>.';
		if (!empty($statoprodotto)) { $bloccoprodotto=$bloccoprodotto.'<br /><br /><strong>Prodotto</strong>: <em>'.$statoprodotto.'</em>'; }
		$bloccoprodotto=$bloccoprodotto.'</p></div></div>';

    #Blocco prodotto Console
		$bloccoprodotto_console= '<div class="gb-block-notice">
			<div class="gb-notice-title bancoprova"><p>Disclaimer (<em>per un mondo pi&ugrave; pulito</em>)</p></div>
			<div class="gb-notice-text"><p><i class="fa-solid fa-ghost fa-pull-right fa-7x" style="padding-right: 10px;"></i> Gli articoli che appartengono al tag &quot;<strong>Banco Prova Console</strong>&quot; raccontano la mia personale esperienza con prodotti generalmente forniti da chi li realizza. In alcuni casi il prodotto descritto rimane a me, in altri viene restituito. In altri casi ancora sono io ad acquistarlo e decidere di pubblicare un articolo in seguito, solo per il piacere di farlo e di condividere con te le mie opinioni.
			<br />Ogni articolo rispetta -<strong><em>come sempre</em></strong>- i miei standard: <strong>nessuna marchetta</strong>, solo il mio parere, riporto i fatti, a prescindere dal giudizio finale.<br />
			Se vuoi leggere le altre recensioni del Banco Prova Console <a href="'.network_site_url('/').'tag/banco-prova-console">fai clic qui</a>.';
		if (!empty($statoprodotto)) { $bloccoprodotto_console=$bloccoprodotto_console.'<br /><br /><strong>Prodotto</strong>: <em>'.$statoprodotto.'</em>'; }
		$bloccoprodotto_console=$bloccoprodotto_console.'</p></div></div>';

    #Blocco prodotto Baby
		$bloccoprodotto_baby= '<div class="gb-block-notice">
			<div class="gb-notice-title bancoprova"><p>Disclaimer (<em>per un mondo pi&ugrave; pulito</em>)</p></div>
			<div class="gb-notice-text"><p><i class="fa-solid fa-child fa-pull-right fa-7x" style="padding-right: 10px;"></i> Gli articoli che appartengono al tag &quot;<strong>Banco Prova Baby</strong>&quot; raccontano la mia personale esperienza con prodotti generalmente forniti da chi li realizza. In alcuni casi il prodotto descritto rimane a me, in altri viene restituito. In altri casi ancora sono io ad acquistarlo e decidere di pubblicare un articolo in seguito, solo per il piacere di farlo e di condividere con te le mie opinioni.
			<br />Ogni articolo rispetta -<strong><em>come sempre</em></strong>- i miei standard: <strong>nessuna marchetta</strong>, solo il mio parere, riporto i fatti, a prescindere dal giudizio finale.<br />
			Se vuoi leggere le altre recensioni del Banco Prova <a href="'.network_site_url('/').'tag/banco-prova-baby">fai clic qui</a>.';
		if (!empty($statoprodotto)) { $bloccoprodotto_baby=$bloccoprodotto_baby.'<br /><br /><strong>Prodotto</strong>: <em>'.$statoprodotto.'</em>'; }
		$bloccoprodotto_baby=$bloccoprodotto_baby.'</p></div></div>';
	wp_reset_query();

	# Consigli per gli acquisti (Sponsored) - Sopravvivenza del blog
	$economysurvive= '<div class="gb-block-notice sponsored">
			<div class="gb-notice-title sponsored"><p>Sponsored</p></div>
			<div class="gb-notice-text sponsored"><p><i class="fas fa-file-invoice-dollar fa-pull-right fa-7x" style="padding-right: 10px;"></i> La regia si prende un piccolo break e ti lascia ai consigli per gli acquisti, articoli scritti sempre e comunque dal proprietario della baracca (o da ospiti di vecchia data) ma - <em>contrariamente al solito</em> - <strong>sponsorizzati</strong>.<br />
				Il giudizio &egrave; e sar&agrave; sempre imparziale come il resto delle pubblicazioni. D\'accordo pagare le spese di questo blog, ma mai vendere giudizi positivi se non meritati. Nel caso in cui venga richiesta esplicita modifica dell\'articolo e/o del giudizio sar&agrave; mia cura rimanere quanto pi&ugrave; neutrale possibile.</p></div>
		</div>';


	if ( is_single() ) {
		/*	"Old Post" (idea e codice originale di Francesco Fullone)
			Se la data di pubblicazione supera i 5 mesi (predefinito) allora inserisco un box che avvisa l'utente riguardo la possibilità che i contenuti non siano aggiornati, invitandolo ad utilizzare l'area commenti.
		*/
		$oldpost = 5; // modificare per aumentare o diminuire l'intervallo di tempo
		$alertpubblicazione = 	'<div class="gb-block-notice timealert">';
		$alertpubblicazione .= 	'<div class="gb-notice-title"><p class="timealert">L\'articolo potrebbe non essere aggiornato</p></div>';
		$alertpubblicazione .=	'<div class="gb-notice-text timealert"><p class="timealert">Questo post &egrave; stato scritto pi&ugrave; di '. $oldpost .' mesi fa, potrebbe non essere aggiornato. Per qualsiasi dubbio ti invito a lasciare un commento per chiedere ulteriori informazioni! :-)</p></div>';
		$alertpubblicazione .=	'</div>';
		if ( strtotime(get_the_time('y-m-d')) <= mktime(0,0,0,date('m')-$oldpost) ) {
			$content .= $alertpubblicazione;
		}
	
		if ( has_tag('mrl') ) { $content .= $mrl; } // MRL - Milano Real Life
		if ( has_tag('press-start-milano') ) { $content .= $pstartmilano; } // Press Start: Milano
		if ( has_tag('pillole') ) { $content .= htmlContent('pillole'); } // Pillole
		if ( has_tag('piccoli-passi') ) { $content .= $piccolipassi; } // Piccoli Passi
		if ( has_category('sponsored') ) { $content .= $economysurvive; } // Consigli per gli acquisti (Sponsored)
		if ( has_tag('android-corner') ) { $content .= $androidcorner; } // Android's Corner
		if ( has_tag('sponsored-project') ) { $content .= $bloccosponsor; } // Progetto sponsorizzato
		if ( has_tag('banco-prova') ) { $content .= $bloccoprodotto; } // Banco Prova: stato prodotto
		if ( has_tag('banco-prova-console') ) { $content .= $bloccoprodotto_console; } // Banco Prova Console: stato prodotto
    	if ( has_tag('banco-prova-baby') ) { $content .= $bloccoprodotto_baby; } // Banco Prova Baby: stato prodotto
	}

	// Home Page
	if ( is_home() ) {
		if ( has_tag('mrl') ) { $content .= $mrl; } // MRL - Milano Real Life
		if ( has_tag('press-start-milano') ) { $content .= $pstartmilano; } // Press Start: Milano
		if ( has_tag('pillole') ) { $content .= htmlContent('pillole'); } // Pillole
		if ( has_tag('piccoli-passi') ) { $content .= $piccolipassi; } // Piccoli Passi
		if ( has_category('sponsored') ) { $content .= $economysurvive; } // Consigli per gli acquisti (Sponsored)
		if ( has_tag('android-corner') ) { $content .= $androidcorner; } // Android's Corner
		if ( has_tag('sponsored-project') ) { $content .= $bloccosponsor; } // Progetto sponsorizzato
		if ( has_tag('banco-prova') ) { $content .= $bloccoprodotto; } // Banco Prova: stato prodotto
		if ( has_tag('banco-prova-console') ) { $content .= $bloccoprodotto_console; } // Banco Prova Console: stato prodotto
    	if ( has_tag('banco-prova-baby') ) { $content .= $bloccoprodotto_baby; } // Banco Prova Baby: stato prodotto
	}

	// Archivi
	if ( is_archive() ) {
		if (has_tag('mrl')) { $content.= $mrl; } // MRL - Milano Real Life
		if (has_tag('press-start-milano')) { $content.= $pstartmilano; } // Press Start: Milano
		if (has_tag('pillole')) { $content.= htmlContent('pillole'); } // Pillole
		if (has_tag('piccoli-passi')) { $content.= $piccolipassi; } // Piccoli Passi
		if (has_category('sponsored')) { $content.= $economysurvive; } // Consigli per gli acquisti (Sponsored)
		if (has_tag('android-corner')) { $content.= $androidcorner; } // Android's Corner
		if (has_tag('sponsored-project')) { $content.= $bloccosponsor; } // Progetto sponsorizzato
		if (has_tag('banco-prova')) { $content.= $bloccoprodotto; } // Banco Prova: stato prodotto
		if (has_tag('banco-prova-console')) { $content.= $bloccoprodotto_console; } // Banco Prova Console: stato prodotto
		if (has_tag('banco-prova-baby')) { $content.= $bloccoprodotto_baby; } // Banco Prova Baby: stato prodotto
	}

   return $content;
}