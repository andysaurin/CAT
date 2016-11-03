<?php

/**
* NQ_Cookie
*
* @package Framework
* @filesource
*/

/**
* NQ_Cookie
*
* Our base cookie class as a static. Handles creating the cookie, 
* writing to the variables and destroying the
* cookie.
*
* @package Framework
*/
class NQ_Cookie
{
    // Reserved session keys
    private static $_reserved = array();

    // Static class cannot be initialized
    private function __construct() {}
   
    // Alias for delete() function
    public static function del($key)
    {
        self::delete($key);
    }
   
    // Delete a cookie
    public static function delete($key)
    {
        // Change string representation array to key/value array
        $key = self::_scrubKey($key);
       
        // Make sure the cookie exists
        if (self::exists($key))
        {           
            // Check for key array
            if (is_array($key))
            {
                // Grab key/value pair
                list ($k, $v) = each($key);
               
                // Set string representation
                $key = $k . '[' . $v . ']';
               
                // Set expiration time to -1hr (will cause browser deletion)
                setcookie($key, false, time() - 3600);
               
                // Unset the cookie
                unset($_COOKIE[$k][$v]);
            }
           
            // Check for cookie array
            else if (is_array($_COOKIE[$key]))
            {
                foreach ($_COOKIE[$key] as $k => $v)
                {
                    // Set string representation
                    $cookie = $key . '[' . $k . ']';
                   
                    // Set expiration time to -1hr (will cause browser deletion)
                    setcookie($cookie, false, time() - 3600);
                   
                    // Unset the cookie
                    unset($_COOKIE[$key][$k]);
                }
            }
           
            // Unset single cookie
            else
            {
                // Set expiration time to -1hr (will cause browser deletion)
                setcookie($key, false, time() - 3600, '/', '.dvdhot.com');
               
                // Unset key
                unset($_COOKIE[$key]);
            }
        }
    }
   
    // See if a cookie key exists
    public static function exists($key)
    {
        // Change string representation array to key/value array
        $key = self::_scrubKey($key);
       
        // Check for array
        if (is_array($key))
        {
            // Grab key/value pair
            list ($k, $v) = each($key);
           
            // Check for key/value pair and return
            if (isset($_COOKIE[$k][$v])) return true;
        }
       
        // If key exists, return true
        else if (isset($_COOKIE[$key])) return true;
       
        // Key does not exist
        return false;
    }
   
    // Get cookie information
    public static function get($key)
    {
        // Change string representation array to key/value array
        $key = self::_scrubKey($key);
       
        // Check for array
        if (is_array($key))
        {
            // Grab key/value pair
            list ($k, $v) = each($key);
           
            // Check for key/value pair and return
            if (isset($_COOKIE[$k][$v])) return $_COOKIE[$k][$v];
        }

        // Return single key if it's set
        else if (isset($_COOKIE[$key])) return $_COOKIE[$key];
           
        // Otherwise return null
        else return null;
    }
   
    // Return the cookie array
    public static function contents()
    {
        return $_COOKIE;
    }
   
    // Set cookie information
    public static function set(
        $key,
        $value,
        $expire = 0,            /* Default expire time (session, 1 week = 604800) */
        $path = '',             /* Default path */
        $domain = '',           /* Default domain */
        $secure = false,        /* Does this cookie need a secure HTTPS connection? */
        $httponly = true        /* Can non-HTTP services access this cookie (IE: javascript)? */
    ){       
        // Make sure they aren't trying to set a reserved word
        if (!in_array($key, self::$_reserved))
        {       
            // If $key is in array format, change it to string representation
            $key = self::_scrubKey($key, true);
               
            // Store the cookie
            setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);   
        }
           
        // Otherwise, throw an error
        else Error::warning('Could not set key -- it is reserved.', __CLASS__);
    }
   
    // Converts strings to arrays (or vice versa if toString = true)
    private static function _scrubKey($key, $toString = false)
    {
        // Converting from array to string
        if ($toString)
        {
            // If $key is in array format, change it to string representation
            if (is_array($key))
            {
                // Grab key/value pair
                list ($k, $v) = each($key);
               
                // Set string representation
                $key = $k . '[' . $v . ']';
            }
        }
       
        // Converting from string to array
        else if (!is_array($key))
        {
            // is this a string representation of an array?
            if (preg_match('/([\w\d]+)\[([\w\d]+)\]$/i', $key, $matches))
            {
                // Store as key/value pair
                $key = array($matches[1] => $matches[2]);
            }
        }
       
        // Return key
        return $key;
    }
}

/* example usage
// Basic usage
NQ_Cookie::set('testcookie', 'test');
print(NQ_Cookie::get('testcookie'));

// You can set 'array' cookies in two different ways:
NQ_Cookie::set('array[one]', 'item one');
NQ_Cookie::set(array('array' => 'two'), 'item two');

// Likewise, you can also get 'array' cookies in two different ways:
print(NQ_Cookie::get('array[one]'));
print(NQ_Cookie::get(array('array' => 'one')));

// Or you can grab the whole array at once:
print_r(NQ_Cookie::get('array'));

// Deleting cookies is done in the same way:
NQ_Cookie::del('array[one]');
NQ_Cookie::del(array('array' => 'two'));

// Delete the entire array:
NQ_Cookie::del('array');

// Print contents of $_COOKIE (refresh for this)
print '<pre>';
print_r(NQ_Cookie::contents());
print '<pre>';
*/
?>
