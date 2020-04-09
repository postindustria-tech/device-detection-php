<?php
/* *********************************************************************
 * This Original Work is copyright of 51 Degrees Mobile Experts Limited.
 * Copyright 2019 51 Degrees Mobile Experts Limited, 5 Charlotte Close,
 * Caversham, Reading, Berkshire, United Kingdom RG4 7BY.
 *
 * This Original Work is licensed under the European Union Public Licence (EUPL) 
 * v.1.2 and is subject to its terms as set out below.
 *
 * If a copy of the EUPL was not distributed with this file, You can obtain
 * one at https://opensource.org/licenses/EUPL-1.2.
 *
 * The 'Compatible Licences' set out in the Appendix to the EUPL (as may be
 * amended by the European Commission) shall be deemed incompatible for
 * the purposes of the Work and the provisions of the compatibility
 * clause in Article 5 of the EUPL shall not apply.
 * 
 * If using the Work as, or as part of, a network application, by 
 * including the attribution notice(s) required under Article 5 of the EUPL
 * in the end user terms of the application under an appropriate heading, 
 * such notice(s) shall fulfill the requirements of that article.
 * ********************************************************************* */


require(__DIR__ . "/../vendor/autoload.php");

// Fake remote address for web integration

$_SERVER["REMOTE_ADDR"] = "0.0.0.0";

use PHPUnit\Framework\TestCase;
use fiftyone\pipeline\devicedetection\deviceDetectionPipelineBuilder;

class exampleTests extends TestCase
{

    public function testPropertyValueBad()
	{
        $builder1 = new deviceDetectionPipelineBuilder(array(
            "resourceKey" => $_ENV["RESOURCEKEY"]
        ));

        $badUA = 'w5higsnrg';

        $pipeline1 = $builder1->build();

        $flowData1 = $pipeline1->createFlowData();

        $flowData1->evidence->set("header.user-agent", $badUA);

        $result = $flowData1->process();
        
		$this->assertFalse($result->device->ismobile->hasValue);
        $this->assertEquals($result->device->ismobile->noValueMessage, 
            "The results contained a null profile for the component which the required property belongs to.");
    }

    public function testPropertyValueGood()
	{

        $builder = new deviceDetectionPipelineBuilder(array(
            "resourceKey" => $_ENV["RESOURCEKEY"]
        ));

        $iPhoneUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_2 like Mac OS X) AppleWebKit/604.4.7 (KHTML, like Gecko) Mobile/15C114';

        $pipeline = $builder->build();

        $flowData = $pipeline->createFlowData();

        $flowData->evidence->set("header.user-agent", $iPhoneUA);

        $result = $flowData->process();
        
		$this->assertTrue($result->device->ismobile->value);

    }

    public function testGetProperties()
	{

        $builder = new deviceDetectionPipelineBuilder(array(
            "resourceKey" => $_ENV["RESOURCEKEY"]
        ));

        $iPhoneUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 11_2 like Mac OS X) AppleWebKit/604.4.7 (KHTML, like Gecko) Mobile/15C114';

        $pipeline = $builder->build();

        $flowData = $pipeline->createFlowData();

        $flowData->evidence->set("header.user-agent", $iPhoneUA);

        $result = $flowData->process();

        $properties = $pipeline->getElement("device")->getProperties();

		$this->assertEquals($properties["ismobile"]["Name"], "IsMobile");
		$this->assertEquals($properties["ismobile"]["Type"], "Boolean");
		$this->assertEquals($properties["ismobile"]["Category"], "Device");
        
    }

    public function testFailureToMatch()
	{

        include __DIR__ . "/../examples/cloud/failureToMatch.php";

		$this->assertTrue(true);

    }

	public function testGettingStarted()
	{

        include __DIR__ . "/../examples/cloud/gettingstarted.php";
        
        $this->assertTrue(true);

    }
    
    public function testMetaData()
	{

        include __DIR__ . "/../examples/cloud/metadata.php";
        
        $this->assertTrue(true);

    }
    
    public function testWebIntegration()
	{

        include __DIR__ . "/../examples/cloud/webIntegration.php";
        
        $this->assertTrue(true);

	}

}
