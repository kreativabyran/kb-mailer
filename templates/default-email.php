<?php
/**
 * @var array $args // Arguments passed to the template
 */

?>
<div style="background-color:<?php echo esc_attr( $args['main_color'] ); ?>;margin:0;padding:20px 0;width:100%">
	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
		<tbody>
			<tr>
				<td align="center" valign="top">
					<table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff;border-radius:2px">
						<tbody>
							<?php if ( ! empty( $args['header'] ) ) : ?>
								<tr>
									<td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%"  style="background-color:<?php echo esc_attr( $args['main_color'] ); ?>;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;border-radius:2px 2px 0 0;padding:0 32px;">
											<tbody>
												<tr>
													<td style="display:block">
														<h1 style="font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:30px;font-weight:400;line-height:150%;margin:0;text-align:left;padding:32px 0 20px;border-bottom: 1px solid #eee;"><?php echo esc_html( $args['header'] ); ?></h1>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							<?php endif; ?>
							<tr>
								<td>
									<table border="0" cellpadding="0" cellspacing="0" width="600">
										<tbody>
											<tr>
												<td valign="top" style="background-color:#ffffff;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-weight:400;">
													<table border="0" cellpadding="20" cellspacing="0" width="100%">
														<tbody>
															<tr>
																<td valign="top" style="padding:32px">
																	<?php echo wp_kses_post( $args['body'] ); ?>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<?php if ( ! empty( $args['logo'] ) ) : ?>
								<tr>
									<td valign="top" style="padding:0;">
										<table border="0" cellpadding="10" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td colspan="2" valign="middle" style="text-align:center;padding:24px 0 0 0">
														<?php if ( ! empty( $args['logo_url'] ) ) : ?>
														<a href="<?php echo esc_url( $args['logo_url'] ); ?>">
														<?php endif; ?>
															<img src="<?php echo esc_url( $args['logo'] ); ?>" alt="logotyp" width="200">
														<?php if ( ! empty( $args['logo_url'] ) ) : ?>
														</a>
														<?php endif; ?>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							<?php endif; ?>
							<?php if ( ! empty( $args['footer'] ) ) : ?>
							<tr>
								<td valign="top" style="padding:0;">
									<table border="0" cellpadding="10" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td colspan="2" valign="middle" style="border-radius:6px;border:0;color:#858585;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:12px;line-height:150%;text-align:center;padding:24px 0">
													<?php echo wp_kses_post( $args['footer'] ); ?>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>