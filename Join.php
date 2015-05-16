<?php

namespace Join;

/**
 * Class simulates behaviour of LEFT and INNER joins.
 *
 * @package Join
 */
class Join extends \ArrayIterator
{
    const LEFT = 'LEFT';
    const INNER = 'INNER;';

    private $left = array();
    private $right = array();
    private $onAttr;
    private $rightParsedByAttr = array();
    private $merged = array();

    /**
     * @param array $left
     * @param array $right
     * @param $onAttr
     */
    public function __construct(array $left, array $right, $onAttr)
    {
        $this->left = $left;
        $this->right = $right;
        $this->onAttr = $onAttr;

        $this->parseOnAttr();
        $this->validateRight();
        parent::__construct($this->merged);

    }

    private function validateRight()
    {
        if (count($this->right) !== count($this->rightParsedByAttr)) {
            throw new \InvalidArgumentException('Values must be unique.');
        }
    }

    private function parseOnAttr()
    {
        foreach ($this->right as $key => $value) {
            $newKey = $value[$this->onAttr];
            $this->rightParsedByAttr[$newKey] = $value;
        }
    }

    public function getRightParsedByAttr()
    {
        return $this->rightParsedByAttr;
    }

    public function leftJoin()
    {
        return $this->parse(self::LEFT);
    }

    public function innerJoin()
    {
        return $this->parse(self::INNER);
    }

    /**
     * @param $type LEFT|INNER
     * @return array
     */
    private function parse($type)
    {
        $this->merged = array();
        foreach ($this->left as $key => $value) {
            $rightKey = $value[$this->onAttr];
            if (array_key_exists($rightKey, $this->rightParsedByAttr)) {
                $this->merged[] = array_merge($value, $this->rightParsedByAttr[$rightKey]);
            } elseif ($type === self::LEFT) {
                $this->merged[] = $value;
            }
        }

        return $this->merged;
    }

}
