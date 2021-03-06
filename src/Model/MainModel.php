<?php

namespace App\Model;

/**
 * Class MainModel
 * Creates Queries for CRUD
 * @package App\Model
 */
abstract class MainModel
{
    /**
     * Database
     * @var PDOModel
     */
    protected $database = null;

    /**
     * Database Table
     * @var string
     */
    protected $table = null;

    /**
     * Model constructor
     * Receives the Database Object & creates the Table Name
     * @param PDOModel $database
     */
    public function __construct(PDOModel $database)
    {
        $this->database = $database;
        $model = explode('\\', get_class($this));
        $this->table = ucfirst(str_replace('Model', '', array_pop($model)));
    }

    /**
     * Lists all Datas from the id or another key
     * @param string $value
     * @param string $key
     * @return array|mixed
     */
    public function listData(string $value = null, string $key = null, array $params = null)
    {
        if (isset($key)) {
            $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $key . ' = ?';

            return $this->database->getAllData($query, [$value]);
        } elseif (isset($params)) {
            $query_args = null;
            foreach ($params as $key => $param) {
                if (is_array($param)) {
                    $query_args .= $key . ' ' . key($param) . ' ' . $param[key($param)] . ' ';
                } else {
                    $query_args .= $key . ' ' . $param;
                }
            }

            $query = 'SELECT * FROM ' . $this->table . ' ' . $query_args;

            return $this->database->getAllData($query, [$value]);
        }

        $query = 'SELECT * FROM ' . $this->table;

        return $this->database->getAllData($query);
    }

    /**
     * getLastId
     *
     * @param  mixed $key
     *
     * @return void
     */
    public function getLastId($key)
    {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY ' . $key . ' DESC LIMIT 1';

        return $this->database->getAllData($query);
    }

    /**
     * setIndex
     *
     * @param  mixed $id
     *
     * @return void
     */
    public function setIndex($id)
    {
        $query = 'ALTER TABLE ' . $this->table . ' AUTO_INCREMENT = ' . $id;

        $this->database->setData($query);
    }

    /**
     * resetIndex
     *
     * @return void
     */
    public function resetIndex()
    {
        $query = 'ALTER TABLE ' . $this->table . ' AUTO_INCREMENT = 1';

        $this->database->setData($query);
    }

    /**
     * Creates a new Data entry
     * @param array $data
     */
    public function createData(array $data)
    {
        $keys = implode(', ', array_keys($data));
        $values = implode('", "', $data);
        $query = 'INSERT INTO ' . $this->table . ' (' . $keys . ') VALUES ("' . $values . '")';

        $this->database->setData($query);
    }

    /**
     * Reads Data from its id or another key
     * @param string $value
     * @param string|null $key
     * @return mixed
     */
    public function readData($value, $key = null)
    {
        if (isset($key)) {
            $query = 'SELECT * FROM ' . $this->table . ' WHERE ' . $key . ' = ?';
        } else {
            $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ?';
        }

        return $this->database->getData($query, [$value]);
    }

    /**
     * Updates Data from its id or another key
     * @param string $value
     * @param array $data
     * @param string|null $key
     */
    public function updateData($value, array $data, array $key)
    {
        $set = null;

        foreach ($data as $dataKey => $dataValue) {
            $set .= $dataKey . ' = "' . $dataValue . '", ';
        }

        $set = substr_replace($set, '', -2);

        if (isset($key) && !empty($key)) {
            $query = 'UPDATE ' . $this->table . ' SET ' . $set . ' WHERE ' . key($key) . ' = ' . $key[key($key)];
        } else {
            $query = 'UPDATE ' . $this->table . ' SET ' . $set;
        }

        $this->database->setData($query, [$value]);
    }

    /**
     * Deletes Data from its id or another key
     * @param string $value
     * @param string|null $key
     */
    public function deleteData(string $value, array $key)
    {
        if (isset($key) && !empty($key)) {
            $query = 'DELETE FROM ' . $this->table . ' WHERE ' . key($key) . ' = ' . $key[key($key)];
        } else {
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = ?';
        }

        $this->database->setData($query, [$value]);
    }
}
