<?php

/**
 * Mapper.php
 * 
 * Provides the model mapper base class.
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

/**
 * Mappers are used to map Models from memory to some persistent storage, and
 * back again.
 * 
 * The Mapper base class provides the additional service of instantiating
 * specific mappers.
 */
class Pariah_Mapper
{
  /**
   * The protected constructor, used to enforce singleton mappers.
   */
  protected function __construct()
  {
    
  }
  
  /**
   * Loads a model.
   */
  public function load()
  {
    throw new Pariah_Mapper_Exception("Function load() must be implemented in a subclass.");
  }
  
  /**
   * Saves a model.
   *
   * @param Model $model
   */
  public function save( Pariah_Model $model )
  {
    throw new Pariah_Mapper_Exception("Function save() must be implemented in a subclass.");
  }
  
  /**
   * Returns a Mapper instance for the given model type.
   *
   * @param Model|string $model
   */
  public static function get( $model )
  {
    $mapper_name = $model;
    if( $model instanceof Pariah_Model )
    {
      $mapper_name = self::getMapperName($model);
    }
    
    // If the mapper has already been instantiated through get()
    if( isset(self::$_mappers[$mapper_name]) )
    {
      return self::$_mappers[$mapper_name];
    }
    
    $mapper_name = get_class(self) . $mapper_name;
    self::$_mappers[$mapper_name] = $mapper = new $mapper_name();
    return $mapper;
  }
  
  /**
   * An array of mappers instantiated through get.
   *
   * @var unknown_type
   */
  protected static $mappers = array();
}
