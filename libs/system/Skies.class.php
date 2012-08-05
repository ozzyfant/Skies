<?php

// Options
require_once ROOT_DIR.'/options.inc.php';

// Core functions
require_once ROOT_DIR.'/libs/system_functions.inc.php';

// Performance reasons ...
define('NOW', time());

// set exception handler
set_exception_handler(['\Skies', 'handleException']);

// set php error handler
if(DEBUG) {
    error_reporting(E_ALL);
}
else {
    error_reporting(E_ALL ^ E_NOTICE);
}

set_error_handler(['\Skies', 'handleError'], E_ALL);

// set autoload handler
spl_autoload_register(['\Skies', 'autoload']);

/*
 * Includes
 */

use skies\system\database\MySQL;
use skies\system\user\Session;
use skies\system\language\Language;
use skies\system\template\Template;
use skies\system\page\Page;
use skies\system\page\PageTypes;
use skies\system\page\DBPage;
use skies\system\page\FilePage;
use skies\system\page\SystemPage;

use skies\utils\spyc;
use skies\utils\PageUtils;


/**
 * @author    Janek Ostendorf (ozzy) <ozzy2345de@gmail.com>
 * @copyright Copyright (c) Janek Ostendorf
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3
 * @package   skies.system
 */
class Skies {

    /**
     * MySQL handler
     *
     * @var \skies\system\database\MySQL
     */
    public static $db = null;

    /**
     * Session handler
     *
     * @var \skies\system\user\Session
     */
    public static $session = null;

    /**
     * Default language defined in the config file
     *
     * @var \skies\system\language\Language
     */
    public static $defLanguage = null;

    /**
     * Language of this user
     *
     * @var \skies\system\language\Language
     */
    public static $language = null;

    /**
     * Configuration array
     *
     * @var array<string|array>
     */
    public static $config = null;

    /**
     * Currently used template
     *
     * @var \skies\system\template\Template
     */
    public static $template = null;

    /**
     * Current page
     *
     * @var \skies\system\page\DBPage|\skies\system\page\FilePage
     */
    public static $page = null;

    /**
     * Initialize Skies
     */
    public function __construct() {

        $this->initConfig();
        $this->initMysql();
        $this->initSession();
        $this->initLanguage();
        $this->initTemplate();
        $this->initPage();

        $this->afterInit();

        $this->showTemplate();

    }

    /**#@+
     * Init function
     */

    /**
     * Connect to the MySQL server
     */
    private function initMysql() {

        // NULL values
        $dbHost = $dbUser = $dbPassword = $dbName = '';

        // Fetch configuration
        require_once ROOT_DIR.'/libs/config.inc.php';

        self::$db = new skies\system\database\MySQL($dbHost, $dbUser, $dbPassword, $dbName);

    }

    /**
     * Initialize the session
     */
    private function initSession() {

        self::$session = new skies\system\user\Session();

    }

    /**
     * Read the config
     *
     * @throws skies\system\exception\SystemException
     */
    private function initConfig() {

        self::$config = \skies\utils\Spyc::YAMLLoad(ROOT_DIR.'/config/config.yml');

        if(self::$config === false) {
            throw new \skies\system\exception\SystemException('Failed to open config file!', 0, 'Failed to open required config file.');
        }

        /**#@+
         * Config dependant constants
         */

        /**
         * Subdirectory for relative paths (mainly URLs)
         */
        define('SUBDIR', self::$config['subdir']);

        /**#@-*/
    }

    /**
     * Initialize the language objects
     */
    private function initLanguage() {

        $query = 'SELECT * FROM '.TBL_PRE.'language WHERE langName = \''.\escape(self::$config['defaultLanguage']).'\'';

        self::$defLanguage = new \skies\system\language\Language(self::$db->query($query)->fetch_array()['langID'], true);

        // TODO: get this from user's data
        self::$language = new \skies\system\language\Language(2);

    }

    /**
     * Initialize the template
     */
    private function initTemplate() {

        // TODO: Get used template from user - if configured
        self::$template = new \skies\system\template\Template(self::$config['defaultTemplate']);

    }

    /**
     * Initialize the current page
     */
    private function initPage() {

        $page_name = (isset($_GET['_0']) ? $_GET['_0'] : self::$config['defaultPage']);

        $page_id = \skies\utils\PageUtils::getIDFromName($page_name);

        // If we get -1 back (system page)
        if($page_id == -1) {

            self::$page = new \skies\system\page\SystemPage($page_name);

        }
        else {

            // Get the type of the page
            switch(\skies\utils\PageUtils::getTypeFromID($page_id)) {

                case \skies\system\page\PageTypes::DB:

                    self::$page = new \skies\system\page\DBPage($page_id);

                    break;


                case \skies\system\page\PageTypes::FILE:

                    self::$page = new \skies\system\page\FilePage($page_id);

                    break;

            }

            // TODO: Make a nicer 404
            if(!is_object(self::$page)) {
                throw new \skies\system\exception\SystemException('404!', 404, '404!');
            }

        }

    }

    /**#@-*/

    /**
     * Performs routines to be called after all initializations
     */
    private function afterInit() {

        // Parse GET arguments
        if(isset($_GET['_1'])) {

            $args = explode('/', $_GET['_1']);

            $i = 0;

            foreach($args as $cur_arg) {

                $_GET['_'.++$i] = $cur_arg;


            }

        }

        // Page includes
        if(self::$page instanceof \skies\system\page\FilePage) {

            if(self::$page->getIncFile() !== false && @file_exists(self::$page->getIncFile())) {
                require_once self::$page->getIncFile();
            }

        }

    }

    /**
     * Print the template
     */
    private function showTemplate() {

        self::$template->printTemplate();

    }

    /**
     * Handle our Exceptions
     *
     * @static
     *
     * @param \Exception $e
     */
    public static final function handleException(\Exception $e) {

        if($e instanceof skies\system\exception\SystemException) {

            $e->show();
            exit;

        }

        // repack Exception
        self::handleException(new skies\system\exception\SystemException($e->getMessage(), $e->getCode(), '', $e));
    }

    /**
     * Catches php errors and throws instead a system exception.
     *
     * @param    integer        $errorNo
     * @param    string         $message
     * @param    string         $filename
     * @param    integer        $lineNo
     *
     * @throws skies\system\exception\SystemException
     */
    public static final function handleError($errorNo, $message, $filename, $lineNo) {

        if(error_reporting() != 0) {
            $type = 'error';
            switch($errorNo) {
                case 2:
                    $type = 'warning';
                    break;
                case 8:
                    $type = 'notice';
                    break;
            }

            throw new skies\system\exception\SystemException('PHP '.$type.' in file '.$filename.' ('.$lineNo.'): '.$message, 0);
        }
    }

    /**
     * Includes the required util or exception classes automatically.
     *
     * @param     string        $className
     *
     * @see        spl_autoload_register()
     */
    public static final function autoload($className) {

        $namespaces = explode('\\', $className);

        // Is it a valid import?
        if(array_shift($namespaces) == 'skies') {

            $classPath = ROOT_DIR.'/libs/'.implode('/', $namespaces).'.class.php';

            if(file_exists($classPath)) {
                require_once($classPath);
            }

        }

    }

}

?>