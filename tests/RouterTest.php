<?php
/*
 * Copyright (c) 2014 Alberto González
 * Distributed under MIT License
 * (see README for details)
 */

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;


class Router extends \PHPUnit_Framework_TestCase
{
    public $url;
    
    public function testgetView()
    {
        $_url = '/register/';
        $routes = null;
        $return = false;
        $view = false;

        try {
            $yaml = new Parser();
            $routes = $yaml->parse(file_get_contents(__DIR__ . '/../Quaver/Routes.yml'));
        } catch (ParseException $e) {
            throw new \Quaver\Core\Exception("Unable to parse the YAML string: %s", $e->getMessage());
        }

        foreach ($routes as $item) {
            $regexp = "/^" . str_replace(array("/", "\\\\"), array("\/", "\\"), $item['url']) . "$/";
            preg_match($regexp, $_url, $match);

            if ($match) {
                $this->url = array(
                    "uri" => array_splice($match, 1),
                    "path" => $match[0],
                );
                $view = $item;
                break;
            }
        }

        $this->assertEquals('register', $view['controller']);

        if ($view) {
            $return = $view;
        } else {
            $this->dispatch('e404');
        }

        return $return;
    }
}
