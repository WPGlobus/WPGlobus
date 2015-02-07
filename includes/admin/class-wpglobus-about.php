<?php
/**
 * @package   WPGlobus/Admin
 * @copyright Alex Gor (alexgff) and Gregory Karpinsky (tivnet)
 */

/**
 * Class WPGlobus_About
 */
class WPGlobus_About {

	/**
	 * Constructor
	 */
	function __construct() {
		$this->about_screen();
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		?>
		<div class="wrap">

			<h2><?php
				/**
				 * @quirk
				 * This should be H2, so that it goes above the WP admin notices
				 */
				echo __( 'About WPGlobus', 'wpglobus' );
				?></h2>

			<div class="wpglobus-about-wrap about-wrap">
				<div class="changelog">
					<div class="feature-main feature-section col three-col">

						<div>
							<h4><?php printf( __( 'Version %s', 'wpglobus' ), WPGLOBUS_VERSION ); ?></h4>
							<img
								src="<?php echo WPGlobus::$PLUGIN_DIR_URL . 'includes/css/images/wpglobus-logo-180x180.png'; ?>"
								alt="WPGlobus logo"/>
						</div>

						<div>
							<h4><?php _e( 'What is WPGlobus', 'wpglobus' ); ?></h4>

							<p><?php _e( 'WPGlobus is a globalization (multi-lingual, internationalization, localization, ...) WordPress plugin.', 'wpglobus' ); ?></p>

							<p><?php _e( 'Our goal is to let WordPress support multiple languages, countries and currencies (for e-commerce).', 'wpglobus' ); ?></p>

							<p><?php printf( __( 'For more information, please visit %s.', 'wpglobus' ), '<a href="http://www.wpglobus.com">WPGlobus.com</a>' ); ?></p>
						</div>


						<div class="last-feature">
							<h4><?php _e( 'Vim tation apeirian eu', 'wpglobus' ); ?></h4>

							<p><?php _e( 'Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea ex, cu eam aeterno accusata partiendo. Utinam semper volumus id eos, in postea eloquentiam nec, per homero ancillae ne. Altera fierent nam no, graeci fierent interesset vel ad. Et paulo moderatius mei.', 'wpglobus' ); ?></p>
						</div>
					</div>
				</div>
				<div class="changelog alternate">
					<h3><?php _e( 'WPGlobus API version 999', 'wpglobus' ); ?></h3>

					<div class="feature-section col three-col">
						<div>
							<h4><?php _e( 'Fabulas omittantur ut sit', 'wpglobus' ); ?></h4>

							<p><?php _e( 'Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea ex, cu eam aeterno accusata partiendo. Utinam semper volumus id eos, in postea eloquentiam nec, per homero ancillae ne. Altera fierent nam no, graeci fierent interesset vel ad. Et paulo moderatius mei..', 'wpglobus' ); ?></p>
						</div>
						<div>
							<h4><?php _e( 'Fabulas omittantur ut sit', 'wpglobus' ); ?></h4>

							<p><?php _e( 'Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea ex, cu eam aeterno accusata partiendo. Utinam semper volumus id eos.', 'wpglobus' ); ?></p>
						</div>
						<div class="last-feature">
							<h4><?php _e( 'Circus', 'wpglobus' ); ?></h4>

							<p><?php _e( 'Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea ex, cu eam aeterno accusata partiendo. Utinam semper volumus id eos.', 'wpglobus' ); ?></p>
						</div>
					</div>
				</div>
				<div class="changelog">
					<div class="feature-section col three-col">
						<div>
							<h4><?php _e( 'Fabulas omittantur ut sit', 'wpglobus' ); ?></h4>

							<p><?php _e( 'Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea ex, cu eam aeterno accusata partiendo. Utinam semper volumus id eos.', 'wpglobus' ); ?></p>
						</div>
						<div>
							<h4><?php _e( 'Fabulas omittantur ut sit', 'wpglobus' ); ?></h4>

							<p><?php _e( 'Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea ex, cu eam aeterno accusata partiendo. Utinam semper volumus id eos.', 'wpglobus' ); ?></p>
						</div>
						<div class="last-feature">
							<h4><?php _e( 'Fabulas omittantur ut sit', 'wpglobus' ); ?></h4>

							<p><?php _e( 'Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea.', 'wpglobus' ); ?></p>
						</div>
					</div>
				</div>
				<p><a name="wpglobus-mini"></a></p>

				<div class="changelog alternate">
					<h3>WPGlobus Mini is obsolete!</h3>

					<div class="feature-section col one-col">
						<div>
							<p>Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut. Vide
								doctus reformidans sea. Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea. Pro ea atqui tritani definiebas.
								His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea. Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea.Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea.Pro ea atqui tritani definiebas.
								His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea.Pro ea
								atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut. Vide
								doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea.Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea.Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea.Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea.Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea.Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea.Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea.
								Pro ea atqui tritani definiebas. His quas utinam iisque ex, falli efficiendi ius ut.
								Vide doctus reformidans sea.Pro ea atqui tritani definiebas. His quas utinam iisque ex,
								falli efficiendi ius ut. Vide doctus reformidans sea. Pro ea atqui tritani definiebas.
								His quas utinam iisque ex, falli efficiendi ius ut. Vide doctus reformidans sea.
							</p>
						</div>
					</div>
				</div>
				<div class="return-to-dashboard">
					<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpglobus_options&tab=0' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to WPGlobus Settings', 'wpglobus' ); ?></a>
				</div>
			</div>
			<!-- .about-wrap -->
		</div>
	<?php
	}

} //class

# --- EOF