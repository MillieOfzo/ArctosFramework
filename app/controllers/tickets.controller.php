<?php
 
namespace App\Controllers;

use \Config;
use \SafeMySQL;
use App\Classes\Logger;
use App\Classes\Helper;
use App\Classes\Auth;
use App\Classes\Mailer;
use App\Classes\SSP;
use App\Models\TicketModel;

class TicketsController extends BaseController
{
    private $succesMessage;

    function __construct()
    {
		parent::__construct();
        $this->conn = new SafeMySQL;
        $this->ticket = new TicketModel;
        $this->auth_user = htmlentities($_SESSION[Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
        $this->purifier = new \HTMLPurifier(\HTMLPurifier_Config::createDefault());
    }

	public function index()
	{
        return array(
            'tickets_external' => $this->getTicketExternal(),
            'tickets_malfunctions' => $this->getTicketMalfunctions()
        );
	}

	public function newTicket()
    {
        return array(
            'view' => '../src/views/ticket_new.view.php',
            'tickets_external' => $this->getTicketExternal(),
            'tickets_malfunctions' => $this->getTicketMalfunctions(),
        );
    }

    public function updateView($ticket_id)
    {
        $row = $this->ticket->getTicketRow($ticket_id);

        if ($row["ticket_on_hold"] == 1 && $row["ticket_date_on_hold"] != '') {
            $display_hold 	= "display:block;";
        } else {
            $display_hold 	= "display:none;";
        }
        if ($row["ticket_status"] == "Geannuleerd" && $row["ticket_sub_status"] != '') {
            $display_geannuleerd 	= "display:block;";
        } else {
            $display_geannuleerd 	= "display:none;";
        }
        $on_hold_date 		= ($row['ticket_date_on_hold'] == NULL || $row['ticket_date_on_hold'] == "0000-00-00") ? date('d-m-Y') : date('d-m-Y', strtotime($row['ticket_date_on_hold']));


        return array(
            'view' => '../src/views/ticket_view.view.php',
            'tickets_malfunctions' => $this->getTicketMalfunctions(),
            'returned_ticket_row' => $row,
            'display_hold' => $display_hold,
            'on_hold_date' => $on_hold_date,
            'display_geannuleerd' => $display_geannuleerd,
        );
    }

    public function updateTicket()
    {
        Auth::checkCsrfToken($_POST['csrf']);
        $cleaned_ticket_id = $this->purifier->purify($_POST['ticket_id']);

        $row_wb = $this->ticket->getTicketRow($cleaned_ticket_id);

        // Roep status text functie aan; regelt de sub-labels in het user commentaar
        $status_text = date("D d M Y H:i:s") . ' ';

        // Indien $ticketnr niet leeg wordt de post value van $ticketnr toegevoegd aan $status_text
        // voeg status status_text toe aan message.
        if (!empty($post_val['ticketnr']))
        {
            $status_text .= 'Ticketnr ' . strtolower($row_wb['ticket_external_ticket_nr']) . ': ' . $_POST['ticketnr'] . ' ';
        }

        if ($_POST['status_update'] == "On hold")
        {
            $status_text .= '<span class="badge badge-warning">Bon on hold tot ' . date('d-m-Y', strtotime($_POST['datum_on_hold'])) . '</span> ';
        }
        elseif ($_POST['status_update'] == "Doorzetten")
        {
            $status_text .= '<span class="badge badge-info">Bon doorgezet naar ' . $_POST['doorzetten_naar'] . '</span> ';
        }
        elseif ($_POST['status_update'] == "Opnieuw geopend")
        {
            $status_text .= '<span class="badge badge-info">Bon opnieuw geopend</span> ';
        }
        elseif ($_POST['status_update'] == 'Opnieuw verzonden')
        {
            $status_text .= '<span class="badge badge-info">Opnieuw verzonden naar ' . $row_wb['ticket_external_ticket_nr'] . '</span>';
        }
        elseif ($_POST['status_update'] == "Geannuleerd")
        {
            $status_text .= '<span class="badge badge-danger">Bon geannuleerd ' . $_POST['reden_geannuleerd'] . '</span> ';
        }
        elseif ($_POST['status_update'] == "Gesloten")
        {
            $status_text .= '<span class="badge badge-success">Bon gesloten</span> ';
        }
        elseif ($_POST['status_update'] == 'Open' && @$_POST['wb_soort'] == 'CONTROLE')
        {
            $status_text .= '<span class="badge badge-info">Verzonden naar ' . $row_wb['ticket_external_ticket_nr'] . '</span>';
        }
        else
        {
            $status_text .= '';
        }

        //Indien status geloten is worden de initialen van de sessie meegestuurd
        if ($_POST['status_update'] == "Gesloten")
        {
            $gesloten_door = $this->auth_user;
            $gesloten = 1;
            $date_gesloten = date("Y-m-d H:i:s");
        }
        else
        {
            $gesloten_door = "";
            $gesloten = 0;
            $date_gesloten = "0000-00-00 00:00:00";
        }

        $update_txt_query = array(
            'ticket_nr' => $row_wb['ticket_nr'],
            'ticket_update_text_label' => $status_text,
            'ticket_update_by' => $this->auth_user,
            'ticket_update_date' => date("Y-m-d H:i:s") ,
            'ticket_update_text' => strip_tags(ucfirst($_POST['extra_comment_update']))
        );

        // Key moeten hetzelfde zijn als de kolom namen uit de database.
        $query_data = array(
            'ticket_changed_date' => date("Y-m-d H:i:s") ,
            'ticket_changed_by' => $this->auth_user,
            'ticket_closed_date' => $date_gesloten,
            'ticket_closed_by' => $gesloten_door,
            'ticket_closed' => $gesloten,
            'ticket_external_ticket_nr' => empty($_POST['ticketnr']) ? $row_wb['ticket_external_ticket_nr'] : $_POST['ticketnr'],
            'ticket_extra_comment' => strip_tags($_POST['extra_comment_update']) ,
            'ticket_status' => $this->setStatus($_POST) ,
            'ticket_updates' => 1
        );

        // Indien status Geannuleerd
        if ($_POST['status_update'] == "Geannuleerd")
        {
            $query_data['ticket_sub_status'] = $_POST['reden_geannuleerd'];
            $msg_type = 'success';
            $msg_title = 'Succes';
            $this->succesMessage .= "Werkbon geannuleerd omdat: <b>" . $_POST['reden_geannuleerd'] . "</b><br>";
        }

        // Indien status Doorzetten
        if ($_POST['status_update'] == "Doorzetten")
        {
            $door_naar = $this->ticket->getExternal($_POST['doorzetten_naar']);

            $query_data['ticket_extern'] = $door_naar;
            $query_data['ticket_put_through'] = 1;
            $query_data['ticket_put_through_too'] = $door_naar;
            $msg_type = 'success';
            $msg_title = 'Succes';
            $this->succesMessage .= "Werkbon doorgezet naar: <b>" . $door_naar . "</b><br>";
        }

        // Indien status on hold
        if ($_POST['status_update'] == "On hold")
        {
            $query_data['ticket_on_hold'] = 1;
            $query_data['ticket_date_on_hold'] = date('Y-m-d', strtotime($_POST['datum_on_hold']));
            $msg_type = 'success';
            $msg_title = 'Succes';
            $this->succesMessage .= "Werkbon on hold tot: <b>" . date('d-m-Y', strtotime($_POST['datum_on_hold'])) . "</b><br>";
        }
        else
        {
            $query_data['ticket_on_hold'] = 0;
        }

        // Indien een totaal uitval gesloten wordt stuur herstel mail naar brandweer
        if ($row_wb['ticket_status'] != "Gesloten" && $_POST['status_update'] == "Gesloten" && $_POST['totaal_uitval'] == 1)
        {
            // Send mail naar brandweer wanneer totaal uitval
            //$send_mail_brandweer = $this->mailBw($row_wb, $query_data, 1);
            //$this->succesMessage .= $send_mail_brandweer['succesmessage'];

        }

        // Als de bon status 'gesloten' is voer de query NIET uit
        if ($row_wb['ticket_status'] == 'Gesloten' && $_POST['status_update'] == 'Gesloten')
        {
            // Denied message als de bon status gesloten is, refreshed page na 5 sec
            $msg_type = 'error';
            $msg_title = 'DENIED';
            $this->succesMessage .= "De bon is al gesloten. Updates worden niet meer opgeslagen.<br>Open de bon opnieuw indien nodig.<br>";

        }
        else
        {

            if ($row_wb)
            {
                $this->ticket->updateTicket($query_data, $_POST['ticket_id']);
                $this->ticket->insertTicketText($update_txt_query);

                $msg_type = 'success';
                $msg_title = 'Succes';
                $this->succesMessage .= "Succesvol werkbon <b>ASB-WB" . $_POST['ticket_id'] . "</b> geupdatet";

            }
        }

        if ($_POST['status_update'] == "Geannuleerd")
        {
            // Send mail naar aanvrager
            //$send_mailAanvrager = $this->mailAanvrager($row_wb, $query_data);
            //$this->succesMessage .= $send_mailAanvrager['succesmessage'];

        }

        // Log to file
        $msg = "WerkbonID: " . $_POST['ticket_id'] . ". " . $row_wb['ticket_customer_scsnr'] . " geupdatet door: " . $query_data['ticket_changed_by'] . " Status: " . $_POST['status_update'];
        $err_lvl = 0;
        // JSON response
        $response_array['type'] = $msg_type;
        $response_array['title'] = $msg_title;
        $response_array['body'] = $this->succesMessage;
        Logger::logToFile(__FILE__, $err_lvl, $msg);

        // Return JSON array
        Helper::jsonArr($response_array);

    }

    public function getTicketUpdates($ticket_id)
    {

	    $search = preg_replace("/[^A-Z0-9-]/","", $ticket_id);
	    $row_updates = $this->conn->getAll("SELECT * FROM app_customer_tickets_updates WHERE ticket_nr = ?s ORDER BY ticket_update_id DESC",'ASB-WB'.$search);
        $updates_text = '';
	    foreach($row_updates as $updates){

            if( $updates['ticket_update_by'] == htmlentities($_SESSION[Config::SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8')){
                $position 	= 'right';
                $img 		= '/public/img/img_green.jpg';
            }else {
                $position   = 'left';
                $img 		= '/public/img/img_blue.jpg';
            }
            $updates_text .= '<div class="chat-message '.$position.'">
                <img class="message-avatar" src="'.$img.'" alt="">
                <div class="message">
                    <a class="message-author" href="#"> '.$updates['ticket_update_by'].' </a>
	    			<span class="message-date"> '.stripslashes($updates['ticket_update_text_label']).' </span>
                    <span class="message-content">
	    			'.nl2br(stripslashes($updates['ticket_update_text'])).'
                    </span>
                </div>
            </div>';
        }

        Helper::jsonArr(array($updates_text));

    }

    public function getTicketInfo($ticket_id)
    {

		$search = preg_replace("/[^0-9]/","", $ticket_id);
		$row = $this->conn->getRow("SELECT * FROM app_customer_tickets WHERE ticket_nr = ?s",'ASB-WB'.$search);

		if($row['ticket_external_ticket_nr'] == "") {
			$ticket 		= "";
			$ticket_naam 	= "";
		} else {
			$ticket 		= $row['ticket_external_ticket_nr'];
			$ticket_naam 	= "Ticketnr:";
		}

		$label_submit_again 	= ($row['ticket_submit_again'] == 1) ? '<span data-toggle="tooltip" title="Opnieuw verzonden" class="label label-info"><span class="fa fa-envelope"></span></span>' : '';

		if (strtotime($row['ticket_created_date']) < strtotime('-14 day') && $row['ticket_status'] == "Open") {
            $status_text 		= '14 '.$this->lang->tickets->status->longer_than;
			$color 				= 'label-danger';
		} elseif (strtotime($row['ticket_created_date']) < strtotime('-5 day') && $row['ticket_status'] == "Open") {
            $status_text 		= '5 '.$this->lang->tickets->status->longer_than;
			$color 				= 'label-warning';
		} elseif($row['ticket_status'] == "Open" || $row['ticket_status'] == "Opnieuw geopend" || $row['ticket_status'] == "Opnieuw verzonden") {
            $status_text 		= $this->lang->tickets->status->open;
			$color 				= 'label-success';
		} elseif ($row['ticket_status'] == 'Gesloten'){
            $status_text 		= $this->lang->tickets->status->closed . ': ' . $row['ticket_closed_date'];
			$color 				= 'label-default';
		} elseif($row['ticket_status'] == "Aangevraagd") {
            $status_text 		= $this->lang->tickets->status->aangevraagd;
			$color 				= 'label-info';
		} elseif($row['ticket_status'] == "Geannuleerd") {
            $status_text 		= $this->lang->tickets->status->geannuleerd . ": ".$row['ticket_sub_status'];
			$color 				= 'label-danger';
		} elseif($row['ticket_status'] == "Totaal uitval" && $row['ticket_total_failure'] == 1) {
            $status_text 		= $this->lang->tickets->status->totaal_uitval;
			$color 				= 'label-danger';
		} elseif($row['ticket_status'] == "On hold") {
            $status_text 		= $this->lang->tickets->status->on_hold . " " . date('d-m-Y', strtotime($row['ticket_date_on_hold']));
			$color 				= 'label-default';
		} else {
            $status_text 		= $this->lang->tickets->status->unknown;
			$color 				= 'label-default';
		}

			$info =	$this->purifier->purify('<table width="100%">
					<tr>
						<td>
							<label for="OMS"><b>' . $this->lang->tickets->create->txt->scs . '</b></label> 
						</td>
						<td>
							'.$row['ticket_customer_scsnr'] .'
						</td>
						<td>
							<label for="Dienst"><b>' . $this->lang->tickets->create->txt->service . '</b></label> 
						</td>
						<td>
						    '.$row['ticket_service'] .'
						</td>
					</tr>
					<tr>
						<td>
							<label for="locatie"><b>' . $this->lang->tickets->create->txt->location . '</b></label>
						</td>
						<td>	
						    '.$row['ticket_customer_location'] .'
						</td>
						<td>
							<label for="adres"><b>' . $this->lang->tickets->create->txt->address . '</b></label>
						</td>
						<td>				
							'.$row['ticket_customer_address'] .'	
						</td>
					</tr>
					
					<tr>
						<td>
							<label for="postcode"><b>' . $this->lang->tickets->create->txt->zipcode . '</b></label> 
						</td>
						<td>
						    '.$row['ticket_customer_zipcode'] .'				
						</td>
						<td>
							<label for="plaats"><b>' . $this->lang->tickets->create->txt->city . '</b></label>
						</td>
						<td>		
						    '.$row['ticket_customer_city'] .'					
						</td>
					</tr>	
					<tr><td><br></td></tr>
					<tr>
						<td colspan="4">
							<div class="x_title">
								<h2><span>' . $this->lang->tickets->create->txt->for . ': </span> <b>'.$this->conn->getOne("SELECT external_name FROM app_customer_tickets_external WHERE external_id = ?i",$row['ticket_extern'])  .'</b></h2>
							
								<div class="clearfix"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<label for="Ticketnr"><b>'. $ticket_naam .'</b></label>
						</td>
						<td>
							'.$ticket.'
						</td>
					</tr>
						<tr><td><label for="storing"><b>' . $this->lang->tickets->create->txt->storing . '</b></label></td><td colspan="3">'. $row['ticket_failure'] .'</td></tr>
						<tr><td><label for="actie"><b>' . $this->lang->tickets->create->txt->action . '</b></label></td><td colspan="3">'. $row['ticket_action'] .'</td></tr>
					
						<tr><td><label for="cp"><b>' . $this->lang->tickets->create->txt->cp . '</b></label></td><td>'.$row['ticket_cp'] .'</td>
						<td><label for="cptel"><b>' . $this->lang->tickets->create->txt->cptel . '</b></label></td><td>'.$row['ticket_cp_tel'] .'</td></tr>

						<tr><td colspan="4"><label for="comment"><b>' . $this->lang->tickets->create->txt->comment . '</b></label>
						<br>'. $row['ticket_comment'] .'</td></tr>
						
					</table>
					<br>
					<h2><span data-i18n="[html]tickets.update.status">Status:</span> 
					<span class="badge-xl '. $color.' pull-right">'.$status_text.'</span>'. $label_submit_again.'</h2>
					<small class="pull-right">
						<i class="fa fa-clock-o"> </i>
						<span data-i18n="[html]location.tab.update">Last update </span>: '.date('D d M Y, H:i:s',strtotime($row['ticket_changed_date'])).'
					</small>');

		Helper::jsonArr(array($info));
    }

    public function getTableTickets()
    {

        $db = new \PDO('mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8', Config::DB_USER, Config::DB_PASS, array(
            \PDO::ATTR_PERSISTENT => true
        ));

        $columns = array(
            array(
                'db' => "ticket_id",
                'dt' => 'DT_RowClass',
                'formatter' => function ($d, $row)
                {
                    if ($row[10] == "Gesloten")
                    {
                        $tr_row = "alert-box success";
                    }
                    elseif ($row[10] == "Totaal uitval")
                    {
                        $tr_row = "alert-box danger";
                    }
                    elseif ($row[7] == "Geannuleerd")
                    {
                        $tr_row = "alert-box danger";
                        // 14 dagen

                    }
                    elseif (strtotime($row[9]) < strtotime('-14 day'))
                    {
                        $tr_row = "alert-box danger";
                        // 5 dagen

                    }
                    elseif (strtotime($row[9]) < strtotime('-5 day'))
                    {
                        $tr_row = "alert-box warning";
                    }
                    elseif ($row[7] == "Aangevraagd")
                    {
                        $tr_row = "alert-box info";
                    }
                    elseif ($row[7] == "On hold")
                    {
                        $tr_row = "alert-box open";
                    }
                    else
                    {
                        $tr_row = "alert-box open";
                    }
                    return $tr_row;
                }
            ) ,
            array(
                'db' => "ticket_nr",
                'dt' => 0,
                'formatter' => function ($d, $row)
                {
                    $id = preg_replace("/[^0-9]/","",$d);
                    return "<a class='text-navy' href='/tickets/view/" . $id . "' >" . $d . " </a>";
                }
            ) ,
            array(
                'db' => "ticket_customer_scsnr",
                'dt' => 1
            ) ,
            array(
                'db' => "ticket_customer_location",
                'dt' => 2,
                'formatter' => function ($d, $row)
                {
                    return stripslashes(substr($d, 0, 13)) . "...";
                }
            ) ,
            array(
                'db' => "ticket_service",
                'dt' => 3
            ) ,
            array(
                'db' => "ticket_extern",
                'dt' => 4,
                'formatter' => function ($d, $row)
                {
                    return $this->conn->getOne("SELECT external_name FROM app_customer_tickets_external WHERE external_id = ?i",$d);
                }
            ) ,
            array(
                'db' => "ticket_failure",
                'dt' => 5,
                'formatter' => function ($d, $row)
                {
                    return substr($this->conn->getOne("SELECT malfunctions_name FROM app_customer_tickets_malfunctions WHERE malfunctions_id = ?i",$d), 0, 13) . "...";
                }
            ) ,
            array(
                'db' => "ticket_external_ticket_nr",
                'dt' => 6
            ) ,
            array(
                'db' => "ticket_created_date",
                'dt' => 7
            ) ,

            array(
                'db' => "ticket_status",
                'dt' => 8,
                'formatter' => function ($d, $row)
                {
                    if ($d == "Gesloten")
                    {
                        $tr_row = "alert-box primary";
                        $colorstatus = 'label-default';
                        $title = "";
                        $status_txt = $d;
                    }
                    elseif ($d == "Totaal uitval")
                    {
                        $tr_row = "alert-box danger";
                        $colorstatus = "label-danger";
                        $title = "";
                        $status_txt = "Totaal uitval";
                    }
                    elseif ($d == "Geannuleerd")
                    {
                        $tr_row = "alert-box danger";
                        $colorstatus = 'label-danger';
                        $title = "";
                        $status_txt = $d;
                        // 14 dagen

                    }
                    elseif (strtotime($row[8]) < strtotime('-14 day'))
                    {
                        $tr_row = "alert-box danger";
                        $colorstatus = 'label-danger';
                        $title = "Langer dan 14 dagen open";
                        if ($d == "Opnieuw verzonden" || $d == "Opnieuw geopend")
                        {
                            $status_txt = "Open";
                        }
                        else
                        {
                            $status_txt = $d;
                        }
                        // 5 dagen

                    }
                    elseif (strtotime($row[8]) < strtotime('-5 day'))
                    {
                        $tr_row = "alert-box warning";
                        $colorstatus = 'label-warning';
                        $title = "Langer dan 5 dagen open";
                        if ($d == "Opnieuw verzonden" || $d == "Opnieuw geopend")
                        {
                            $status_txt = "Open";
                        }
                        else
                        {
                            $status_txt = $d;
                        }
                    }
                    elseif ($d == "Aangevraagd")
                    {
                        $tr_row = "alert-box info";
                        $colorstatus = 'label-info';
                        $title = "";
                        $status_txt = $d;
                    }
                    elseif ($d == "On hold")
                    {
                        $tr_row = "alert-box open";
                        $colorstatus = 'label-default';
                        $title = "";
                        $status_txt = $d;
                    }
                    else
                    {
                        $tr_row = "alert-box open";
                        $colorstatus = 'label-success';
                        $title = "";
                        $status_txt = "Open";
                    }

                    if ($row[10] == 1)
                    {
                        $submit_again = "<span class='label label-info'><span data-toggle='tooltip' title='Opnieuw verzonden' class='fa fa-envelope'></span></span>";
                    }
                    else
                    {
                        $submit_again = "";
                    }
                    if ($row[11] == 1)
                    {
                        $ingepland = "<span class='label label-default'><span data-toggle='tooltip' title='Ingepland' class='fa fa-calendar'></span></span>";
                    }
                    else
                    {
                        $ingepland = "";
                    }
                    return "<span data-toggle='tooltip' title='" . $title . "' class='label " . $colorstatus . "'>" . $status_txt . "</span> " . $submit_again . " " . $ingepland;
                }
            ) ,
            array(
                'db' => "ticket_submit_again",
                'dt' => 9
            ) ,
            array(
                'db' => "ticket_planned",
                'dt' => 10
            )
        );

        Helper::jsonArr(SSP::complex($_GET, $db, 'app_customer_tickets', 'ticket_id', $columns, $whereResult = null, $whereAll = null));
    }

    private function setStatus($post_val)
    {
        if ($post_val['status_update'] == "Geannuleerd")
        {
            $res = "Geannuleerd";
        }
        elseif ($post_val['status_update'] == "Gesloten")
        {
            $res = "Gesloten";
        }
        elseif ($post_val['totaal_uitval'] == 1)
        {
            $res = "Totaal uitval";
        }
        elseif ($post_val['status_update'] == "Doorzetten")
        {
            $res = "Open";
        }
        elseif ($post_val['status_update'] == "Opnieuw geopend" || $post_val['status_update'] == "Opnieuw verzonden")
        {
            $res = "Open";
        }
        elseif ($post_val['status_update'] == "Open (Not send)")
        {
            $res = "Open";
        }
        else
        {
            $res = $post_val['status_update'];
        }
        return $res;
    }

    private function getTicketExternal()
    {
        $tickets_external ='<option></option>';
        foreach($this->conn->getAll("SELECT * FROM app_customer_tickets_external") as $extern)
        {
            $tickets_external .= '<option value="'.$extern['external_id'].'">'.$extern['external_name'].'</option>';
        }

        return $tickets_external;
    }

    private function getTicketMalfunctions()
    {
        $tickets_malfunctions ='<option></option>';
        foreach($this->conn->getAll("SELECT * FROM app_customer_tickets_malfunctions") as $mal)
        {
            $tickets_malfunctions .= '<option value="'.$mal['malfunctions_id'].'">'.$mal['malfunctions_name'].'</option>';
        }

        return $tickets_malfunctions;
    }
}

