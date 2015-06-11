<?PHP
#################################################################################
## Developed by Manifest Interactive, LLC                                      ##
## http://www.manifestinteractive.com                                          ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ##
##                                                                             ##
## THIS SOFTWARE IS PROVIDED BY MANIFEST INTERACTIVE 'AS IS' AND ANY           ##
## EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE         ##
## IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR          ##
## PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL MANIFEST INTERACTIVE BE          ##
## LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR         ##
## CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF        ##
## SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR             ##
## BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,       ##
## WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE        ##
## OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,           ##
## EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                          ##
## ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ ##
## Authors of file: Peter Schmalfeldt & John Kramlich                          ##
#################################################################################

/**
 * @category Apple Push Notification Service using PHP & MySQL
 * @package EasyAPNs
 * @author Peter Schmalfeldt <manifestinteractive@gmail.com>
 * @author John Kramlich <me@johnkramlich.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link http://code.google.com/p/easyapns/
 */

/**
 * Begin Document
 */

class DbConnect
{
	/**
	* Connection to MySQL.
	*
	* @var string
	*/
	var $link;

	/**
	* Holds the most recent connection.
	*
	* @var string
	*/
	var $recent_link = null;

	/**
	* Holds the contents of the most recent SQL query.
	*
	* @var string
	*/
	var $sql = '';

	/**
	* Holds the number of queries executed.
	*
	* @var integer
	*/
	var $query_count = 0;

	/**
	* The text of the most recent database error message.
	*
	* @var string
	*/
	var $error = '';

	/**
	* The error number of the most recent database error message.
	*
	* @var integer
	*/
	var $errno = '';

	/**
	* Do we currently have a lock in place?
	*
	* @var boolean
	*/
	var $is_locked = false;

	/**
	* Show errors? If set to true, the error message/sql is displayed.
	*
	* @var boolean
	*/
	var $show_errors = false;

	/**
	* Log errors? If set to true, the error message/sql is logged.
	*
	* @var boolean
	*/
	public $log_errors = false;

	/**
	* The Database.
	*
	* @var string
	*/
	public $DB_DATABASE;

	/**
	* The variable used to contain a singleton instance of the database connection.
	*
	* @var string
	*/
	static $instance;

	/**
	* The number of rows affected by the most recent query.
	*
	* @var string
	*/
	public $affected_rows;

	public $insert_id;



	/**
	* Constructor. Initializes a database connection and selects our database.
	* @param string $host		The host to wchich to connect.
	* @param string $username	The name of the user used to login to the database.
	* @param string $password	The password of the user to login to the database.
	* @param string $database	The name of the database to which to connect.
	*/
	function __construct($host, $username, $password, $database)
	{
		$this->DB_HOST     = $host;
		$this->DB_USERNAME = $username;
		$this->DB_PASSWORD = $password;
		$this->DB_DATABASE = $database;
	}

	/**
	* Singleton pattern to retrieve database connection.
	*
	* @return mixed	MySQL database connection
	*/
	function _get($property)
	{
		if(self::$instance == NULL)
		{
			self::$instance = $this->connect();
		}

		return self::$instance->$property;

	}


	/**
	* Singleton pattern to retrieve database connection.
	*
	* @return mixed	MySQL database connection
	*/
	function Connection()
	{
		if(self::$instance == NULL)
		{
			self::$instance = $this->connect();
		}
		return self::$instance;
	}


	/**
	* Connect to the Database.
	*
	*/
	function connect()
	{
		self::$instance = new mysqli($this->DB_HOST, $this->DB_USERNAME, $this->DB_PASSWORD, $this->DB_DATABASE);

		if (mysqli_connect_errno()) {
			$this->raise_error(printf("Connect failed: %s\n", mysqli_connect_error()));
		}

		return self::$instance;
	}


	/**
	* Executes a sql query. If optional $only_first is set to true, it will
	* return the first row of the result as an array.
	*
	* @param  string  Query to run
	* @param  bool    Return only the first row, as an array?
	* @return mixed
	*/
	function query($sql, $only_first = false)
	{
		if(self::$instance == NULL)
		{
			self::$instance = $this->connect();
		}

		$this->recent_link =& self::$instance;
		$this->sql =& $sql;

		if(!$result = self::$instance->query($sql))
		{
			$this->raise_error(printf("Connect failed: %s\n", self::$instance->error));
		}

		$this->affected_rows = self::$instance->affected_rows;
		$this->insert_id = self::$instance->insert_id;
		$this->query_count++;

		if ($only_first)
		{
			$return = $result->fetch_array(MYSQLI_ASSOC);
			$this->free_result($result);
			return $return;
		}
		return $result;
	}

	/**
	* Fetches a row from a query result and returns the values from that row as an array.
	*
	* @param  string  The query result we are dealing with.
	* @return array
	*/
	function fetch_array($result)
	{
		return @mysql_fetch_assoc($result);
	}

	/**
	* Returns the number of rows in a result set.
	*
	* @param  string  The query result we are dealing with.
	* @return integer
	*/
	function num_rows($result)
	{
		return self::$instance->num_rows;
	}

	/**
	* Retuns the number of rows affected by the most recent query
	*
	* @return integer
	*/
	function affected_rows()
	{
		return self::$instance->affected_rows;
	}


	/**
	* Returns the number of queries executed.
	*
	* @param  none
	* @return integer
	*/
	function num_queries()
	{
		return $this->query_count;
	}

	/**
	* Lock database tables
	*
	* @param   array  Array of table => lock type
	* @return  void
	*/
	function lock($tables)
	{
		if (is_array($tables) AND count($tables))
		{
			$sql = '';

			foreach ($tables AS $name => $type)
			{
				$sql .= (!empty($sql) ? ', ' : '') . "$name $type";
			}

			$this->query("LOCK TABLES $sql");
			$this->is_locked = true;
		}
	}

	/**
	* Unlock tables
	*/
	function unlock()
	{
		if ($this->is_locked)
		{
			$this->query("UNLOCK TABLES");
		}
	}

	/**
	* Returns the ID of the most recently inserted item in an auto_increment field
	*
	* @return  integer
	*/
	function insert_id()
	{
		return self::$instance->insert_id;
	}

	/**
	* Escapes a value to make it safe for using in queries.
	*
	* @param  string  Value to be escaped
	* @param  bool    Do we need to escape this string for a LIKE statement?
	* @return string
	*/
	function prepare($value, $do_like = false)
	{
		if(self::$instance == NULL)
		{
			self::$instance = $this->connect();
		}

		if ($do_like)
		{
			$value = str_replace(array('%', '_'), array('\%', '\_'), $value);
		}

		return self::$instance->real_escape_string($value);
	}

	/**
	* Frees memory associated with a query result.
	*
	* @param  string   The query result we are dealing with.
	* @return boolean
	*/
	function free_result($result)
	{
		return @mysql_free_result($result);
	}

	/**
	* Turns database error reporting on
	*/
	function show_errors()
	{
		$this->show_errors = true;
	}

	/**
	* Turns database error reporting off
	*/
	function hide_errors()
	{
		$this->show_errors = false;
	}

	/**
	* Closes our connection to MySQL.
	*
	* @param  none
	* @return boolean
	*/
	function close()
	{
		$this->sql = '';
		return self::$instance->close();
	}

	/**
	* Returns the MySQL error message.
	*
	* @param  none
	* @return string
	*/
	function error()
	{
		$this->error = (is_null($this->recent_link)) ? '' : self::$instance->error;
		return $this->error;
	}

	/**
	* Returns the MySQL error number.
	*
	* @param  none
	* @return string
	*/
	function errno()
	{
		$this->errno = (is_null($this->recent_link)) ? 0 : self::$instance->errno ;
		return $this->errno;
	}

	/**
	* Gets the url/path of where we are when a MySQL error occurs.
	*
	* @access private
	* @param  none
	* @return string
	*/
	function _get_error_path()
	{
		if ($_SERVER['REQUEST_URI'])
		{
			$errorpath = $_SERVER['REQUEST_URI'];
		}
		else
		{
			if ($_SERVER['PATH_INFO'])
			{
				$errorpath = $_SERVER['PATH_INFO'];
			}
			else
			{
				$errorpath = $_SERVER['PHP_SELF'];
			}

			if ($_SERVER['QUERY_STRING'])
			{
				$errorpath .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		if (($pos = strpos($errorpath, '?')) !== false)
		{
			$errorpath = urldecode(substr($errorpath, 0, $pos)) . substr($errorpath, $pos);
		}
		else
		{
			$errorpath = urldecode($errorpath);
		}
		return $_SERVER['HTTP_HOST'] . $errorpath;
	}

	/**
	* If there is a database error, the script will be stopped and an error message displayed.
	*
	* @param  string  The error message. If empty, one will be built with $this->sql.
	* @return string
	*/
	function raise_error($error_message = '')
	{
		if ($this->recent_link)
		{
			$this->error = $this->error($this->recent_link);
			$this->errno = $this->errno($this->recent_link);
		}

		if ($error_message == '')
		{
			$this->sql = "Error in SQL query:\n\n" . rtrim($this->sql) . ';';
			$error_message =& $this->sql;
		}
		else
		{
			$error_message = $error_message . ($this->sql != '' ? "\n\nSQL:" . rtrim($this->sql) . ';' : '');
		}

		$message = "<textarea rows=\"10\" cols=\"80\">MySQL Error:\n\n\n$error_message\n\nError: {$this->error}\nError #: {$this->errno}\nFilename: " . $this->_get_error_path() . "\n</textarea>";

		if (!$this->show_errors)
		{
			$message = "<!--\n\n$message\n\n-->";
		}
		else die("There seems to have been a slight problem with our database, please try again later.<br /><br />\n$message");
	}
}

?>
