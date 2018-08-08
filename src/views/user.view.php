<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row m-t-sm">
        <div class="col-sm-12 col-lg-4">
            <div class="panel panel-filled">

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 m-t-sm">
                            <i class="pe pe-7s-user c-accent fa-3x"></i>
                            <div class="btn-group m-t-sm pull-right">
                                <a href="#" class="btn btn-default btn-sm" data-toggle="tooltip" data-i18n="[title]user.edit.tooltip.password_reset"><i class="fa fa-refresh"></i></a>
                            </div>
                            <h3 class="m-b-none">
                               <span id="user_name"></span> <span id="user_last_name"></span>
                            </h3>
                            <table class="table m-t-sm">
                                <tbody>
                                <tr>
                                    <td data-i18n="[html]user.edit.table.tr1">
                                        Status
                                    </td>
                                    <td>
                                        <strong class="c-white" id="user_status"></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-i18n="[html]user.edit.table.tr2">
                                        Email
                                    </td>
                                    <td>
                                        <strong class="c-white" id="user_email"></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-i18n="[html]user.edit.table.tr3">User role</td>
                                    <td>
                                        <strong class="c-white" id="user_role"></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td data-i18n="[html]user.edit.table.tr4">
                                        Last access
                                    </td>
                                    <td>
                                        <strong class="c-white" id="user_last_access"></strong>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
            <div class="panel panel-filled">

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 m-t-sm">
                            <h3 class="m-b-none" data-i18n="[html]user.edit.title">
                                Update user
                            </h3>
                            <form class="m-t" id="update_user" name="update_user" >
								<div class="row">
									<div class="form-group col-md-6">
										<label data-i18n="[html]user.edit.input.3">Email address:</label>
										<input type="email" class="form-control" name="user_email" required="" value="" data-i18n="[placeholder]loginscreen.placeholder.email">
									</div>
									<div class="form-group col-md-6">
										<label data-i18n="[html]user.edit.input.4">Language:</label>
										<select class="select2 form-control" name="user_language">
											<?= \App\Classes\Helper::getLanguageSelect();?>
										</select>
									</div>									
								</div>
								
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label data-i18n="[html]user.edit.input.1">First name:</label>
                                        <input type="text" name="user_name" data-i18n="[placeholder]placeholders.input" class="form-control" data-name="Address">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label data-i18n="[html]user.edit.input.2">Last name: </label>
                                        <input type="text" name="user_last_name" data-i18n="[placeholder]placeholders.input" class="form-control" data-name="Zipcode">
                                    </div>
                                </div>
								
                                <input type="text" name="user_id" hidden>
                                <button type="submit" class="btn btn-primary " name="update" data-i18n="[html]user.edit.button">Update</button>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<?php
foreach($arr_js as $js){
    echo '<script src="'.$js.'"></script>';
}
?>
<script>
    $(document).ready(function () {
		
        getUserInfo('/user/info');

        $('#update_user').formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            locale: $('html').attr('lang').toLowerCase() + '_' + $('html').attr('lang').toUpperCase(),
            fields: {
                user_name: {
                    validators: {
                        notEmpty: {
                        }
                    }
                },
                user_last_name: {
                    validators: {
                        notEmpty: {
                        }
                    }
                },
                user_email: {
                    validators: {
                        notEmpty: {
                        },
                        emailAddress: {
                        }
                    }
                }
            }
        }).on('success.field.fv', function(e, data) {
            if (data.fv.getInvalidFields().length > 0) {
                data.fv.disableSubmitButtons(true);
            }
        }).on('success.form.fv', function(e) {
            // Voorkom form submission
            e.preventDefault();
            var $form = $(e.target),
                fv = $form.data('formValidation');
			$.ajaxq ('users',{
				type: "POST",
    			url: '/user/update',
    			data: $('form[name="update_user"]').serialize()
			}).done(function(data){
				swal({
    				title: data.title,
    				html: data.text,
    				type: data.type
    			});
			}).fail(ajaxObj.fail);				

        });

    });

    function getUserInfo(url)
	{
		$.ajaxq ('user',{
			url: url,
		}).done(function(data){
			$.each( data, function( key, value ) {
                $('#'+key).html(value);
				if(key == 'user_language')
				{
					$('select[name="'+key+'"]').val(value);
					$('select[name="'+key+'"]').trigger('change');						
				}				
                $('input[name="'+key+'"').val(value);
            });
		}).fail(ajaxObj.fail);
		
    }

</script>