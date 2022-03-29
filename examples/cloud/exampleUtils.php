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

require(__DIR__ . "/../../vendor/autoload.php");

use fiftyone\pipeline\core\Logger;

class ExampleLogger extends Logger
{
    public function logInternal($log)
    {
        $message = $this->settings["name"].":".$log["level"].": ".$log["message"]."\n";

        if (php_sapi_name() == "cli")
        {
            echo $message;
        }
        else
        {
            echo "<pre>$message</pre>";
        }
    }
}

class ExampleUtils
{
    // The default environment variable used to get the resource key 
    // to use when running examples.
    const RESOURCE_KEY_ENV_VAR = "resource_key";

    const ENDPOINT_ENV_VAR = "cloud_endpoint";
    
    public static function getResourceKey()
    {
        return ExampleUtils::getEnvVariable(ExampleUtils::RESOURCE_KEY_ENV_VAR);
    }

    public static function getCloudEndpoint()
    {
        return ExampleUtils::getEnvVariable(ExampleUtils::ENDPOINT_ENV_VAR);
    }
    
    private static function getEnvVariable($name)
    {
        $env = getenv();
        if (isset($env[$name]))
        {
            return $env[$name];
        }
        else
        {
            return "";
        }
    }

    public static function setResourceKeyInFile($configFile, $resourceKey)
    {
        $config = json_decode(file_get_contents($configFile), true);

        // Get the resource key setting from the config file. 
        $resourceKeyFromConfig = $config["PipelineOptions"]["Elements"][0]["BuildParameters"]["resourceKey"];
        $configHasKey = isset($resourceKeyFromConfig) && empty($resourceKeyFromConfig) == false &&
            strpos($resourceKeyFromConfig, "!!") === false; 

        // If no resource key is specified in the config file then override it with the key
        // from the environment variable / command line. 
        if ($configHasKey === false) {
            $config["PipelineOptions"]["Elements"][0]["BuildParameters"]["resourceKey"] = $resourceKey;
        }

        file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
    }
    
    public static function configFileHasResourceKey($configFile)
    {
        $config = json_decode(file_get_contents($configFile), true);

        return empty($config["PipelineOptions"]["Elements"][0]["BuildParameters"]["resourceKey"]) === false;
    }

    static function getLogger($name)
    {
        return new ExampleLogger("info", array("name" => $name));
    }

    public static function getHumanReadable($device, $name)
    {
        try
        {
            $value = $device->$name;
            if ($value->hasValue)
            {
                if (is_array($value->value))
                {
                    return implode(", ", $value->value);
                }
                else
                {
                    return $value->value;
                }
            }
            else
            {
                return "Unknown (".$value->noValueMessage.")";
            }
        }
        catch (Exception $e)
        {
            return "Property not found using the current resource key.";
        }
    }

    public static function containsAcceptCh()
    {
        foreach (headers_list() as $header)
        {
            $parts = explode(": ", $header);
            if (strtolower($parts[0]) === "accept-ch")
            {
                return true;
            }
        }
        return false;
    }
}
?>