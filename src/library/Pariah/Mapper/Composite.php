<?php

/**
 * Composite.php
 * 
 * Provides the Composite mapper class.
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

/**
 * This class extends the Mapper base class by adding functions to handle
 * Composite models.
 * 
 * Composite models themselves are handled the same way as regular Models. The
 * additional operations available for Composite Models are:
 *   * loadComponents
 *   * saveComponents
 */
abstract class Pariah_Mapper_Composite extends Pariah_Mapper
{
  /**
   * Used to load components of a given type into the composite.
   *
   * @param Pariah_Model_Composite $component_type The type of components to load. If null, all components will be loaded.
   */
  abstract public function loadComponents( Pariah_Model_Composite $composite, $component_type );
  
  /**
   * Used to save components of a given type.
   *
   * @param Pariah_Model_Composite $component_type The type of components to save. If null, all components will be saved.
   */
  abstract public function saveComponents( Pariah_Model_Composite $composite, $component_type );
}