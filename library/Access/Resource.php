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
 * A Resource represents an access controlled object in the domain.
 * 
 * Resources created at run time, existing only in memory are considered not to
 * be "concrete". This distinguishes new resources from resources which were
 * loaded from disk.
 * 
 * Every concrete resource has a unique identifier (a ResourceId).
 * Every resource has a set of access rules to which it is subject.
 */
class Resource extends CompositeModel implements Zend_Acl_Resource_Interface
{
  public function __construct( $data = null, $id = null, $dirty = true )
  {
    parent::__construct( $data, $id, $dirty );
    $this->addFields( array(
                'parent'
              ))
         ->addStructure(
                'Rule', CompositeModel::REL_MANYTOMANY
              );
  }
  
  /**
   * Gets the resource ID of the parent of this resource, unless an orphan.
   * 
   * @return ResourceId|null Returns the ResourceId of the parent, or null.
   */
  public function getParent()
  {
    // Get the value of the parent ID
    $parent = $this->get('parent');
    
    // If the resource has no parent, return null
    if( empty($parent) )
      return null;
    
    // Convert it to a ResourceId by first splitting it around the separator
    $bits = explode(ResourceId::SEPARATOR, $parent);
    // Create the resource ID
    $rid = new ResourceId($bits[1], $bits[0]);
    // Return it
    return $rid;
  }
  
  /**
   * Sets the parent field of the resource.
   */
  public function setParent( ResourceId $rid )
  {
    return $this->set('parent', implode( ResourceId::SEPARATOR, array($rid->getClass(), $rid->getId()) ) );
  }
  
  public final function getResourceId()
  {
    if( !$this->concrete() )
      throw new ResourceException("Resource not concrete, can't form ResourceId.", 1);
    
    return new ResourceId($this->getId(), get_class($this));
  }
  
  public final function setResourceId()
  {
    throw new ResourceException("You can't set a resource ID directly.", 0);
  }
}
