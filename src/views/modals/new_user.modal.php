<div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <!--<i class="fa fa-laptop modal-icon"></i>-->
                <img src="/public/img/<?= \Config::LOGO_NAME;?>" width="30%"/>
                <h4 class="modal-title"><span data-i18n="[html]password_modal.welcome"></span> <?= \Config::APP_TITLE;?></h4>
                <?php 	if(htmlentities($_SESSION[\Config::SES_NAME]['user_new'], ENT_QUOTES, 'UTF-8') == 1) { ?>
                    <p class="font-bold" data-i18n="[html]password_modal.first_time_text"></p>
                    <p data-i18n="[html]password_modal.first_time_subtext"></p>
                <?php };?>
            </div>
            <div class="modal-body">
                <form name="form_update_acc" id="loginForm" class="form-horizontal form-label-left">

                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12"><span data-i18n="[html]password_modal.form.password"></span>:<font color="red">*</font></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="password" class="form-control" name="password" data-i18n="[placeholder]password_modal.form.placeholder.password"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12"></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <div class="progress-small password-progress">
                                <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-md-4 col-sm-4 col-xs-12"><span data-i18n="[html]password_modal.form.confirm_password"></span>:<font color="red">*</font></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
                            <input type="password" class="form-control" name="confirmPassword" data-i18n="[placeholder]password_modal.form.placeholder.password"/>
                        </div>
                    </div>

                    <div class="form-group">
						<label class="control-label col-md-4 col-sm-4 col-xs-12"></label>
                        <div class="col-md-8 col-sm-8 col-xs-12">
							<button type="button" class="btn btn-default" data-dismiss="modal"><span data-i18n="[html]password_modal.form.close_btn"></span></button>
							<button type="submit" class="btn btn-primary" name="update_account" value="Update Account" ><span data-i18n="[html]password_modal.form.submit_btn"></span></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">

            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var lang_code = $('html').attr('lang').toLowerCase()+'_'+$('html').attr('lang').toUpperCase();


        $('#loginForm').formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            locale: lang_code,
            fields: {
                password: {
                    validators: {
                        notEmpty: {
                        },
                        // Password meter
                        callback: {
                            callback: function(value, validator, $field) {
                                var password = $field.val();
                                if (password == '') {
                                    return true;
                                }

                                var result  = zxcvbn(password),
                                    score   = result.score,
                                    message = result.feedback.warning || 'Het wachtwoord is te zwak';

                                // Update the progress bar width and add alert class
                                var $bar = $('#strengthBar');
                                switch (score) {
                                    case 0:
                                        $bar.attr('class', 'progress-bar progress-bar-danger')
                                            .css('width', '1%');
                                        break;
                                    case 1:
                                        $bar.attr('class', 'progress-bar progress-bar-danger')
                                            .css('width', '25%');
                                        break;
                                    case 2:
                                        $bar.attr('class', 'progress-bar progress-bar-danger')
                                            .css('width', '50%');
                                        break;
                                    case 3:
                                        $bar.attr('class', 'progress-bar progress-bar-warning')
                                            .css('width', '75%');
                                        break;
                                    case 4:
                                        $bar.attr('class', 'progress-bar progress-bar-primary')
                                            .css('width', '100%');
                                        break;
                                }

                                // We will treat the password as an invalid one if the score is less than 3
                                if (score < 3) {
                                    return {
                                        valid: false,
                                        message: message
                                    }
                                }
                                return true;
                            }
                        }
                    }
                },
                confirmPassword: {
                    validators: {
                        identical: {
                            field: 'password'
                        }
                    }
                }
            }
        })
            .on('success.field.fv', function(e, data) {
                if(data.fv.getInvalidFields().length > 0) {
                    data.fv.disableSubmitButtons(true);
                }
            })
            .on('success.form.fv', function(e) {
                // Voorkom form submission
                e.preventDefault();
                var $form = $(e.target),
                    fv = $form.data('formValidation');

                // Ajax om de data te posten naar de db
                $.ajax({
                    type: "POST",
                    url: "/user/password/update",
                    data: $('form[name="form_update_acc"]').serialize(),
                    success: function(data){
                        $('#myModal').modal('hide');
                        swal({
                            title: data.label,
                            text: data.text,
                            type: data.type
                        });
                    },
                    error: function(data){
                        $('#myModal').modal('hide');
                        swal({
                            title: data.label,
                            text: data.text,
                            type: data.type
                        });
                    }
                });
            });
    });
</script>