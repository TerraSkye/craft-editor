<?php

namespace Craft;

use PHPUnit_Framework_TestCase;

// Ensure Twig classes are loaded
craft()->templates->registerTwigAutoloader();

class EditorTwigExtensionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->extension = new EditorTwigExtension();
    }

    /**
     * GetFilters should return a "shake" filter
     */
    public function testGetFilters()
    {
        $result = $this->extension->getFilters();

        $this->assertArrayHasKey('shake', $result);
    }

    public function testShakeFilter()
    {
        $str = "This is an example sentence to be shaken up.";
        $result = $this->extension->shakeFilter($str);

        $this->assertEquals(strlen($str), strlen($result));
        $this->assertNotEquals($str, $result);
    }

    public function testShakeFilterEmptyString()
    {
        $result = $this->extension->shakeFilter(null);

        $this->assertSame('', $result);
    }
}
