<?php
namespace App\Models;

use \SafeMySQL;
use App\Classes\Logger;

class TicketModel
{
    private $conn;

    function __construct()
    {
        $this->conn = new SafeMySQL;
    }

    public function getTicketRow($ticket_id)
    {
        try
        {
            $row = $this->conn->getRow("SELECT * FROM app_customer_tickets WHERE ticket_id = ?i", $ticket_id);
            return $row;
        }
        catch(Exception $ex)
        {
            return self::logError($ex);
        }
    }

    public function updateTicket($query_data, $ticket_id)
    {
        try
        {
            $this->conn->query("UPDATE app_customer_tickets SET ?u  WHERE ticket_id = ?i", $query_data, $ticket_id);
            return true;
        }
        catch(Exception $ex)
        {
            return self::logError($ex);
        }
    }

    public function insertTicketText($query_data)
    {
        try
        {
            $this->conn->query("INSERT INTO app_customer_tickets_updates SET ?u", $query_data);
            return true;
        }
        catch(Exception $ex)
        {
            return self::logError($ex);
        }
    }

    public function getExternal($external_id)
    {
        try
        {
            return $this->conn->getOne("SELECT external_name FROM app_customer_tickets_external WHERE external_id = ?s",$external_id);
        }
        catch(Exception $ex)
        {
            return self::logError($ex);
        }
    }

    private static function logError($exception)
    {
        $msg = 'Regel: ' . $exception->getLine() . ' Bestand: ' . $exception->getFile() . ' Error: ' . $exception->getMessage();
        Logger::logToFile(__FILE__, 1, $msg);
        return false;
    }
}

