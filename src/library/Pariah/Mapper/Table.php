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

require_once('Pariah/Mapper/Composite.php');

/**
 * This class is used to map models to database table entries.
 * 
 * Each model is assigned an id, which corresponds to its entry in the primary
 * field of the table it's being saved to or loaded from. If a model has no id
 * or its id is null, it's considered not to correspond to a table entry, and
 * hence operations like delete won't work on it.
 * 
 * This class is intended to be subclassed. 
 * 
 */
class Pariah_Model_Mapper_Table implements Pariah_Mapper_Composite
{
  /**
   * The name of the table class to which models are to be mapped.
   *
   * @var string
   */
  protected $_table = null;
  protected $_table_options = array();
  protected $_table_instance = null;
  
  public function __construct()
  {
    if( $this->_table !== null )
    {
      $table = (string)$this->_table;
      $this->_table_instance = new $table( $this->_table_options );
    }
  }
  
  /**
   * Loads from $table any models matching $criteria, where $criteria is a map
   * of property names to values.
   * 
   * @param array $criteria
   * @param Zend_Db_Table $table
   * 
   * @return array All matching models
   */
  public function load( array $criteria, Zend_Db_Table $table = null )
  {
    // If we're given a table to work with use that, otherwise use $_table
    if( $table === null )
    {
      if( $this->_table_instance === null )
      {
        throw new Pariah_Mapper_Exception("No table given, can't load models.");
      }
      
      $table = $this->_table_instance;
    }
    
    // Get the row matching $id
    $rows = $table->find($id);
    
    // Inflect Model fields to database fields
    $table_fields = array();
    foreach( $criteria as $model_field => $value )
    {
      $table_fields[ $this->_inflect($model_field) ] = $value;
    }
    
    // Search for matching rows
    $rows = $table->find($table_fields);
    
    // Make models from the rows
    return $this->makeModels($rows);
  }
  
  /**
   * Saves a model in a database.
   *
   * @param Pariah_Model $model
   * @param string|Zend_Db_Table $table
   */
  public function save( Pariah_Model $model, $table = null )
  {
    // If we're given a table to work with use that, otherwise use $_table
    if( $table === null )
    {
      if( $this->_table_instance === null )
      {
        throw new Pariah_Mapper_Exception("No table given, can't save model.");
      }
      
      $table = $this->_table_instance;
    }
    
    
  }
  
  public function delete()
  {
    
  }
  
  /**
   * Inflects a model field to the name of the corresponding database field.
   *
   * @param string $model_field
   */
  protected function _inflect( $model_field )
  {
    return $model_field;//TODO
  }
  
  /**
   * Instantiates models from the data in a rowset.
   *
   * @param unknown_type $model
   * @param Zend_Db_Table_Rowset_Abstract $rows
   */
  protected function _makeModels( $model, $rows )
  {
    // TODO
  }
}