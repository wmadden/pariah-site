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
 * The Mapper class serves as a central controller for mapper interactions.
 * 
 * While you can use mappers directly, there is a lot of configuration which
 * needs to be done to make them work, and if you're using a general base, like
 * Model, you don't want to have to figure out which mapper to use every time.
 * This class takes care of that by doing it for you.
 * 
 * Mapper works by using preset information to control its behaviour. This
 * information is stored as a Zend_Config structure in the registry, under a
 * preset name. You can change the name of the registry entry using the get/set-
 * RegistryEntry accessor functions.
 * 
 * This class is entirely static. I was considering making it a singleton, since
 * you shouldn't really instantiate it more than once, but in fact you can
 * instantiate it as much as you like and it'll still be useless (as an
 * instance).
 * 
 * Configuration info is arranged as follows:
 *     mappers =>
 *         (class name) => (config)
 *     models =>
 *         (class name) =>
 *             mapper => (class name)
 *             config => (config)
 * 
 * Each time you load/save/create/delete a model, the static Mapper instantiates
 * a mapper to perform the action. The "mappers" config subset lets you set
 * default configuration options for mappers by class name, then by operation.
 * The "models" subset lets you specify the mapper to be used for each operation
 * and the configuration info to be passed to the mapper upon initialisation.
 * 
 * Default values will be merged with more specific ones so that specific
 * options override defaults.
 */
class Mapper
{
  const DEFAULT_REGISTRY_ENTRY = 'mapper_config';
  
  /**
   * Loads a model.
   * 
   * @param string $class The name of the class being loaded.
   * @param mixed $criteria An array of data identifying the model to be loaded.
   * @param array $config The config array to pass to the mapper on init.
   * @param string $mapper The name of the mapper class.
   * @param array $args Additional args to pass to the function.
   * 
   * @return The loaded model.
   */
  public static function loadModel( $class, $criteria, $config = array(), $mapper = null, array $args = array() )
  {
    // Get a mapper
    $mapper = self::getMapper( $class, $mapper, $config );
    // Compose function arguments
    $args = array_merge( array($criteria), $args );
    // Load the model
    return call_user_func_array( array($mapper, 'loadModel'), $args );
  }
  /**
   * Loads a set of models.
   * 
   * @param string $class The name of the class being loaded.
   * @param mixed $criteria An array of data identifying the models to be loaded.
   * @param array $config The config array to pass to the mapper on init.
   * @param string $mapper The name of the mapper class.
   * @param array $args Additional args to pass to the function.
   * 
   * @return The set of loaded models.
   */
  public static function loadModels( $class, $criteria, $config = array(), $mapper = null, array $args = array() )
  {
    // Get a mapper
    $mapper = self::getMapper( $class, $mapper, $config );
    // Compose function arguments
    $args = array_merge( array($criteria), $args );
    // Load the model
    return call_user_func_array( array($mapper, 'loadModels'), $args );
  }
  
  /**
   * Saves a model.
   *
   * @param Model $model The Model being saved.
   * @param array $config The config array to pass to the mapper on init.
   * @param string $mapper The name of the mapper class.
   * @param array $args Additional args to pass to the function.
   */
  public static function saveModel( Model $model, $config = array(), $mapper = null, array $args = array() )
  {
    // Get a mapper
    $mapper = self::getMapper( get_class($model), $mapper, $config );
    // Compose function arguments
    $args = array_merge( array($model), $args );
    // Save the model
    return call_user_func_array( array($mapper, 'saveModel'), $args );
  }
/**
   * Saves a set of models.
   *
   * @param array $models An array of models to save.
   * @param array $config The config array to pass to the mapper on init.
   * @param string $mapper The name of the mapper class.
   * @param array $args Additional args to pass to the function.
   */
  public static function saveModels( array $models, $config = array(), $mapper = null, array $args = array() )
  {
    // Get a mapper
    $mapper = self::getMapper( get_class($model), $mapper, $config );
    // Compose function arguments
    $args = array_merge( array($models), $args );
    // Save the model
    return call_user_func_array( array($mapper, 'saveModels'), $args );
  }
  
  /**
   * Creates a model from data.
   *
   * @param string $class
   * @param mixed $data
   * @param array $config The config array to pass to the mapper on init.
   * @param string $mapper The name of the mapper class.
   * @param array $args Additional args to pass to the function.
   * 
   * @return Model A shiny new Model.
   */
  public static function createModel( $class, $data, $config = array(), $mapper = null, array $args = array() )
  {
    // Get a mapper
    $mapper = self::getMapper( $class, $mapper, $config );
    // Compose function arguments
    $args = array_merge( array($data), $args );
    // Create the model
    return call_user_func_array( array($mapper, 'createModel'), $args );
  }
  /**
   * Creates a set of models from data.
   *
   * @param string $class The type of Model to create.
   * @param array $data An array of data to create the models from.
   * @param array $config The config array to pass to the mapper on init.
   * @param string $mapper The name of the mapper class.
   * @param array $args Additional args to pass to the function.
   * 
   * @return Model A shiny new Model.
   */
  public static function createModels( $class, array $data, $config = array(), $mapper = null, array $args = array() )
  {
    // Get a mapper
    $mapper = self::getMapper( $class, $mapper, $config );
    // Compose function arguments
    $args = array_merge( array($data), $args );
    // Create the model
    return call_user_func_array( array($mapper, 'createModels'), $args );
  }
  
  /**
   * Deletes a model.
   *
   * @param string $class The type of Model to delete.
   * @param array $model Either a Model, or an array of criteria.
   * @param array $config The config array to pass to the mapper on init.
   * @param string $mapper The name of the mapper class.
   * @param array $args Additional args to pass to the function.
   */
  public static function deleteModel( $class, $model, $config = array(), $mapper = null, array $args = array() )
  {
    // Get a mapper
    $mapper = self::getMapper( $class, $mapper, $config );
    // Compose function arguments
    $args = array_merge( array($model), $args );
    // Delete the model
    return call_user_func_array( array($mapper, 'deleteModel'), $args );
  }
/**
   * Deletes a set of models.
   *
   * @param string $class The type of Model to delete.
   * @param array $model An array of either Models, or arrays of criteria (or a mix of both).
   * @param array $config The config array to pass to the mapper on init.
   * @param string $mapper The name of the mapper class.
   * @param array $args Additional args to pass to the function.
   */
  public static function deleteModels( $class, array $models, $config = array(), $mapper = null, array $args = array() )
  {
    // Get a mapper
    $mapper = self::getMapper( $class, $mapper, $config );
    // Compose function arguments
    $args = array_merge( array($models), $args );
    // Delete the model
    return call_user_func_array( array($mapper, 'deleteModels'), $args );
  }
  
  /**
   * Sets the name of the registry entry where configuration data is stored.
   *
   * @param string $registry_entry
   */
  public static function setRegistryEntry( $registry_entry )
  {
    self::$registryEntry = $registry_entry;
  }
  
  /**
   * Gets the name of the registry entry where configuration data is stored.
   *
   * @return string The name of the registry entry where configuration data is stored.
   */
  public static function getRegistryEntry()
  {
    return self::$registryEntry;
  }
  
  /**
   * Sets the Zend_Config object storing the configuration info for the mapper.
   *
   * @param Zend_Config $config The configuration object.
   */
  public static function setConfig( Zend_Config $config )
  {
    Zend_Registry::set(self::$registryEntry, $config);
  }
  
  /**
   * Returns the Zend_Config object storing the configuration info for the
   * mapper.
   */
  public static function getConfig()
  {
    // Zend_Registry::get will throw an exception if there is no data
    try
    {
      return Zend_Registry::get(self::$registryEntry);
    }
    catch ( Zend_Exception $e )
    {
      // Create a blank config objec
      $config = new Zend_Config(
          array(
            'mappers' => array(),
            'models' => array()
          ),
          true
      );
      // Store it
      Zend_Registry::set(self::$registryEntry, $config);
      // Return it
      return $config;
    }
  }
  
  /**
   * Returns a Mapper instance for the given class.
   * 
   * @param string $class The type of class being handled.
   * @param string $mapper The class name of the mapper to use.
   * @param array $config The config array to pass to the mapper.
   */
  public static function getMapper( $class, $mapper = null, $config = array() )
  {
    // Get the mapper configuration info
    $info = self::getConfig();
    try
    {
      $mapper_settings = $info->mappers;
      $model_settings = $info->models;
    }
    // If the config doesn't have either of these fields, it's badly formed
    catch ( Zend_Exception $e )
    {
      throw new MapperException("Mapper config badly formed.", 5);
    }
    
    // If mapper name is not given, get it and config info
    if( $mapper == null )
    {
      // If config info for this model is set,
      if( isset($model_settings->$class) && isset($model_settings->$class->mapper) )
      {
        $mapper = $model_settings->$class->mapper;
      }
      // Else, we can't continue. Not enough information.
      else
        throw new MapperException("Model config unset for '$class'.", 6);
    }
    
    // If config info for the mapper is set, use it
    $default_config = array();
    if( isset($mapper_settings->$mapper) )
      $default_config = $mapper_settings->$mapper->toArray();
    
    // If model config info for the mapper is set, use it
    if( isset($model_settings->$class) && isset($model_settings->$class->config) )
      $default_config = array_merge( $default_config,
                                     $model_settings->$class->config->toArray()
                                   );
    
    // Merge the defaults with the given config
    $config = array_merge( $default_config, $config );
    
    // Initialise the mapper
    return new $mapper( $config );
  }
  
  protected static $registryEntry = Mapper::DEFAULT_REGISTRY_ENTRY;
}
