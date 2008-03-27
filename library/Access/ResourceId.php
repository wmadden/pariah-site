<?php

/**
 * {filename}.php
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

/**
 * This class encapsulates a resource identifier.
 * 
 * The purpose of this class, rather than a scalar, is to allow the
 * identification of a resource with zero margin for error.
 * 
 * This is achieved by storing the class name of the resource, and the primary
 * identifier (for a database table, the primary key).
 */
class ResourceId
{
  const SEPARATOR = ':';

  public function __construct( $primary_id, $class_name )
  {
    if( empty($primary_id) || empty($class_name) )
      throw new ResourceException('Resource ID requires class and primary ID!', 0);
    
    $this->primaryId = $primary_id;
    $this->className = $class_name;
  }
  
  public function getId()
  {
    return $this->primaryId;
  }
  
  public function getClass()
  {
    return $this->className;
  }
  
  public function __toString()
  {
    return $this->getClass() . ResourceId::SEPARATOR . $this->getId();
  }
  
  protected $primaryId = null;
  protected $className = null;
}
