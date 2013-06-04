<?php

namespace Craft;

use Mockery as m;
use PHPUnit_Framework_TestCase;

class EditorServiceTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->ingredientRecord = m::mock('Craft\Editor_IngredientRecord');
        $this->service = new EditorService($this->ingredientRecord);
    }

    public function testNewIngredient()
    {
        $result = $this->service->newIngredient();

        $this->assertInstanceOf('Craft\Editor_IngredientModel', $result);
    }

    public function testNewIngredientWithAttributes()
    {
        $result = $this->service->newIngredient(array('id' => 5));

        $this->assertInstanceOf('Craft\Editor_IngredientModel', $result);
        $this->assertEquals(5, $result->id);
    }

    public function testGetAllIngredients()
    {
        $fakeResults = array(array('id' => 3), array('id' => 5));

        $this->ingredientRecord
            ->shouldReceive('findAll')->with(array('order' => 't.name'))
            ->andReturn($fakeResults);

        $results = $this->service->getAllIngredients();

        $this->assertEquals(2, count($results));
        $this->assertInstanceOf('Craft\Editor_IngredientModel', $results[5]);
    }

    public function testGetIngredientById()
    {
        $attributes = array('id' => 5);

        $mockRecord = m::mock('Craft\Editor_IngredientModel');
        $this->ingredientRecord
            ->shouldReceive('findByPk')->with(5)
            ->andReturn($mockRecord);

        $mockRecord->shouldReceive('getAttributes')->andReturn($attributes);

        $result = $this->service->getIngredientById(5);

        $this->assertInstanceOf('Craft\Editor_IngredientModel', $result);
        $this->assertEquals(5, $result->id);
    }

    public function testGetIngredientByMissingId()
    {
        $this->ingredientRecord->shouldReceive('findByPk')->with(5)
            ->andReturn(null);
        $result = $this->service->getIngredientById(5);

        $this->assertNull($result);
    }

    public function testSaveIngredient()
    {
        $mockModel = m::mock('Craft\Editor_IngredientModel');
        $mockModel->shouldReceive('getAttribute')->with('id')->once()->andReturn(5);

        $mockRecord = m::mock('Craft\Editor_IngredientRecord');
        $this->ingredientRecord->shouldReceive('findByPk')->with(5)->once()
            ->andReturn($mockRecord);

        $attributes = array('name' => 'example');
        $mockModel->shouldReceive('getAttributes')->once()->andReturn($attributes);
        $mockRecord->shouldReceive('setAttributes')->with($attributes)->once();

        $mockRecord->shouldReceive('save')->once()->andReturn(true);

        $mockRecord->shouldReceive('getAttribute')->with('id')->once()
            ->andReturn(5);
        $mockModel->shouldReceive('setAttribute')->with('id', 5)->once();

        $result = $this->service->saveIngredient($mockModel);
        $this->assertTrue($result);
    }

    /**
     * @expectedException Craft\Exception
     */
    public function testSaveIngredientNotFound()
    {
        $mockModel = m::mock('Craft\Editor_IngredientModel');
        $mockModel->shouldReceive('getAttribute')->with('id')->once()->andReturn(5);

        $mockRecord = m::mock('Craft\Editor_IngredientRecord');
        $this->ingredientRecord->shouldReceive('findByPk')->with(5)->once()
            ->andReturn(null);

        $result = $this->service->saveIngredient($mockModel);
    }

    public function testSaveIngredientInvalid()
    {
        $mockModel = m::mock('Craft\Editor_IngredientModel');
        $mockModel->shouldReceive('getAttribute')->with('id')->once()->andReturn(5);

        $mockRecord = m::mock('Craft\Editor_IngredientRecord');
        $this->ingredientRecord->shouldReceive('findByPk')->with(5)->once()
            ->andReturn($mockRecord);

        $attributes = array('name' => 'example');
        $mockModel->shouldReceive('getAttributes')->once()->andReturn($attributes);
        $mockRecord->shouldReceive('setAttributes')->with($attributes)->once();

        $mockRecord->shouldReceive('save')->once()->andReturn(false);

        $errors = array('name' => 'error message');
        $mockRecord->shouldReceive('getErrors')->once()->andReturn($errors);
        $mockModel->shouldReceive('addErrors')->with($errors)->once();

        $result = $this->service->saveIngredient($mockModel);
        $this->assertFalse($result);
    }

    public function testSaveIngredientNewRecord()
    {
        $mockModel = m::mock('Craft\Editor_IngredientModel');
        $mockModel->shouldReceive('getAttribute')->with('id')->once()->andReturn(null);

        $mockRecord = m::mock('Craft\Editor_IngredientRecord');
        $this->ingredientRecord->shouldReceive('create')->once()
            ->andReturn($mockRecord);

        $attributes = array('name' => 'example');
        $mockModel->shouldReceive('getAttributes')->once()->andReturn($attributes);
        $mockRecord->shouldReceive('setAttributes')->with($attributes)->once();

        $mockRecord->shouldReceive('save')->once()->andReturn(true);

        $mockRecord->shouldReceive('getAttribute')->with('id')->once()
            ->andReturn(5);
        $mockModel->shouldReceive('setAttribute')->with('id', 5)->once();

        $result = $this->service->saveIngredient($mockModel);
        $this->assertTrue($result);
    }

    public function testDeleteIngredientById()
    {
        $this->ingredientRecord
            ->shouldReceive('deleteByPk')->with(5)->andReturn(2);

        $result = $this->service->deleteIngredientById(5);

        $this->assertSame(2, $result);
    }
}
