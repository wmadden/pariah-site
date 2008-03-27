<?php

/**
 * {filename}.php
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */

class ResourceTable extends CompositeModelTable
{
  /**
   * Loads the parent of a resource.
   *
   * Takes as parameter a resource. You may also provide a table, if you don't 
   * want to use this one to load the parent.
   * 
   * Returns null if the resource has no parent.
   * 
   * @param Resource $resource The resource.
   * @param ResourceTable $table The table used to load the parent resource.
   */
  public function getParent( Resource $resource, ResourceTable $table = null )
  {
    // Get the parent's ID
    $id = $resource->getParent()->__toString();
    
    if( $id = null )
      return null;
    
    // We have the ID, get the object
    if( $table == null )
      $table = $this;
    
    $parent = $table->load($id);
    
    return $parent;
  }
  
  /**
   * Loads the rules of a resource.
   *
   * @param Resource $resource The resource.
   * @param ResourceTable $table The table used to load the rules.
   */
  public function loadRules( Resource $resource, ResourceTable $table = null )
  {
    if( $table == null )
      $table = $this;
    
    $table->loadComponents( $resource, 'Rule' );
  }
  
}
