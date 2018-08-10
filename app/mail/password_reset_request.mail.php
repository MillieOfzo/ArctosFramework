{{header}}

<table class="content" align="center" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#ffffff">
    <tbody>
    <tr>
        <td>
            <p style="font-size:18px; color:#000000; line-height:27px; font-weight:bold">Dear {{user_name}},</p>
            <p style="font-size:18px; color:#000000; line-height:27px;">Your password has been reset by <b>{{user_name_authenticator}}</b>.</p>
            <p style="font-size:18px; color:#000000; line-height:27px;">Log in on {{link}} with your email address and the password below</p>
            <p style="font-size:18px; color:#000000; line-height:27px;"><b style="font-size:12px; text-transform:uppercase; margin-bottom:4px">One time password</b>
                <br> <i>{{gen_password}}</i></p>

            <p style="font-size:18px; color:#000000; line-height:27px;">Have a question? You can contact us on {{contact_mail}}.</p>
            <p style="font-size:18px; color:#000000; line-height:27px; margin-bottom:0">With kind regards,
                <br> {{app_name}}</p>
        </td>
    </tr>
    </tbody>
</table>

{{footer}}