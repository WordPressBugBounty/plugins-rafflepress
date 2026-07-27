<?php if ( ! defined( 'ABSPATH' ) ) { exit; } // Prevent direct file access ?>
<?php // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template partial loaded via require_once inside a function (rafflepress_lite_*_page / render / email builder); its top-level variables are function-local, not global. ?>
<?php // phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- Standalone email template sent via wp_mail(); CSS must be inline because emails have no wp_enqueue/wp_head pipeline. ?>
<!DOCTYPE html>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<style type="text/css" rel="stylesheet" media="all">
	/* Media Queries */
	@media only screen and (max-width: 500px) {
		.button {
			width: 100% !important;
		}
	}
	</style>
</head>

<?php

$style = array(
	/* Layout ------------------------------ */

	'body'                => 'margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;',
	'email-wrapper'       => 'width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;',

	/* Masthead ----------------------- */

	'email-masthead'      => 'padding: 25px 0; text-align: center;',
	'email-masthead_name' => 'font-size: 16px; font-weight: bold; color: #2F3133; text-decoration: none; text-shadow: 0 1px 0 white;',

	'email-body'          => 'width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;',
	'email-body_inner'    => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0;',
	'email-body_cell'     => 'padding: 35px;',

	'email-footer'        => 'width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;',
	'email-footer_cell'   => 'color: #AEAEAE; padding: 35px; text-align: center;',

	/* Body ------------------------------ */

	'body_action'         => 'width: 100%; margin: 30px auto; padding: 0; text-align: center;',
	'body_sub'            => 'margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;',

	/* Type ------------------------------ */

	'anchor'              => 'color: #f35414;',
	'header-1'            => 'margin-top: 0; color: #2F3133; font-size: 19px; font-weight: bold; text-align: left;',
	'paragraph'           => 'margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;',
	'paragraph-sub'       => 'margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;',
	'paragraph-center'    => 'text-align: center;',

	/* Buttons ------------------------------ */

	'button'              => 'display: block; display: inline-block; width: 200px; min-height: 20px; padding: 10px;
                 background-color: #3869D4; border-radius: 3px; color: #ffffff; font-size: 15px; line-height: 25px;
                 text-align: center; text-decoration: none; -webkit-text-size-adjust: none;',

	'button--green'       => 'background-color: #22BC66;',
	'button--red'         => 'background-color: #dc4d2f;',
	'button--blue'        => 'background-color: #4CAF55;',
);
?>

<?php $fontFamily = 'font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif;'; ?>

<body style="<?php echo esc_attr( $style['body'] ); ?>">
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td style="<?php echo esc_attr( $style['email-wrapper'] ); ?>" align="center">
				<table width="100%" cellpadding="0" cellspacing="0">
					<!-- Logo -->
					<tr>
						<td style="<?php echo esc_attr( $style['email-masthead'] ); ?>">
							<a style="<?php echo esc_attr( $fontFamily ); ?> <?php echo esc_attr( $style['email-masthead_name'] ); ?>" href=""
								target="_blank">
								<img src="<?php echo esc_url( RAFFLEPRESS_PLUGIN_URL ); ?>public/img/logo.png"
									alt="RafflePress Logo" style="width:200px">
							</a>
						</td>
					</tr>

					<!-- Email Body -->
					<tr>
						<td style="<?php echo esc_attr( $style['email-body'] ); ?>" width="100%">
							<table style="<?php echo esc_attr( $style['email-body_inner'] ); ?>" align="center" width="570"
								cellpadding="0" cellspacing="0">
								<tr>
									<td style="<?php echo esc_attr( $fontFamily ); ?> <?php echo esc_attr( $style['email-body_cell'] ); ?>">
										<!-- Greeting -->
										<?php esc_html_e( 'Hello', 'rafflepress' ); ?>,<br><br>

										<?php
										if ( $settings->updates == 'daily' ) {
											?>
											<?php esc_html_e( "Here's a summary of your Giveaways for the last 24 hours.", 'rafflepress' ); ?><br><br>
											<?php
										} else {
											?>
											<?php esc_html_e( "Here's a summary of your Giveaways for the last week.", 'rafflepress' ); ?><br><br>
											<?php
										}
										?>

										<?php
										if ( ! empty( $daily_results_running ) ) {
											?>
										<strong><?php esc_html_e( 'Running', 'rafflepress' ); ?></strong><br>
										<ul>
											<?php
											foreach ( $daily_results_running as $v ) {
												?>
											<li style="font-size:13px"><?php echo esc_html( $v->name ); ?> -
												<?php echo esc_html( $v->contestants_count ); ?>
												<?php esc_html_e( 'new contestants and', 'rafflepress' ); ?>
												<?php echo esc_html( $v->entries_count ); ?>
												<?php esc_html_e( 'new entries', 'rafflepress' ); ?></li>
												<?php
											}
											?>
										</ul>
											<?php
										}
										?>
										<?php
										if ( ! empty( $daily_results_ended ) ) {
											?>
										<strong><?php esc_html_e( 'Ended', 'rafflepress' ); ?></strong><br>
										<ul>
											<?php
											foreach ( $daily_results_ended as $v ) {
												?>
											<li style="font-size:13px"><?php echo esc_html( $v->name ); ?> -
												<?php esc_html_e( 'with', 'rafflepress' ); ?>
												<?php echo esc_html( $v->contestants_count ); ?>
												<?php esc_html_e( 'contestants and', 'rafflepress' ); ?>
												<?php echo esc_html( $v->entries_count ); ?>
												<?php esc_html_e( 'entries', 'rafflepress' ); ?></li>
												<?php
											}
											?>
										</ul>
											<?php
										}
										?>

										<?php
										if ( ! empty( $weekly_results_running ) ) {
											?>
										<strong><?php esc_html_e( 'Running', 'rafflepress' ); ?></strong><br>
										<ul>
											<?php
											foreach ( $weekly_results_running as $v ) {
												?>
											<li style="font-size:13px"><?php echo esc_html( $v->name ); ?> -
												<?php echo esc_html( $v->contestants_count ); ?>
												<?php esc_html_e( 'new contestants and', 'rafflepress' ); ?>
												<?php echo esc_html( $v->entries_count ); ?>
												<?php esc_html_e( 'new entries', 'rafflepress' ); ?></li>
												<?php
											}
											?>
										</ul>
											<?php
										}
										?>
										<?php
										if ( ! empty( $weekly_results_ended ) ) {
											?>
										<strong><?php esc_html_e( 'Ended', 'rafflepress' ); ?></strong><br>
										<ul>
											<?php
											foreach ( $weekly_results_ended as $v ) {
												?>
											<li style="font-size:13px"><?php echo esc_html( $v->name ); ?> -
												<?php esc_html_e( 'with', 'rafflepress' ); ?>
												<?php echo esc_html( $v->contestants_count ); ?>
												<?php esc_html_e( 'contestants and', 'rafflepress' ); ?>
												<?php echo esc_html( $v->entries_count ); ?>
												<?php esc_html_e( 'entries', 'rafflepress' ); ?></li>
												<?php
											}
											?>
										</ul>
											<?php
										}
										?>


										<br>
										<?php esc_html_e( 'Cheers', 'rafflepress' ); ?>,<br><br>
										RafflePress

									</td>
								</tr>
							</table>
						</td>
					</tr>

					<!-- Footer -->
					<tr>
						<td>
							<table style="<?php echo esc_attr( $style['email-footer'] ); ?>" align="center" width="570"
								cellpadding="0" cellspacing="0">
								<tr>
									<td style="<?php echo esc_attr( $fontFamily ); ?> <?php echo esc_attr( $style['email-footer_cell'] ); ?>">
										<p style="<?php echo esc_attr( $style['paragraph-sub'] ); ?>">
											&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
											<a style="<?php echo esc_attr( $style['anchor'] ); ?>" href="https://www.rafflepress.com"
												target="_blank">RafflePress</a>.
											All rights reserved.
										</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>

</html>
