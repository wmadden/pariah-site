<?php

/**
 * ModelTest.php
 * 
 * This file provides the unit test for the Model class.
 * 
 * Copyright (C) 2008, Pariah Software,
 * all rights reserved.
 * 
 * @author William Madden
 */
 
require_once 'PHPUnit/Framework.php';
require_once '../../src/library/Pariah/Model.php';

class ModelTest extends PHPUnit_Framework_TestCase
{
  protected $model = null;
  
  public function setUp()
  {
    $this->model = new SomeModel();
  }
  
  public function tearDown()
  {
    unset($this->model);
  }
  
  /**
   * Test that the ID for a newly instantiated model is null.
   */
  public function testNoId()
  {
    $this->assertEquals( null, $this->model->getId() );
  }
  
  /**
   * Test that getId() will return the ID set with setId().
   */
  public function testSetId()
  {
    $this->model->setId(666);
    $this->assertEquals( 666, $this->model->getId() );
  }
  
  /**
   * Tests that the calls to addField, addFields and deleteField in the
   * SomeModel constructor worked.
   * 
   * @todo Think of a better way of testing this
   */
  public function testFieldManaging()
  {
    $this->assertEquals( true, $this->model->hasField('fieldB') );
    $this->assertEquals( true, $this->model->hasField('fieldC') );
    $this->assertEquals( true, $this->model->hasField('fieldD') );
  }
  
  public function testFieldAccessors()
  {
    $this->model->setFieldB('some value');
    $this->assertEquals( 'some value', $this->model->getFieldB() );
  }
}

class SomeModel extends Pariah_Model
{
  public function __construct()
  {
    $this->addField('fieldA')
         ->addField('fieldB')
         ->deleteField('fieldA')
         ->addFields( array('fieldC', 'fieldD', 'fieldE') );
  }
}