<?php

namespace WebEtDesign\ActualityBundle\Cms;

use WebEtDesign\ActualityBundle\Entity\WDActuality;
use WebEtDesign\CmsBundle\Entity\GlobalVarsInterface;

class ActualityVars implements GlobalVarsInterface
{

    protected $objects = [];

    protected $routeParams = [];

    public function __call($name, $arguments)
    {
        if (!$this->objects) {
            return '';
        }

        $name = lcfirst(preg_replace(['/^get/', '/^is/'], '', $name));
        $key  = array_search($name, self::getAvailableVars());
        $name = is_string($key) ? $key : $name;

        $data   = explode('.', $name);
        $prefix = count($data) > 1 ? array_shift($data) : '';
        $name   = $data[0];

        $object = isset($this->objects[$prefix]) ? $this->objects[$prefix] : null;
        if (!$object) {
            return '';
        }

        $method = 'get' . ucfirst($name);
        if (method_exists($object, $method)) {
            return $object->$method();
        }

        $method = 'is' . ucfirst($name);
        if (method_exists($object, $method)) {
            return $object->$method();
        }

        if (method_exists($object, $name)) {
            return $object->$name();
        }

        if (property_exists($object, $name)) {
            return $object->$name;
        }

        return '';
    }

    public function getObjects()
    {
        return $this->objects;
    }

    public function getRouteParam($param)
    {
        return $this->routeParams[$param] ?? null;
    }

    public static function getAvailableVars(): array
    {
        return [
            'actualite.title' => 'actualite.titre',
            'actualite.category.title' => 'actualite.category',
        ];
    }

    public function __construct(WDActuality $actuality)
    {
        $this->objects = [
            'actualite' => $actuality,
        ];

        $this->routeParams = [
            'actuality' => $actuality->getSlug(),
            'category' => $actuality->getCategory() ? $actuality->getCategory()->getSlug() : '',
        ];
    }
}
