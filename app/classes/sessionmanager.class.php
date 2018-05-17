<?php
/**
 * ARCTOS - Lightweight framework.
 *
 * Session manager class
 *
 * Creates secure sessions. 
 * Regenerates sessions, if sessions is no longer valid the file is marked as obsolete and ready to be cleaned up by the garbage collector
 */
 
namespace App\Classes;

class SessionManager
{

    /**
     * Needs to call the regenerateSession function on new requests and periodically after that, as well as destroy the session if it is invalid. Here is the complete SessionStart function.
     * This function starts, validates and secures a session.
     *
     * @param string $name The name of the session.
     * @param int    $limit Expiration date of the session cookie, 0 for session only
     * @param string $path Used to restrict where the browser sends the cookie
     * @param null   $domain Used to allow subdomains access to the cookie
     * @param bool   $secure If true the browser only sends the cookie over https
     * @throws \Exception
     */
    public function sessionStart($name, $limit = 0, $path = '/', $domain = null, $secure = null)
    {
        // Set the cookie name
        session_name($name . '_Session');

        // Set SSL level
        $https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);

        // Set session cookie options
        session_set_cookie_params($limit, $path, $domain, $https, true);
        session_start();

        // Make sure the session hasn't expired, and destroy it if it has
        if ($this->validateSession())
        {
            // Check to see if the session is new or a hijacking attempt
            if (!$this->preventHijacking())
            {
                // Reset session data and regenerate id
                $_SESSION = array();
                $_SESSION['IPaddress'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
				$_SESSION['_token'] = Csrf::genCsrfToken();

                $this->regenerateSession();

                // Give a 5% chance of the session id changing on any request
                
            }
            elseif (rand(1, 100) <= 5)
            {
                $this->regenerateSession();
            }
        }
        else
        {
            $_SESSION = array();
            session_destroy();
            session_start();

        }
    }
	
	/**
	 * This function checks to make sure a session exists and is coming from the proper host. On new visits and hacking
	 * attempts this function will return false.
	 *
	 * @return bool
	 */
    protected function preventHijacking()
    {
        if (!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent'])) 
			return false;

        if ($_SESSION['IPaddress'] != $_SERVER['REMOTE_ADDR']) 
			return false;

        if ($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']) 
			return false;

        return true;
    }

    /**
     * If an application creates a lot of quick connections to the server some interesting things can happen.
     * PHP, and many other languages, restricts access to the session data to one running script at a time,
     * so if multiple requests come in that try to access the session data the second request (and any other) gets queued up.
     * When the first request changes the ID and deletes the old session the second request still has the old session ID which no longer exists.
     * This results in a third, new session being opened and generally means your user gets logged out.
     *
     * This bug is ridiculously hard to diagnose as the timing of not just the requests but the ID regeneration has to be just right.
     * In sites that don’t make requests to the server using javascript this type of bug may never be encountered at all,
     * as the time between page loads is more than long enough for the browser to have updated its session info.
     * For sites that make use of ajax techniques, however, this issue will have a chance of occurring whenever the session ID is changed.
     *
     * In our final update to the SessionManager class we’re going to write a fix for this problem. Rather than delete the session immediately when we change the ID,
     * we’re going to mark the old session as obsolete and mark it to expire in ten seconds.
     * This way any queued up requests will still have access to the expired session but we don’t have to leave it open forever.
     *
     * To accomplish this we’re going to add the regenerateSession function. This function adds the obsolete flag and expiration to the session,
     * regenerates the ID to create the new session and saves them both.
     * It then reopens the new session and removes the obsolete flag. Unlike our other internal functions we are leaving this one open for use outside the class so that it can be tied into login scripts.
     * @throws \Exception
     */
    protected function regenerateSession()
    {
        // If this session is obsolete it means there already is a new id
        if (isset($_SESSION['OBSOLETE']))
        {
            if ($_SESSION['OBSOLETE'] == true)
            {
                return;
            }
            return;
        }

        // Set current session to expire in 10 seconds
        $_SESSION['OBSOLETE'] = true;
        $_SESSION['EXPIRES'] = time() + 10;

        // Create new session without destroying the old one
        session_regenerate_id(false);

        // Grab current session ID and close both sessions to allow other scripts to use them
        $newSession = session_id();
        session_write_close();

        // Set session ID to the new one, and start it back up again
        session_id($newSession);
        session_start();

        // Now we unset the obsolete and expiration values for the session we want to keep
        unset($_SESSION['OBSOLETE']);
        unset($_SESSION['EXPIRES']);
    }

    /**
     * We need to add another function to check for the obsolete flag and to see if the session has expired.
	 *
	 * @return bool
     */
    protected function validateSession()
    {
        if (isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES'])) 
			return false;

        if (isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time()) 
			return false;

        return true;
    }
}

