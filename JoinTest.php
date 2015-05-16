<?php

namespace Join;

require_once ('Join.php');
require_once ('PrivateMethodTrait.php');

use Join\Join;

class JoinTest extends \PHPUnit_Framework_TestCase
{
    use PrivateMethodTrait;

    private $left = [
        ['id' => 1, 'name' => 'Miriam', 'email' => 'miriam@email.com'],
        ['id' => 2, 'name' => 'Anthony', 'email' => 'anthony@email.com'],
        ['id' => 3, 'name' => 'Barry', 'email' => 'barry@email.com'],
        ['id' => 4, 'name' => 'Jack', 'email' => 'jack@email.com'],
    ];

    private $right = [
        ['id' => 1, 'city' => 'Luton'],
        ['id' => 2, 'city' => 'Tribogna'],
        ['id' => 3, 'city' => 'Midlands'],
    ];

    private $leftShouldBe = [
        ['id' => 1, 'name' => 'Miriam', 'email' => 'miriam@email.com', 'city' => 'Luton'],
        ['id' => 2, 'name' => 'Anthony', 'email' => 'anthony@email.com', 'city' => 'Tribogna'],
        ['id' => 3, 'name' => 'Barry', 'email' => 'barry@email.com', 'city' => 'Midlands'],
        ['id' => 4, 'name' => 'Jack', 'email' => 'jack@email.com'],
    ];

    public function testParseRightById()
    {
        $join = new Join($this->left, $this->right, 'id');
        $keys = array_keys($join->getRightParsedByAttr());

        $this->assertEquals($keys, array(1, 2, 3));
    }

    public function testParseRightByCity()
    {
        $join = new Join($this->left, $this->right, 'city');
        $keys = array_keys($join->getRightParsedByAttr());

        $this->assertEquals($keys, array('Luton', 'Tribogna', 'Midlands'));
    }

    public function testIfRightIsUniqueOnAttr()
    {
        $join = new Join($this->left, $this->right, 'id');
        $checkUnique = $this->invokeMethod($join, 'validateRight');

        $this->assertNull($checkUnique);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIfRightIsUniqueOnAttrWithNonUniqueValue()
    {
        $this->right[] = ['id' => 2, 'city' => 'Warsaw'];
        $join = new Join($this->left, $this->right, 'id');
        $this->invokeMethod($join, 'validateRight');
    }

    public function testMergedLeftJoin()
    {
        $join = new Join($this->left, $this->right, 'id');
        $merged = $join->leftJoin();

        $this->assertEquals($merged, $this->leftShouldBe);
    }

    public function testMergedInnerJoin()
    {
        $join = new Join($this->left, $this->right, 'id');
        $merged = $join->innerJoin();

        array_pop($this->leftShouldBe);

        $this->assertEquals($merged, $this->leftShouldBe);
    }

    public function testIteratorJoin()
    {
        $join = new Join($this->left, $this->right, 'id');
        $merged = $join->leftJoin();
        $this->assertEquals(count($this->leftShouldBe), count($merged));
    }

}
