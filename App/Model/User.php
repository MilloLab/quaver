<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

namespace Quaver\App\Model;

class User extends \Quaver\Model\GuardUser
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
