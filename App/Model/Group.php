<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Model;

/**
 * Class Group
 * @package Quaver\App\Model
 */
class Group extends \Quaver\Model\Group
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->fields['level']['type'] = 'enum';
        $this->fields['level']['values'] = ['user', 'admin'];
        
        parent::__construct();
    }
}
