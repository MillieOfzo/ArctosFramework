{{header}}

	<!-- BODY -->
	<tr>
		<td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;" >
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
						<b>Beste {{user_name}},</b>
					</td>
				</tr>
				<tr>
					<td style="padding: 20px 0 30px 0;">
						De authenticatie token is geverifeerd. Log in met jouw email adres en het onderstaande wachtwoord op {{link}}					
					</td>
				</tr>
				<tr>
					<td>
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td style="color: #153643; font-family: Arial, sans-serif; font-size: 24px;">
									<b>Wachtwoord:</b>
								</td>
								<td width="350" valign="top">
									{{gen_password}}
								</td>
							</tr>
						</table>
					</td>
				</tr>

			</table>
		</td>
	</tr>
	<!-- / BODY -->
	
{{footer}}