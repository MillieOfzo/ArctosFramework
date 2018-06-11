	<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5><span data-i18n="[html]tickets.label"> Tickets</span> <small></small></h5>
						</div>
						<div class="ibox-content">

						<div class="row">
							<div class="col-md-2 col-sm-12 col-xs-12 form-group">				  
								<select id="filter_dienst" class="select2 form-control" data-hide-disabled="true" data-live-search="true" data-size="10" data-width="100%" title="Maak een keuze">
									<option value=""></option>
									<option value="Brand">Brand</option>
									<option value="ING">ING</option>
									<option value="DIGI">DIGI</option>
									<option value="RAC">RAC</option>
								</select>
							</div>
							<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
								<select id="filter_extern" class="select2 form-control" data-hide-disabled="true" data-live-search="true" data-size="10" data-width="100%" title="Maak een keuze">
                                    <?= $obj['response']['tickets_external']; ?>
								</select>
							</div>
							<div class="col-md-2 col-sm-12 col-xs-12 form-group">	
								<select id="filter_status" class="select2 form-control" data-hide-disabled="true" data-live-search="true" data-size="10" data-width="100%" title="Maak een keuze">
									<option value=""></option>
									<option value="Aangevraagd">Aangevraagd</option>
									<option value="Open">Open</option>
									<option value="On hold">On hold</option>
									<option value="Totaal uitval">Totaal uitval</option>
									<option value="Gesloten">Gesloten</option>
									<option value="Geannuleerd">Geannuleerd</option>
								</select>		
							</div>
							<div class="col-md-2 col-sm-12 col-xs-12 pull-right">
								<a class="btn btn-primary pull-right" href="/tickets/new"><i class="fa fa-plus"></i> <span data-i18n="[html]tickets.buttons.new">New</span> </a>
							</div>							
						</div>						
						<table id='datatable-all' class='table table-hover jambo_table bulk_action' style="width:100%">
							<thead>
								<th data-i18n="[html]tickets.table.th1">TicketID</th>
								<th data-i18n="[html]tickets.table.th2">LocationID</th>
								<th data-i18n="[html]tickets.table.th3">Locatie</th>
								<th data-i18n="[html]tickets.table.th5">Dienst</th>
								<th data-i18n="[html]tickets.table.th6">Bon voor</th>
								<th data-i18n="[html]tickets.table.th7">Storing</th>
								<th data-i18n="[html]tickets.table.th8">Ticketnr</th>
								<th data-i18n="[html]tickets.table.th9">Aangemaakt op</th>
								<th data-i18n="[html]tickets.table.th10">Status</th>
							</thead>
						</table>

						</div>
					</div>
				
            </div>
        </div>
    </div>
	
	
	<?php
		// View specific scripts
		array_push($arr_js, '/public/js/dataTables/datatables.min.js');
		
	?>		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.$js.'"></script>';
		}
	?>
    <script>
    $(document).ready(function() {
		var url_str = $('#url_string').val();
		var lang_code = $('html').attr('lang');

        // Datatables defaults
        $.extend( true, $.fn.dataTable.defaults, {
            language: {
                url: '/public/js/dataTables/' + $('html').attr('lang')+'.json'
            },
            iDisplayLength: 10,
            deferRender: true,
            order: [[ 0, "desc"]],
            lengthMenu: [ 10, 20, 25 ],
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
        } );

		//Function voor het filteren van de data in table
		$("#filter_dienst").on('change', function() {
			$("#datatable-all, #datatable-open, #datatable-close, #datatable-annu, #datatable-kpn, #datatable-onhold, #datatable-actie").DataTable().column(4).search($(this).val()).draw();
		});
		$("#filter_extern").on('change', function() {
			$("#datatable-all, #datatable-open, #datatable-close, #datatable-annu, #datatable-kpn, #datatable-onhold, #datatable-actie").DataTable().column(5).search($(this).val()).draw();
		});
		$("#filter_status").on('change', function() {
			$("#datatable-all, #datatable-open, #datatable-close, #datatable-annu, #datatable-kpn, #datatable-onhold, #datatable-actie").DataTable().column(8).search($(this).val()).draw();
		});		
		
		var interval;
		var table_active = $("#datatable-all").DataTable({	
			ajax: '/tickets/table',
            order: [[ 8, "desc"]],
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                { extend: 'copy'},
                {extend: 'csv'},
                {extend: 'excel', title: 'ExampleFile'},
                {extend: 'pdf', title: 'ExampleFile'},

                {extend: 'print',
                    customize: function (win){
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ],
			fnInitComplete: function(oSettings, json) {
				$('#ibox1').children('.ibox-content').toggleClass('sk-loading');

				clearInterval(interval);

				interval = setInterval( function () {
					table_active.ajax.reload( null, false ); 
				}, 10000 );

				var lang_code = $('html').attr('lang');
				$.i18n.init({
					resGetPath: '/src/lang/__lng__.json',
					load: 'unspecific',
					fallbackLng: false,
					lng: lang_code
				}, function (t){
					$('#i18container').i18n();
				});				
				
			}			
		});
				
	});
	</script>