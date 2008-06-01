<?php

/**
 * Composite.php
 * 
 * Provides the Composite mapper interface.
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

require('Pariah/Model/Composite.php');

/**
 * This interface extends the Mapper base class by adding functions to handle
 * Composite models.
 * 
 * Composite models themselves are handled the same way as regular Models. The
 * additional operations available for Composite Models are:
 *   o loadComponents
 *   o saveComponents
 *   o deleteComponents
 * TODO: revise deleteComponents. It's a dangerous function and might be better
 * left alone, or to a more specific subclass.
 */
interface Pariah_Mapper_Composite
{
  /**
   * Used to load components of a given type into the composite.
   *
   * @param Pariah_Model_Composite $component_type The type of components to load. If null, all components will be loaded.
   */
  public function loadComponents( Pariah_Model_Composite $composite, $component_type );
  
  /**
   * Used to save components of a given type.
   *
   * @param Pariah_Model_Composite $component_type The type of components to save. If null, all components will be saved.
   */
  public function saveComponents( Pariah_Model_Composite $composite, $component_type );
  
  /**
   * Used to save components of a given type.
   *
   * @param Pariah_Model_Composite $component_type The type of components to save. If null, all components will be saved.
   */
  public function deleteComponents( Pariah_Model_Composite $composite, $component_type );
}