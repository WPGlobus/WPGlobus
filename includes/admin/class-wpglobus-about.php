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

			<?php //$this->intro(); ?>

			<?php $welcome_page_title = __( 'Welcome to WPGlobus', 'wpglobus' ); ?>

			<h2><?php echo $welcome_page_title; ?></h2>

			<div class="wpglobus-about-wrap about-wrap">
				<div class="changelog">
					<div class="feature-main feature-section col three-col">
						<h3><?php _e( 'Main features', 'wpglobus' ); ?></h3>

						<div>
							<h4><?php _e( 'Fabulas omittantur ut sit', 'wpglobus' ); ?></h4>

							<p><?php _e( 'Mea dicat facer nonumes ex. Nam id prompta epicurei, te cibo accusata pro. At ornatus docendi pro, quis delenit in mel, in aperiri impedit pri. Et nobis singulis cum, no mundi solet causae mei, duo stet vituperata!', 'wpglobus' ); ?></p>
						</div>

						<div>
							<div class="wpglobus-icon"></div>
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