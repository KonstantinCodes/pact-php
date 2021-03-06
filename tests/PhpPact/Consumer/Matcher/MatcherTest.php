<?php

namespace PhpPact\Consumer\Matcher;

use Exception;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    /** @var Matcher */
    private $matcher;

    protected function setUp()
    {
        $this->matcher = new Matcher();
    }

    /**
     * @throws Exception
     */
    public function testLikeNoValue()
    {
        $this->expectException(Exception::class);
        $this->matcher->like(null);
    }

    /**
     * @throws Exception
     */
    public function testLike()
    {
        $json = \json_encode($this->matcher->like(12));

        $this->assertEquals('{"contents":12,"json_class":"Pact::SomethingLike"}', $json);
    }

    /**
     * @throws Exception
     */
    public function testEachLikeStdClass()
    {
        $object         = new \stdClass();
        $object->value1 = $this->matcher->like(1);
        $object->value2 = 2;

        $expected = \json_encode([
            'contents' => [
                'value1' => [
                    'contents'   => 1,
                    'json_class' => 'Pact::SomethingLike'
                ],
                'value2' => 2
            ],
            'json_class' => 'Pact::ArrayLike',
            'min'        => 1
        ]);

        $actual = \json_encode($this->matcher->eachLike($object, 1));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testEachLikeArray()
    {
        $object = [
            'value1' => $this->matcher->like(1),
            'value2' => 2
        ];

        $expected = \json_encode([
            'contents' => [
                'value1' => [
                    'contents'   => 1,
                    'json_class' => 'Pact::SomethingLike'
                ],
                'value2' => 2
            ],
            'json_class' => 'Pact::ArrayLike',
            'min'        => 1
        ]);

        $actual = \json_encode($this->matcher->eachLike($object, 1));

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testRegexNoMatch()
    {
        $this->expectException(Exception::class);
        $this->matcher->regex('SomeWord', 'BadPattern');
    }

    /**
     * @throws Exception
     */
    public function testRegex()
    {
        $expected = [
            'data' => [
                'generate' => 'Games',
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => 'Games|Other'
                ]
            ],
            'json_class' => 'Pact::Term'
        ];

        $actual = $this->matcher->regex('Games', 'Games|Other');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws Exception
     */
    public function testDate()
    {
        $expected = [
            'data' => [
                'generate' => '2010-01-17',
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => '^([\\+-]?\\d{4}(?!\\d{2}\\b))((-?)((0[1-9]|1[0-2])(\\3([12]\\d|0[1-9]|3[01]))?|W([0-4]\\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\\d|[12]\\d{2}|3([0-5]\\d|6[1-6])))?)$'
                ]
            ],
            'json_class' => 'Pact::Term'
        ];

        $actual = $this->matcher->dateISO8601('2010-01-17');

        $this->assertEquals($expected, $actual);
    }
}
