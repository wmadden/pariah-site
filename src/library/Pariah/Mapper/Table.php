<?php

/**
 * Table.php
 * 
 * Provides the Table mapper base class.
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

/**
 * This class is used to map models to database table entries.
 * 
 * Each model is assigned an id, which corresponds to its entry in the primary
 * field of the table it's being saved to or loaded from. If a model has no id
 * or its id is null, it's considered
 * 
 */
class Pariah_Model_Mapper_Table extends Pariah_Model_Mapper_Composite
{
  /**
   * Loads a model from a database.
   */
  public function load( $id )
  {
    
  }
  
  public function loadWhere( )
  {
    
  }
  
  public function loadComponents( )
  {
    
  }
  
  public function saveComponents( )
  {
    
  }
  
  /**
   * Saves a model in a database.
   *
   * @param Pariah_Model $model
   * @param string|Zend_Db_Table $table
   */
  public function save( Pariah_Model $model, $table = null )
  {
    
  }
}