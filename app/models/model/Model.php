<?php
namespace model;

use aryelgois\Medools\Model as MedoolsModel;

class Model extends MedoolsModel
{
    protected $_error;

    /**
     * Construct Method
     * @method __construct
     * @return Object
     */
    public function __construct($db = 'default')
    {
        if (!empty($db)) {
            app()->db($db);
        }
        parent::__construct();
    }

    /**
     * getError
     * @method getError
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }
}
