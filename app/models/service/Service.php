<?php
namespace service;

/**
 * Service
 * @author ROC <i@rocs.me>
 */
class Service
{
    public $_error;

    /**
     * __get
     *
     * @param string $modelName
     * @return Object
     */
    public function __get($modelName)
    {
        if (empty($this->$modelName) || !is_object($this->$modelName)) {
            $ModelName = 'model\\'.ucfirst($modelName);
            $this->$modelName = new $ModelName();
        }
        return $this->$modelName;
    }

    /**
     * 重置Model
     *
     * @param string $modelName
     * @return Object
     */
    public function resetModel($modelName)
    {
        $ModelName = 'model\\'.ucfirst($modelName);
        $this->$modelName = new $ModelName();

        return $this;
    }
}
