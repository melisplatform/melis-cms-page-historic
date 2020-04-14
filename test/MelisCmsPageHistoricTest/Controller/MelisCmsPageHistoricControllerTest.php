<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoricTest\Controller;

use MelisCore\ServiceManagerGrabber;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
class MelisCmsPageHistoricControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = false;
    protected $sm;
    protected $method = 'save';

    public function setUp()
    {
        $this->sm  = new ServiceManagerGrabber();
    }



    public function getPayload($method)
    {
        return $this->sm->getPhpUnitTool()->getPayload('MelisCmsPageHistoric', $method);
    }

    /**
     * START ADDING YOUR TESTS HERE
     */

    public function testBasicMelisCmsPageHistoricTestSuccess()
    {
        $this->assertEquals("equalvalue", "equalvalue");
    }

}

