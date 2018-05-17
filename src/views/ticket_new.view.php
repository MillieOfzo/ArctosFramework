
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><span data-i18n="[html]tickets.create.label">Create ticket</span> <small>#<?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?></small></h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form name="new_wb" id="NewWerkbonForm" >

                            <div class="col-md-12">
                                <div class="ln_solid"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="first-name"><span data-i18n="[html]tickets.create.txt.scs">SCS nr</span><font color="red">*</font></label>
                                    <input type="text" class="form-control" name="OMS" id="autocomplete-custom-append" value="<?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?>"  data-inputmask="'mask': '99-99-999999'" maxlength="12" data-i18n="[placeholder]placeholders.input" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="first-name"><span data-i18n="[html]tickets.create.txt.service">Dienst</span><font color="red">*</font></label>
                                    <input type="text" class="form-control" name="dienst" id="DIENST"  data-i18n="[placeholder]placeholders.input"   >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="first-name"><span data-i18n="[html]tickets.create.txt.location">Locatie</span><font color="red">*</font></label>
                                    <input type="text" class="form-control" name="locatie" id="LOCATIE"  data-i18n="[placeholder]placeholders.input" value="<?= @$row['SCS_Account_Address_Name']; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="first-name"><span data-i18n="[html]tickets.create.txt.address">Adres</span><font color="red">*</font></label>
                                    <input type="text" class="form-control" name="adres"  id="ADRES" data-i18n="[placeholder]placeholders.input"  value="<?= @$row['SCS_Account_Address_Address']; ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" ><span data-i18n="[html]tickets.create.txt.zipcode">Postcode</span><font color="red">*</font></label>
                                    <input type="text" class="form-control" name="postcode" id="POSTCODE"  data-i18n="[placeholder]placeholders.input" value="<?= @$row['SCS_Account_Address_Zip']; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label"><span data-i18n="[html]tickets.create.txt.city">Plaats</span><font color="red">*</font></label>
                                    <input type="text" class="form-control" name="plaats" id="PLAATS" data-i18n="[placeholder]placeholders.input" value="<?= @$row['SCS_Account_Address_City']; ?>">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="ln_solid"></div>
                            </div>
                            <div id="txt"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" ><span data-i18n="[html]tickets.create.txt.for">Werkbon voor</span><font color="red">*</font></label>
                                    <select id="voor" name="extern" class="select2 form-control"  >
                                        <option value="" ></option>

                                    </select>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" ><span data-i18n="[html]tickets.create.txt.storing">Storing</span><font color="red">*</font></label>
                                    <div id="storing_wijzigen" >
                                        <select name="storing" class="select2 form-control" onchange="Storing(this.value)"  id="storing_select">
                                            <option value=""></option>

                                            <option data-i18n="[html]tickets.create.malfunction.anders" value="anders">Anders</option>
                                        </select>
                                    </div><span id="searchclear" style="display:none;" class="fa fa-remove"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" ><span data-i18n="[html]tickets.create.txt.action">Actie</span><font color="red">*</font></label>
                                    <input type="text" class="form-control"	name="actie" data-i18n="[placeholder]placeholders.input">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-6" id="CP" style="display:block">
                                <div class="form-group">
                                    <label class="control-label"><span data-i18n="[html]tickets.create.txt.cp">Contactpersoon</span><font color="red">*</font></label>
                                    <input type="text" class="form-control" name="cp" id="CP_input" data-i18n="[placeholder]placeholders.input">
                                </div>
                            </div>
                            <div class="col-md-6" id="CPTEL" style="display:block">
                                <div class="form-group">
                                    <label class="control-label" ><span data-i18n="[html]tickets.create.txt.cptel">Contactpersoon telnr</span><font color="red">*</font></label>
                                    <input type="text" class="form-control" name="cptel" id="CPTEL_input" data-i18n="[placeholder]placeholders.input" >
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"> <span data-i18n="[html]tickets.create.txt.comment">Extra commentaar</span></label>
                                    <textarea name="comment" class="resizable_textarea form-control"  data-i18n="[placeholder]placeholders.input"></textarea>
                                </div>
                            </div>

                            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['_token'], ENT_QUOTES, 'UTF-8');?>">

                            <div class="col-md-12">
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <input type="text" hidden name="regio" id="REGIO" >
                                    <div id="div_send_button" style="display:block">
                                        <button class="btn btn-primary" name="save_button" value="Verzenden" id="send"><i class='fa fa-envelope fa-fw'></i> <span data-i18n="[html]tickets.buttons.send">Send</span></button>
                                    </div>
                                    <div id="div_send_totaaluitval" style="display:none">
                                        <input type="text" id="totaal_uitval" name="totaal_uitval"/>
                                        <button type="submit" class="btn btn-warning" name="save_button" value="Verzenden"><i class='fa fa-envelope fa-fw'></i> Verzenden</button>
                                    </div>
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
foreach($arr_js as $js){
    echo '<script src="'.$js.'"></script>';
}
?>

<script>
    $(document).ready(function() {

        $("#searchclear").click(function(){
            var clear	= document.getElementById('searchclear');

            document.getElementById('storing_wijzigen').innerHTML = '<select name="storing" class="select2 form-control" onchange="Storing(this.value)" ><option value="">Selecteer...</option>	<option value="Geen lijnsync">Geen lijnsync</option><option value="GPRS uitval">GPRS uitval</option>	<option value="RAM uitval">RAM uitval</option>	<option value="ACCU uitval">ACCU uitval</option>	<option value="Voeding uitval">Voeding uitval</option>	<option value="Router uitval">Router uitval</option>	<option value="NVR comm uitval">NVR comm uitval</option>	<option value="Totale uitval">Totale uitval</option><option value="anders">Anders</option>	</select></div>',
                clear.style.display='none';
        });
        var url_str = $('#url_string').val();
        var lang_code = $('html').attr('lang').toLowerCase()+'_'+$('html').attr('lang').toUpperCase();
        var lang_tel_code = $('html').attr('lang').toUpperCase();
        var button;
        $('button[name="save_button"]').click(function() {
            button = $(this).attr('value');
            //alert(button);
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
                OMS: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                dienst: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                locatie: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                adres: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                postcode: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                plaats: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                extern: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                storing: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                actie: {
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                cp: {
                    enabled: false,
                    validators: {
                        notEmpty: {

                        }
                    }
                },
                cptel: {
                    enabled: false,
                    validators: {
                        phone: {
                            country: 'NL',
                            message: 'The value is not valid %s phone number'
                        }
                    }
                },
                ticketnr: {
                    enabled: true,
                    validators: {
                        notEmpty: {

                        }
                    }
                }
            }
        })
        // Indien er een CP naam ingevoerd wordt voer validatie uit
            .on('keyup', '[name="cp"]', function() {
                var isEmpty = $(this).val() == '';
                $('#NewWerkbonForm').formValidation('enableFieldValidators', 'cp', !isEmpty).formValidation('enableFieldValidators', 'cptel', !isEmpty);
                // Revalidate input veld als de user begint te typen
                if ($(this).val().length == 1) {
                    $('#NewWerkbonForm').formValidation('validateField', 'cp').formValidation('validateField', 'cptel');
                }
            })
            .on('keyup', '[name="ticketnr"]', function() {
                var isEmpty = $(this).val() == '';
                $('#NewWerkbonForm').formValidation('enableFieldValidators', 'ticketnr', !isEmpty);
                // Revalidate input veld als de user begint te typen
                if ($(this).val().length == 1) {
                    $('#NewWerkbonForm').formValidation('validateField', 'ticketnr');
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

                if(button == "Verzenden"){
                    json_url = "/Src/controllers/ticket.controller.php?new";
                } else if(button == "Totaal_uitval"){
                    json_url = "/Src/controllers/ticket.controller.php?send=totaal";
                } else {
                    json_url = '';
                }

                $.ajax({
                    type: "POST",
                    url: json_url,
                    data: $('form[name="new_wb"]').serialize(),
                    success: function(data){
                        swal({
                            html:true,
                            title: data.title,
                            text: data.body,
                            type: data.type
                        });
                    },
                    error: function(xhr, status, error){
                        var json = $.parseJSON(xhr.responseText);
                        console.log(json);
                        swal({
                            html:true,
                            title: json.title,
                            text: json.msg,
                            type: "error"
                        });
                    }
                });
            });
// ==========================================================================================================
//	AUTOCOMPLETE	
// ==========================================================================================================
//	DROPDOWN
// ==========================================================================================================	
        $(document).ready(function() {
            $('#autocomplete-custom-append').autocomplete({
                serviceUrl: '/src/helpers/scs_naw_hint.json.php',
                max: 10,
                onSearchComplete: function (query, suggestions) {
                    if(!suggestions.length) {
                        $('#Error').modal('show');
                    }
                }
            });
        });
//	GET LOCATION VALUES BASED ON AUTOCOMPLETE
// ==========================================================================================================	
        $(function(){

            $('#autocomplete-custom-append').on('keyup', fillForm);
            $('.autocomplete-suggestions').on('mouseleave', fillForm);

            function fillForm(){
                var inpval=$('#autocomplete-custom-append').val();
                var elementCP=document.getElementById('CP');
                var elementCPTEL=document.getElementById('CPTEL');
                $.ajax({
                    type: 'POST',
                    data: ({p : inpval}),
                    url: '/src/helpers/scs_naw_get.json.php',
                    success: function(data) {

                        var object = $.parseJSON(data);
                        $.each(object, function () {
                            $.each(this, function (name, value) {
                                if (name == "omsnr") {
                                    $("#OMSNR").val(value);
                                } else if (name == "dienst") {
                                    $("#DIENST").val(value);
                                } else if (name == "locatie") {
                                    $("#LOCATIE").val(value);
                                } else if (name == "adres") {
                                    $("#ADRES").val(value);
                                } else if (name == "postcode") {
                                    $("#POSTCODE").val(value);
                                } else if (name == "plaats") {
                                    $("#PLAATS").val(value);
                                } else if (name == "regio") {
                                    $("#REGIO").val(value);
                                }

                            });
                        });
                    }
                });
            };
        });

    });

    function Storing(val){
        var div_send_totaaluitval 	= document.getElementById('div_send_totaaluitval'),
            div_send_button			= document.getElementById('div_send_button'),
            searchclear	 			= document.getElementById('searchclear');

        if(val=='Totale uitval')
            $('#totaal_uitval').val(1),
                div_send_totaaluitval.style.display='block',
                div_send_button.style.display='none',
                $('#header').removeClass('modal-header-info'),
                $('#header').addClass('modal-header-warning');
        else if (val=='anders')
            document.getElementById('storing_wijzigen').innerHTML = '<input id="searchinput" type="type" name="storing" class="form-control" data-i18n="[placeholder]placeholders.input">',
                searchclear.style.display='block',
                div_send_totaaluitval.style.display='none',
                div_send_button.style.display='block',
                $('#header').removeClass('modal-header-warning'),
                $('#header').addClass('modal-header-info');
        else
            div_send_totaaluitval.style.display='none',
                div_send_button.style.display='block',
                $('#header').removeClass('modal-header-warning'),
                $('#header').addClass('modal-header-info');
    }
</script>