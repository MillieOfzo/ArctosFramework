<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="pull-right"><span data-i18n="[html]tickets.update.submitted_by">Submitted by</span>:<b><?=  $obj['response']['returned_ticket_row']['ticket_created_by']; ?></b>
                    <span data-i18n="[html]tickets.update.checked_by">Checked by</span>: <b><?=  $obj['response']['returned_ticket_row']['ticket_checked_by']; ?></b></span>

                    <h2><a href="/tickets" class="text-primary"><i class="fa fa-arrow-left"></i> </a><span data-i18n="[html]tickets.label">Ticket</span> <b><?=  $obj['response']['returned_ticket_row']['ticket_id']; ?></b> <small></small></h2>

                    <div class="clearfix"></div>
                </div>
                <div class="ibox-content" id="ticket_info">


                </div>

                <div class="ibox chat-view">
                    <div class="ibox-title">
                        <h2><span data-i18n="[html]tickets.update.updates">Updates:</span><small></small></h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="chat-discussion"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="ibox float-e-margins">

                <div class="ibox-title">
                    <h2><span data-i18n="[html]tickets.update.create.label">Update ticket</span><small></small></h2>

                    <div class="clearfix"></div>
                </div>
                <div class="ibox-content">

                    <form id="update_wb_form" >

                        <div class="form-group">
                            <label class="control-label" ><span >Extern ticketid:</span></label>
                            <input type="text" class="form-control" name="ticketnr"  data-i18n="[placeholder]placeholders.input">
                        </div>

                        <div class="form-group">
                            <label class="control-label" ><span data-i18n="[html]tickets.update.create.label">Update comment:</span><font color='red'>*</font></label>
                            <textarea class="form-control" name="extra_comment_update" rows="5" cols="40" data-i18n="[placeholder]placeholders.input"></textarea>
                        </div>

                        <div class="form-group" >
                            <label class="control-label"><span >Update status:</span><font color='red'>*</font></label>
                            <select class="select2 form-control"  name="status_update" id="status_update">
                                <optgroup data-i18n="[label]tickets.select.label.current" label="Huidige status..."></optgroup>
                                <option value="<?=  $obj['response']['returned_ticket_row']['ticket_status']; ?>"> <?=  $obj['response']['returned_ticket_row']['ticket_status']; ?> </option>
                                <optgroup data-i18n="[label]tickets.select.label.options" label="Ticket opties..."></optgroup>
                                <option value="Open" data-i18n="[html]tickets.status.open">Open</option>
                                <option value="On hold" data-i18n="[html]tickets.status.on_hold">On hold</option>
                                <option value="Opnieuw geopend" data-i18n="[html]tickets.status.opnieuw_geopend">Opnieuw openen</option>
                                <option value="Opnieuw verzonden" data-i18n="[html]tickets.status.opnieuw_verzenden">Opnieuw verzenden</option>
                                <optgroup data-i18n="[label]tickets.select.label.actions" label="Ticket acties..."></optgroup>
                                <option value="Geannuleerd" data-i18n="[html]tickets.status.geannuleerd">Ticket annuleren</option>
                                <!--<option value="Escaleren">Werkbon escaleren</option>-->
                                <option value="Doorzetten" data-i18n="[html]tickets.status.doorzetten">Ticket doorsturen</option>
                                <option value="Gesloten" data-i18n="[html]tickets.status.gesloten">Ticket sluiten</option>
                            </select>
                        </div>


                        <div class="form-group" id="on_hold_div" style="<?= $obj['response']['display_hold'];?>">
                            <label class="control-label"><span data-i18n="[html]tickets.select.label.on_hold"></span>:<font color='red'>*</font></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" name="datum_on_hold" class="form-control" value="<?=  $obj['response']['on_hold_date']; ?>" placeholder="dd-mm-jjjj" data-inputmask="'mask': '99-99-9999'">
                            </div>
                        </div>
                        <div class="form-group" id="geannuleerd_div" style="<?=  $obj['response']['display_geannuleerd']; ?>">
                            <label class="control-label"><span data-i18n="[html]tickets.select.label.canceled">Reden geannuleerd</span>:<font color='red'>*</font></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-times"></i></span>
                                <select class="form-control"  name="reden_geannuleerd" >
                                    <option value="<?=  $obj['response']['returned_ticket_row']['ticket_sub_status'];?>"> <?=  $obj['response']['returned_ticket_row']['ticket_sub_status'];?> </option>
                                    <optgroup data-i18n="[label]tickets.select.label.canceled" label="Reason canceled..."></optgroup>
                                    <option value="Storing hersteld">Storing hersteld</option>
                                    <option value="Ticket onnodig">Ticket onnodig</option>
                                    <option value="Ticket foutief">Ticket foutief</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="doorzet_div" style="display:none;">
                            <label class="control-label"><span data-i18n="[html]tickets.select.label.sent_to">Doorzetten naar</span>:<font color='red'>*</font></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
                                <select class="form-control"  name="doorzetten_naar" >
                                    <option value=""> </option>
                                    <optgroup data-i18n="[label]tickets.select.label.external"  label="Externe partijen..."></optgroup>

                                </select>
                            </div>
                        </div>

                        <div class="alert alert-warning"  id="gesloten_div" style="display:none;">
                            <b>Pro Tip:</b> Sluit de openstaande werkbon in SCS op OMS: <b><?=  $obj['response']['returned_ticket_row']['ticket_customer_scsnr'];?></b>.
                        </div>

                        <input type="text" hidden name="ticket_id" id="ticket_id" value="<?=  $obj['response']['returned_ticket_row']['ticket_id']; ?>">
                        <input type="text" hidden name="wb_id" value="">
                        <input type="text" hidden name="totaal_uitval" value=""/>

                        <button class="btn btn-primary" name="save_button" value="Opslaan" id="send"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]tickets.buttons.update">Update</span></button>
                        <button class="btn btn-success hidden" name="save_button" value="VerzendenOpnieuw" id="btn_send_opnieuw"  ><i class="fa fa-envelope"></i> <span data-i18n="[html]tickets.buttons.re_send">Update</span></button>
                        <button class="btn btn-primary hidden"  name="save_button" value="Verzenden"><i class='fa fa-envelope fa-fw'></i> <span data-i18n="[html]tickets.buttons.send_fast">Onmiddelijk verzenden</span></button>
                        <button class="btn btn-warning hidden"  name="save_button" value="Doorzetten" id="btn_externe_partij"><i class='fa fa-arrow-right fa-fw'></i> <span data-i18n="[html]tickets.buttons.send_too">Doorzetten</span></button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<?php
array_push($arr_js, '/public/js/datepicker/bootstrap-datepicker.js');
array_push($arr_js, '/public/js/datepicker/locales/bootstrap-datepicker.'.strtolower(Config::APP_LANG).'.js');

foreach($arr_js as $js){
    echo '<script src="'.$js.'"></script>';
}
?>

<script>
    $(document).ready(function() {

        var url_str = $('#url_string').val();

        loadInfo();
        loadUpdates();

        // Datum/tijd begin
        $('input[name="datum_on_hold"]').datepicker({
            todayBtn: "linked",
            keyboardNavigation: true,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy",
            language: $('html').attr('lang').toLowerCase()
        })
            .on('changeDate', function(e){
                $('input[name="datum_eind"]').val($(this).val());
                $('#NewBezoekForm').formValidation('revalidateField','datum_begin');
            });


        var lang_code = $('html').attr('lang').toLowerCase()+'_'+$('html').attr('lang').toUpperCase();
        var button;
        $('button[name="save_button"]').click(function() {
            button = $(this).attr('value');
            //alert(button);
        });

        $('#update_wb_form').formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            locale: lang_code,
            fields: {
                actie: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                extra_comment: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                status: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                extra_comment_update: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                status_update: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                reden_geannuleerd: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                doorzetten_naar: {
                    validators: {
                        notEmpty: {

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

                var json_url;

                if(button == "Opslaan") {
                    json_url = "/tickets/update";
                } else if(button == "Verzenden"){
                    json_url = "/tickets/send";
                } else if(button == "VerzendenOpnieuw"){
                    json_url = "/tickets/send/again";
                } else if(button == "Doorzetten"){
                    json_url = "/tickets/send/extern";
                } else {
                    json_url = '';
                }

                $.ajax({
                    type: "POST",
                    url: json_url,
                    data: $('#update_wb_form').serialize(),
                    success: function(data){
                        swal({
                            html:true,
                            title: data.title,
                            text: data.body,
                            type: data.type
                        });
                        loadInfo();
                        loadUpdates();
                        fv.resetForm();
                        //$('input').val('');
                        $('textarea').val('');

                    },
                    error: function(xhr, status, error){
                        swal({
                            html:true,
                            title: json.title,
                            text: json.msg,
                            type: "error"
                        });
                    }
                });
            });

        $('.chat-discussion').slimScroll({
            height: '500px',
            railOpacity: 0.4,
            wheelStep: 10
        });

        $('#status_update').on('change', function() {
            var val = this.value;

            if(val=='Gesloten'){
                $("#gesloten_div").css("display", "block");
            } else {
                $("#gesloten_div").css("display", "none");
            }

            if(val=='Opnieuw verzonden'){
                $("#btn_send_opnieuw").removeClass("hidden");
            } else {
                $("#btn_send_opnieuw").addClass("hidden");
            }

            if(val=='Totale_uitval'){
                $("#btn_send").removeClass("hidden");
            } else {
                $("#btn_send").addClass("hidden");
            }

            if(val=='Geannuleerd'){
                $("#geannuleerd_div").css("display", "block");
            } else {
                $("#geannuleerd_div").css("display", "none");
            }
            if(val=='On hold'){
                $("#on_hold_div").css("display", "block");
            } else {
                $("#on_hold_div").css("display", "none");
            }
            if(val=='Doorzetten'){
                $("#btn_externe_partij").removeClass("hidden");
                $("#doorzet_div").css("display", "block");
            } else {
                $("#btn_externe_partij").addClass("hidden");
                $("#doorzet_div").css("display", "none");
            }
        });

        $('select[name="doorzetten_naar"]').on('change', function() {
            var text = i18n.t('tickets.select.label.sent_to');
            $("#btn_externe_partij").html('<i class=\'fa fa-arrow-right fa-fw\'></i>'+text + ' ' + this.value);
        });

    });

    function loadUpdates(){
        var ticket_id = $('#ticket_id').val();
        $.ajax({
            type: "GET",
            url: '/tickets/get/updates/'+ ticket_id,
            success: function(data){
                $('.chat-discussion').html(data);
            },
            error: function(data){
                console.log(ticket_id + ' not found');
            }
        });

    }
    function loadInfo(){
        var ticket_id = $('#ticket_id').val();
        $.ajax({
            type: "GET",
            url: '/tickets/get/info/'+ ticket_id,
            success: function(data){
                $('#ticket_info').html(data);
                $.i18n.init({
                    resGetPath: '/src/lang/__lng__.json',
                    load: 'unspecific',
                    fallbackLng: false,
                    lng: $('html').attr('lang')
                }, function (t){
                    $('#i18container').i18n();
                });
            },
            error: function(data){
                console.log(ticket_id + ' not found');
            }
        });
    };

</script>	