<?php
/**
 * Mapper.php
 *
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 *
 * @author William Madden
 */

/**
 * The Mapper interface, provides a common set of mapping operations.
 * 
 * A Mapper is a class which allows you to map a Model to or from memory. An
 * example of a mapper is a class which maps models to records in a database
 * table.
 * 
 * This interface provides the following operations:
 *    loadModel(s)
 *    saveModel(s)
 *    deleteModel(s)
 * 
 * The return types for these functions are implementation specific (of course),
 * but it is strongly recommended that where possible you avoid indicative 
 * return values, and instead throw exceptions subclassed from MapperException
 * on errors.
 */
interface MapperInterface
{
  /**
   * The constructor.
   * 
   * @param array $config An array of configuration info.
   */
  public function __construct( array $config = array() );
  
  /**
   * Loads a model, given some means to identify the model.
   * 
   * How $identifier is defined and handled is implementation specific.
   * 
   * @param array $criteria A map of properties to values for selecting a single model.
   * @throws MapperException If no single model can be found which matches the given criteria.
   * @return Model
   */
  public function loadModel( $criteria );
  /**
   * Loads a model, given some means to identify the model.
   * 
   * How $identifier is defined and handled is implementation specific.
   * 
   * @param array $criteria A map of properties to values for selecting models.
   * @return array An array of models matching the given criteria.
   */
  public function loadModels( $criteria );
  
  /**
   * Saves a given model.
   *
   * The means by which the model is saved are implementation specific. This
   * defines a common (and simple) interface to saving models.
   * 
   * @param Model $model
   */
  public function saveModel( Model $model );
  /**
   * Saves a set of models.
   * 
   * @param arra $models
   */
  public function saveModels( array $models );
  
  /**
   * Deletes a given model.
   *
   * The Model to delete can either be given as a parameter, or you can give it
   * an array of criteria - as with the loadModel functions.
   * 
   * @param Model|array $model The Model, or an array of criteria.
   */
  public function deleteModel( $model );
  /**
   * Deletes a given model.
   *
   * As with the deleteModel function, except this function takes an array of
   * Models/arrays.
   * 
   * @param $model
   */
  public function deleteModels( array $models );
  
  /**
   * Creates a model.
   * 
   * @param mixed $data Data from which to create a Model.
   * 
   * @return Model A shiny new model.
   */
  public function createModel( $data );
  /**
   * Creates a set of models.
   * 
   * @param array $data An array of data.
   * 
   * @return Model A shiny new model.
   */
  public function createModels( array $data );
}
