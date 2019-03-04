<?php
namespace model;

use aryelgois\Medools\Model as MedoolsModel;

/**
 * Model
 * @author ROC <i@rocs.me>
 */
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

    /**
     * Count
     *
     * @param array $condition
     * @return integer
     */
    public function count(array $condition)
    {
        return $this->getDatabase()->count(static::TABLE, $condition);
    }

    /**
     * Sum
     *
     * @param string $field
     * @param array $condition
     * @return Number
     */
    public function sum($field, array $condition)
    {
        return $this->getDatabase()->sum(static::TABLE, $field, $condition) ?: 0;
    }

    /**
     * Has
     *
     * @param array $condition
     * @return boolean
     */
    public function has(array $condition)
    {
        return $this->getDatabase()->has(static::TABLE, $condition);
    }

    /**
     * updateData
     *
     * @param array $data
     * @param array $condition
     * @return integer
     */
    public function updateData(array $data, array $condition)
    {
        return $this->getDatabase()->update(static::TABLE, $data, $condition);
    }
}
