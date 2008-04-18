<?php

/**
 * {filename}.php
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

class Rule extends CompositeModel
{
  const PERMIT = 'true';
  const FORBID = 'false';
  
  public function __construct($data = null, $id = null, $dirty = true)
  {
    // Set up composite structure
    $this->addField('name')
         ->addField('permit')
         ->addStructure( 'Role', 'MM' )
         ->addStructure( 'Action', 'MM' );
    
    // Call parent constructor
    parent::__construct($data, $id, $dirty);
  }
}
