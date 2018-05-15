    <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
			<!-- Logging view -->
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="ibox float-e-margins">
                  <div class="ibox-title">									  
                      <h5>Log naam <span  id="log_name"><?= $obj['response']['current_log'];?></span> <small><b>Folder:</b> <?= date("Y");?></small></h5>
                    <div class="clearfix"></div>
                  </div>
                  <div class="ibox-content" id="log" ></div>
                </div>
              </div>
			  <!-- /Logging view -->
			</div>	
            <div class="row">
			<!-- Logging history -->
              <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="ibox float-e-margins">
                  <div class="ibox-title">
                    <h5>Log files<small></small></h5>

                    <div class="clearfix"></div>
                  </div>
                  <div class="ibox-content" >

					<table id='datatable-log' class='table'>
					<thead><th align='left'>Datum:</th></thead>
                        <?= $obj['response']['logs'];?>
					</table>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="ibox float-e-margins">
                  <div class="ibox-title">
                    <h5>Error log files<small></small></h5>
                    <div class="clearfix"></div>
                  </div>
                  <div class="ibox-content" >
				    <table id='datatable-error' class='table'>
					<thead><th align='left'>Datum:</th></thead>
                        <?= $obj['response']['error_logs'];?>
					</table>
                  </div>
                </div>
              </div>			  
			  <!-- /Logging history -->
			</div>
        <input type="text" value="<?= $obj['response']['current_log'];?>" id="get_log_name" hidden />
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
			
		loadUpdates();

        setInterval( function () {
            loadUpdates();
        }, 5000 );

		$.extend( true, $.fn.dataTable.defaults, {
			language: {
				url: '/public/js/dataTables/'+$('html').attr('lang')+'.json'
			},
			iDisplayLength: 5,
			deferRender: true,
			order: [[ 0, "desc"]],
			lengthMenu: [ 5, 10, 20, 25 ],
			dom: '<"html5buttons"B>lTfgitp',
			buttons: [
			    {extend: 'copy'},
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
			]			
		} );
			
		$("#datatable-log").DataTable();
		$("#datatable-error").DataTable();
	
	});
	
	function setLogFile(file_name){
		$('#get_log_name').val(file_name);
		$('#log_name').html(file_name);
		loadUpdates();
	}
	function setErrorLogFile(file_name){
		$('#get_log_name').val(file_name);
		$('#log_name').html(file_name);
		loadUpdates();
	}
	function loadUpdates(){
		var file_name = $('#get_log_name').val();

		if(file_name != '')
		{
			$.ajax({
				type: "POST",
				url: '/logging/file/',
				data: {file: file_name},
				success: function(data){
						$('#log').html(data);
				},
				error: function(data){
					console.log(file_name + ' not found');
				}
			});		   	   
		}
	}

    </script>