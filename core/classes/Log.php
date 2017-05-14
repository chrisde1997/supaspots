<?php

/* 
 * Created by Chris Dekker.
 * 
 * Date:
 * 
 * Log Class
 */

class Log {
    private $_db,
            $_data,
            $_count;
    
    public function __construct() {
        $this->_db = DB::getInstance();
    }
    
    public function getLogs($key) {
        $data = $this->_db->getAll('logs', $key);
        
        if($data->count()) {
            $this->_data = $data->results();
            $this->_count = $data->count();
            return true;
        }
    }
    
    public function create($fields) {
        if(!$this->_db->insert('logs', $fields)) {
            throw new Exception('There was a problem creating the log.');
        }
    }
    
    public function delete($id = null) {
        $this->_db->delete('logs', array('id', '=', $id));
    }
    
    public function data() {
        return $this->_data;
    }
    
    public function count() {
        return $this->_count;
    }
}