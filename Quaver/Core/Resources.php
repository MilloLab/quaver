<?php

/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\Core;

/**
 * Resources class.
 */
class Resources
{   
    /**
     * Constructor
     * @param array $arrayPath 
     */
    public function __construct($arrayPath)
    {
        $this->add($arrayPath);
    }

    /**
     * Add values to object
     * @param array $values 
     */
    protected function add($values)
    {   
        if (isset($values)) {
            foreach ($values as $k => $v) {
                $this->$k = $v;
            }    
        }
    }

    /**
     * Set css, js and img files
     * @param string $file 
     * @param string $type 
     * @param bool $_randomVar 
     * @return mixed
     */
    public function map($file, $type, $_randomVar = true)
    {
        if (isset($file) && isset($type)) {
            $part = explode($type, $file);
            if ($type == 'css' || $type == 'js') {
                $min = (defined('DEV_MODE') && DEV_MODE) ? 'min.' : '';
            } else {
                $min = '';
            }
            $randomVar = $_randomVar ? '?'.$this->randomVar : '';
            $typePath = isset($this->$type)? $this->$type : $this->img;
            
            return $typePath.'/'.$part[0].$min.$type.$randomVar;
        } else {
            return false;
        }
    }
}
