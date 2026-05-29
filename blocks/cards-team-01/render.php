<?php
/**
 * Server-side rendering for `zenyx/cards-team-01`.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = isset( $attributes['heading'] ) ? (string) $attributes['heading'] : '';
$intro   = isset( $attributes['intro'] ) ? (string) $attributes['intro'] : '';

$empty_member = array(
	'imageId'      => 0,
	'imageUrl'     => '',
	'imageAlt'     => '',
	'name'         => '',
	'role'         => '',
	'linkedinUrl'  => '',
);

$raw_members = isset( $attributes['members'] ) && is_array( $attributes['members'] ) ? array_values( $attributes['members'] ) : array();
$members     = array();

foreach ( $raw_members as $item ) {
	$current = is_array( $item ) ? $item : array();
	$merged  = array_merge( $empty_member, $current );

	$image_id  = isset( $merged['imageId'] ) ? absint( $merged['imageId'] ) : 0;
	$image_url = isset( $merged['imageUrl'] ) ? (string) $merged['imageUrl'] : '';
	$image_alt = isset( $merged['imageAlt'] ) ? (string) $merged['imageAlt'] : '';

	if ( $image_id > 0 && '' === $image_url ) {
		$image_url = (string) wp_get_attachment_image_url( $image_id, 'large' );
	}
	if ( $image_id > 0 && '' === $image_alt ) {
		$image_alt = (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	}

	$members[] = array(
		'imageId'      => $image_id,
		'imageUrl'     => $image_url,
		'imageAlt'     => $image_alt,
		'name'         => isset( $merged['name'] ) ? (string) $merged['name'] : '',
		'role'         => isset( $merged['role'] ) ? (string) $merged['role'] : '',
		'linkedinUrl'  => isset( $merged['linkedinUrl'] ) ? (string) $merged['linkedinUrl'] : '',
	);
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'mwm-cards-team-01 w-full bg-[#083b51] py-[120px]',
	)
);
?>

<section data-light <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="mwm-max-1 flex flex-col gap-20">
		<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) || '' !== trim( wp_strip_all_tags( $intro ) ) ) : ?>
			<header class="mwm-cards-team-01__header flex max-w-[636px] flex-col gap-6">
				<?php if ( '' !== trim( wp_strip_all_tags( $heading ) ) ) : ?>
					<div class="mwm-cards-team-01__heading-wrap">
						<h2 class="mwm-cards-team-01__heading text-[2rem] font-heading leading-[1.2] text-neutral-light md:text-4xl">
							<?php echo wp_kses_post( $heading ); ?>
						</h2>
					</div>
				<?php endif; ?>
				<?php if ( '' !== trim( wp_strip_all_tags( $intro ) ) ) : ?>
					<div class="mwm-cards-team-01__intro-wrap text-lg leading-[1.3] text-neutral-light md:text-xl">
						<div class="mwm-cards-team-01__intro">
							<?php echo wp_kses_post( $intro ); ?>
						</div>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( ! empty( $members ) ) : ?>
			<div class="mwm-cards-team-01__grid grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
				<?php foreach ( $members as $member ) : ?>
					<?php
					$m_name    = $member['name'];
					$m_role    = $member['role'];
					$m_url          = $member['linkedinUrl'];
					$m_img          = $member['imageUrl'];
					$m_alt          = $member['imageAlt'];
					$has_img        = '' !== trim( $m_img );
					$linkedin_trim  = trim( $m_url );
					$linkedin_href  = '' !== $linkedin_trim ? esc_url( $linkedin_trim ) : '';
					$clip_id        = wp_unique_id( 'mwm-cards-team-li-' );
					$aria_name = trim( $m_name );
					$aria_li   = '' !== $aria_name
						? sprintf(
							/* translators: %s: person name */
							__( 'Perfil de LinkedIn de %s', 'zenyx' ),
							$aria_name
						)
						: __( 'Perfil de LinkedIn', 'zenyx' );
					?>
					<article class="mwm-cards-team-01__card flex md:max-w-[306px] flex-col gap-3">
						<figure class="mwm-cards-team-01__media relative aspect-306/336 w-full overflow-hidden">
							<?php if ( $has_img ) : ?>
								<img
									class="mwm-cards-team-01__img h-full w-full object-cover"
									src="<?php echo esc_url( $m_img ); ?>"
									alt="<?php echo esc_attr( $m_alt ); ?>"
									loading="lazy"
								/>
							<?php endif; ?>
						</figure>
						<div class="mwm-cards-team-01__body flex flex-col gap-5">
							<div class="flex flex-col gap-2">
								<?php if ( '' !== trim( $m_name ) ) : ?>
									<p class="mwm-cards-team-01__name text-xl leading-tight text-neutral-light">
										<?php echo esc_html( $m_name ); ?>
									</p>
								<?php endif; ?>
								<?php if ( '' !== trim( $m_role ) ) : ?>
									<p class="mwm-cards-team-01__role text-base leading-tight text-neutral-light">
										<?php echo esc_html( $m_role ); ?>
									</p>
								<?php endif; ?>
							</div>
							<?php if ( $linkedin_href ) : ?>
								<a
									class="mwm-cards-team-01__linkedin inline-flex text-neutral-light no-underline transition-opacity hover:opacity-80"
									href="<?php echo esc_url( $linkedin_trim ); ?>"
									target="_blank"
									rel="noopener noreferrer"
									aria-label="<?php echo esc_attr( $aria_li ); ?>"
								>
									<svg class="mwm-cards-team-01__linkedin-icon h-6 w-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
										<g clip-path="url(#<?php echo esc_attr( $clip_id ); ?>)">
											<path d="M20.447 20.452H16.893V14.883C16.893 13.555 16.866 11.846 15.041 11.846C13.188 11.846 12.905 13.291 12.905 14.785V20.452H9.351V9H12.765V10.561H12.811C13.288 9.661 14.448 8.711 16.181 8.711C19.782 8.711 20.448 11.081 20.448 14.166L20.447 20.452ZM5.337 7.433C4.193 7.433 3.274 6.507 3.274 5.368C3.274 4.23 4.194 3.305 5.337 3.305C6.477 3.305 7.401 4.23 7.401 5.368C7.401 6.507 6.476 7.433 5.337 7.433ZM7.119 20.452H3.555V9H7.119V20.452ZM22.225 0H1.771C0.792 0 0 0.774 0 1.729V22.271C0 23.227 0.792 24 1.771 24H22.222C23.2 24 24 23.227 24 22.271V1.729C24 0.774 23.2 0 22.222 0H22.225Z" fill="currentColor" />
										</g>
										<defs>
											<clipPath id="<?php echo esc_attr( $clip_id ); ?>">
												<rect width="24" height="24" fill="white" />
											</clipPath>
										</defs>
									</svg>
								</a>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
