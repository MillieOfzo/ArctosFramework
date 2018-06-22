	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span data-i18n="[html]users.title">Users</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<table class="table table-hover jambo_table bulk_action datatable" id="datatable" style="width:100%">
							<thead>
								<tr>
									<th data-i18n="[html]users.table.th1">UserID</th>
									<th data-i18n="[html]users.table.th2">User name</th>
									<th data-i18n="[html]users.table.th3">User last name</th>
									<th data-i18n="[html]users.table.th4">Email</th>
									<th data-i18n="[html]users.table.th5">Userrole</th>
									<th data-i18n="[html]users.table.th6">Status</th>
									<th data-i18n="[html]users.table.th7">Last access</th>
									<th data-i18n="[html]users.table.th8"data-i18n="[html]users.status.1">Action</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
		
			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span data-i18n="[html]users.new.title">New user</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<form id="NewWerkbonForm" name="new_wb">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.new.input.1">User name</span><font color="red">*</font></label>
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="new_user_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.new.input.2">User last name</span><font color="red">*</font></label>
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="new_user_last_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.new.input.3">User email</span><font color="red">*</font></label>
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="new_user_email" type="text" >
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]users.ne.button">Create</span></button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6" style="display:none;" id="menu-editor">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span data-i18n="[html]users.edit.title">Edit user</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<form id="update_user" name="update_user">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.edit.input.1">User name</span><font color="red">*</font></label>
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="user_name" type="text" value="">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.edit.input.2">User last name</span><font color="red">*</font></label>
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="user_last_name" type="text" value="">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.edit.input.3">User email</span><font color="red">*</font></label>
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="user_email" type="text" value="">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><span>User role</span data-i18n="[html]users.edit.input.4"><font color="red">*</font></label>
										<select class="select2 form-control" name="user_role">
											<?= $obj['response']['role_select'];?>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><span data-i18n="[html]users.edit.input.5">User status</span><font color="red">*</font></label>
										<select class="select2 form-control" name="user_status">
											<?= $obj['response']['status_select'];?>
										</select>
									</div>
								</div>

								<input class="form-control hidden" name="user_id" type="text" value="">

								<div class="col-md-12">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]users.edit.button">Update</span></button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
	
	
	<?php
		// View specific scripts
		array_push($arr_js, '/public/js/dataTables/datatables.min.js');
		array_push($arr_js, '/public/js/dataTables/datatables_responsive.min.js');		
		
	?>		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.$js.'"></script>';
		}		
	?>	
    <script>
    $(document).ready(function() {
		var menuEditor = $("#menu-editor");
    	$('.datatable').on('click', '#delete', function() {
    		var id = $(this).attr('value');
    		var user_email = '<b>'+$(this).attr('rel')+'</b>';
    		var csrf_token = $('input[name="csrf"]').attr('value');
    		swal({
    			html: true,
    			title: i18n.t('swal.confirm.title'),
    			text: i18n.t('users.swal.confirm.msg', { placeholder: user_email}),
    			type: "warning",
    			showCancelButton: true,
				cancelButtonText: i18n.t('swal.confirm.cancelbutton'),
    			confirmButtonColor: "#DD6B55",
    			confirmButtonText: i18n.t('swal.confirm.confirmbutton'),
    			closeOnConfirm: false
    		}, function() {
    			$.ajax({
    				type: "post",
    				url: "/users/delete",
    				data: {
    					user_id: id,
    					csrf: csrf_token
    				},
    				success: function(data) {
    					swal({
    						html: true,
                            title: data.label,
                            text: data.text,
                            type: data.type
    					});
    					table_active.ajax.reload(null, false);
    				}
    			});
    		});
    	});

        $(".datatable").on('click', '#edit', function() {
            var data = table_active.row($(this).parents('tr')).data();
            $('input[name="user_id"]').val(data[0]);
            $('input[name="user_name"]').val(data[1]);
            $('input[name="user_last_name"]').val(data[2]);
            $('input[name="user_email"]').val(data[3]);
			$('select[name="user_role"]').select2("val", data[8]);
			$('select[name="user_status"]').select2("val", data[9] );
			menuEditor.fadeIn();
        });		
		
    	$.extend(true, $.fn.dataTable.defaults, {
    		language: {
    			url: '/public/js/dataTables/' + $('html').attr('lang') + '.json'
    		},
    		iDisplayLength: 10,
    		deferRender: true,
    		order: [
    			[3, "desc"]
    		],
    		lengthMenu: [10, 20, 25],
    		processing: true,
    		serverSide: true,
			responsive: {
				details: {
					renderer: function ( api, rowIdx, columns ) {
						var data = $.map( columns, function ( col, i ) {
							return col.hidden ?
								'<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
									'<td>'+col.title+':'+'</td> '+
									'<td>'+col.data+'</td>'+
								'</tr>' :
								'';
						} ).join('');
	
						return data ?
							$('<table/ width="100%" class="sub_responsive">').append( data ) :
							false;
					}
				}
			}
    	});
    	var interval;
		var lang_code = $('html').attr('lang').toLowerCase() + '_' + $('html').attr('lang').toUpperCase();

		function init_lang()
        {
            $.i18n.init({
                resGetPath: '/src/lang/__lng__.json',
                load: 'unspecific',
                fallbackLng: false,
                lng: $('html').attr('lang')
            }, function(t) {
                $('#i18container').i18n();
            });
        }

    	var table_active = $(".datatable").DataTable({
    		ajax: "/users/table",
    		fnInitComplete: function(oSettings, json) {
                init_lang();
    		}
    	});
		
    	$('#update_user').formValidation({
    		framework: 'bootstrap',
    		icon: {
    			valid: 'glyphicon glyphicon-ok',
    			invalid: 'glyphicon glyphicon-remove',
    			validating: 'glyphicon glyphicon-refresh'
    		},
    		locale: lang_code,
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
    			},
    			user_role: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			user_status: {
    				validators: {
    					notEmpty: {
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
    		$.ajax({
    			type: "POST",
    			url: "/users/update",
    			data: $('form[name="update_user"]').serialize(),
    			success: function(data) {
    				swal({
    					html: true,
    					title: data.label,
    					text: data.text,
    					type: data.type
    				});
    				table_active.ajax.reload(null, false);
					fv.resetForm();
					$('form').find("input[type=text], textarea, select").val("");
					menuEditor.fadeOut();
    			},
    			error: function(xhr, status, error) {
    				var json = $.parseJSON(xhr.responseText);
    				console.log(json);
    				swal({
    					html: true,
    					title: json.title,
    					text: json.msg,
    					type: "error"
    				});
    			}
    		});
    	});
    	$('#NewWerkbonForm').formValidation({
    		framework: 'bootstrap',
    		icon: {
    			valid: 'glyphicon glyphicon-ok',
    			invalid: 'glyphicon glyphicon-remove',
    			validating: 'glyphicon glyphicon-refresh'
    		},
    		locale: lang_code,
    		fields: {
    			new_user_name: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			new_user_last_name: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			new_user_email: {
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
    		$.ajax({
    			type: "POST",
    			url: "/users/new",
    			data: $('form[name="new_wb"]').serialize(),
    			success: function(data) {
    				swal({
    					html: true,
                        title: data.label,
                        text: data.text,
                        type: data.type
    				});
    				table_active.ajax.reload(null, false);
					fv.resetForm();
					$('form').find("input[type=text], textarea, select").val("");
    			},
    			error: function(xhr, status, error) {
    				var json = $.parseJSON(xhr.responseText);
    				//console.log(json);
    				swal({
    					html: true,
    					title: json.title,
    					text: json.msg,
    					type: "error"
    				});
    			}
    		});
    	});
    });
	</script>	