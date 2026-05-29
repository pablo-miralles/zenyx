<?php
/**
 * 404 — Page Not Found template.
 */

$title    = get_option( 'mwm_404_title' );
$btn_text = get_option( 'mwm_404_button_text' );

$title       = ! empty( $title ) ? $title : __( 'Pagina no encontrada', THEME_TEXT_DOMAIN );
$btn_text    = ! empty( $btn_text ) ? $btn_text : __( 'Volver a la home', THEME_TEXT_DOMAIN );
$home_url    = home_url( '/' );
$aria_button = sprintf(
	/* translators: %s: home URL. */
	__( 'Ir a la pagina principal: %s', THEME_TEXT_DOMAIN ),
	$home_url
);

get_header();
?>

<main class="mwm-main-container">
	<section class="pt-header relative overflow-hidden bg-neutral-light pb-10 lg:px-[35px] lg:pb-[35px]">
		<div class="mwm-max-1">
			<div class="flex min-h-[500px] w-full flex-col justify-between xl:min-h-[768px]">
				<div class="flex justify-center pt-5">
					<div class="w-full">
						<svg class="h-auto w-full" viewBox="0 0 1296 571" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<path d="M1296 370.975V431.936H1200.1V563.525H1133.93V431.936H891.57V376.179L1143.6 7.43436H1200.1V370.975H1296ZM1133.93 370.975V129.358L966.657 370.975H1133.93Z" fill="url(#pattern0_6020_7445)"></path>
							<path d="M1296 370.975V431.936H1200.1V563.525H1133.93V431.936H891.57V376.179L1143.6 7.43436H1200.1V370.975H1296ZM1133.93 370.975V129.358L966.657 370.975H1133.93Z" fill="url(#paint0_linear_6020_7445)"></path>
							<path d="M648.056 0C709.018 0 757.341 25.029 793.026 75.0871C829.207 124.649 847.297 194.78 847.297 285.479C847.297 376.179 829.207 446.557 793.026 496.615C757.341 546.178 709.018 570.959 648.056 570.959C586.103 570.959 537.036 546.178 500.856 496.615C465.171 446.557 447.328 376.179 447.328 285.479C447.328 194.78 465.171 124.649 500.856 75.0871C537.036 25.029 586.103 0 648.056 0ZM744.703 118.206C722.399 80.0433 689.936 60.9617 647.312 60.9617C604.689 60.9617 572.225 80.0433 549.922 118.206C527.619 156.369 516.468 212.127 516.468 285.479C516.468 358.832 527.619 414.59 549.922 452.753C572.225 490.916 604.689 509.997 647.312 509.997C689.936 509.997 722.399 490.916 744.703 452.753C767.501 414.59 778.901 358.832 778.901 285.479C778.901 212.127 767.501 156.369 744.703 118.206Z" fill="url(#pattern1_6020_7445)"></path>
							<path d="M648.056 0C709.018 0 757.341 25.029 793.026 75.0871C829.207 124.649 847.297 194.78 847.297 285.479C847.297 376.179 829.207 446.557 793.026 496.615C757.341 546.178 709.018 570.959 648.056 570.959C586.103 570.959 537.036 546.178 500.856 496.615C465.171 446.557 447.328 376.179 447.328 285.479C447.328 194.78 465.171 124.649 500.856 75.0871C537.036 25.029 586.103 0 648.056 0ZM744.703 118.206C722.399 80.0433 689.936 60.9617 647.312 60.9617C604.689 60.9617 572.225 80.0433 549.922 118.206C527.619 156.369 516.468 212.127 516.468 285.479C516.468 358.832 527.619 414.59 549.922 452.753C572.225 490.916 604.689 509.997 647.312 509.997C689.936 509.997 722.399 490.916 744.703 452.753C767.501 414.59 778.901 358.832 778.901 285.479C778.901 212.127 767.501 156.369 744.703 118.206Z" fill="url(#paint1_linear_6020_7445)"></path>
							<path d="M404.429 370.975V431.936H308.526V563.525H242.36V431.936H0V376.179L252.025 7.43436H308.526V370.975H404.429ZM242.36 370.975V129.358L75.0871 370.975H242.36Z" fill="url(#pattern2_6020_7445)"></path>
							<path d="M404.429 370.975V431.936H308.526V563.525H242.36V431.936H0V376.179L252.025 7.43436H308.526V370.975H404.429ZM242.36 370.975V129.358L75.0871 370.975H242.36Z" fill="url(#paint2_linear_6020_7445)"></path>
							<defs>
								<linearGradient id="paint0_linear_6020_7445" x1="1093.78" y1="7.43436" x2="1093.78" y2="563.525" gradientUnits="userSpaceOnUse">
									<stop stop-color="#C1D9E4" stop-opacity="0"></stop>
									<stop offset="1" stop-color="#C1D9E4"></stop>
								</linearGradient>
								<linearGradient id="paint1_linear_6020_7445" x1="647.312" y1="0" x2="647.312" y2="570.959" gradientUnits="userSpaceOnUse">
									<stop stop-color="#C1D9E4" stop-opacity="0"></stop>
									<stop offset="1" stop-color="#C1D9E4"></stop>
								</linearGradient>
								<linearGradient id="paint2_linear_6020_7445" x1="202.215" y1="7.43436" x2="202.215" y2="563.525" gradientUnits="userSpaceOnUse">
									<stop stop-color="#C1D9E4" stop-opacity="0"></stop>
									<stop offset="1" stop-color="#C1D9E4"></stop>
								</linearGradient>
								<pattern id="pattern0_6020_7445" patternUnits="userSpaceOnUse" patternTransform="matrix(10.454 0 0 5.2285 1092.41 284.104)" preserveAspectRatio="none" viewBox="0 0 40.2076 20.1096" width="1" height="1">
									<use xlink:href="#pattern0_6020_7445_inner" transform="translate(0 -20.1096)"></use>
									<use xlink:href="#pattern0_6020_7445_inner" transform="translate(20.1038 -10.0548)"></use>
									<g id="pattern0_6020_7445_inner">
										<path d="M5.88736 10.584H0V0H10.5809V5.868L5.88736 10.584Z" fill="#083B51"></path>
									</g>
									<use xlink:href="#pattern0_6020_7445_inner" transform="translate(20.1038 10.0548)"></use>
								</pattern>
								<pattern id="pattern1_6020_7445" patternUnits="userSpaceOnUse" patternTransform="matrix(10.454 0 0 5.2285 645.937 284.104)" preserveAspectRatio="none" viewBox="0 0 40.2076 20.1096" width="1" height="1">
									<use xlink:href="#pattern1_6020_7445_inner" transform="translate(0 -20.1096)"></use>
									<use xlink:href="#pattern1_6020_7445_inner" transform="translate(20.1038 -10.0548)"></use>
									<g id="pattern1_6020_7445_inner">
										<path d="M5.88736 10.584H0V0H10.5809V5.868L5.88736 10.584Z" fill="#083B51"></path>
									</g>
									<use xlink:href="#pattern1_6020_7445_inner" transform="translate(20.1038 10.0548)"></use>
								</pattern>
								<pattern id="pattern2_6020_7445" patternUnits="userSpaceOnUse" patternTransform="matrix(10.454 0 0 5.2285 200.839 284.104)" preserveAspectRatio="none" viewBox="0 0 40.2076 20.1096" width="1" height="1">
									<use xlink:href="#pattern2_6020_7445_inner" transform="translate(0 -20.1096)"></use>
									<use xlink:href="#pattern2_6020_7445_inner" transform="translate(20.1038 -10.0548)"></use>
									<g id="pattern2_6020_7445_inner">
										<path d="M5.88736 10.584H0V0H10.5809V5.868L5.88736 10.584Z" fill="#083B51"></path>
									</g>
									<use xlink:href="#pattern2_6020_7445_inner" transform="translate(20.1038 10.0548)"></use>
								</pattern>
							</defs>
						</svg>
					</div>
				</div>

				<div class="mt-10 pb-3 lg:mt-14 lg:pb-0">
					<div class="mx-auto flex max-w-[416px] flex-col items-center gap-10 text-center">
						<h1 class="m-0 text-3xl leading-tight text-[#083B51] lg:text-5xl"><?php echo esc_html( $title ); ?></h1>
						<?php
						mwm_render_button(
							array(
								'text'       => $btn_text,
								'url'        => $home_url,
								'variant'    => 'primary',
								'icon'       => 'arrow-right',
								'aria_label' => $aria_button,
							)
						);
						?>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>

<?php get_footer(); ?>
