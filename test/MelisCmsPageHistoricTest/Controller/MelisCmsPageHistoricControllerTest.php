<?php

/**
 * Melis Technology (http://www.melistechnology.com)
 *
 * @copyright Copyright (c) 2016 Melis Technology (http://www.melistechnology.com)
 *
 */

namespace MelisCmsPageHistoricTest\Controller;

use PHPUnit_Framework_TestCase;
use MelisCmsProspectsTest\ServiceManagerGrabber;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
class MelisCmsPageHistoricControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = false;

    public function setUp()
    {
        $this->setApplicationConfig(
            include '../../../config/test.application.config.php'
        );

        parent::setUp();

    }

    public function testBasicMelisCmsPageHistoricTestSuccess()
    {
        $this->assertEquals("equalvalue", "equalvalue");
    }

    public function testBasicMelisCmsPageHistoricTestError()
    {
        $this->assertEquals("supposed-to", "display-an-error");
    }

}


