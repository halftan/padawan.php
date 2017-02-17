<?php

namespace Padawan\Domain\Scope;

use Padawan\Domain\Project\FQN;
use Padawan\Domain\Project\Node\Uses;
use Padawan\Domain\Project\Node\ClassData;
use Padawan\Domain\Project\Node\InterfaceData;

class FileScope extends AbstractScope
{
    private $classes = [];
    private $interfaces = [];
    /** @var FQCN */
    private $namespace;
    /** @var Uses */
    private $uses;

    public function __construct(FQN $namespace, Uses $uses = null)
    {
        $this->uses = $uses;
        $this->namespace = $namespace;
    }
    public function getNamespace()
    {
        if (empty($this->uses)) {
            return new FQN();
        }
        return $this->uses->getFQCN();
    }
    public function getUses()
    {
        return $this->uses;
    }
    public function getClasses()
    {
        return $this->classes;
    }
    public function getClass($className)
    {
        if (array_key_exists($className, $this->classes)) {
            return $this->classes[$className];
        }
    }
    public function addClass(ClassData $class)
    {
        $this->classes[$class->getName()] = $class;
    }
    public function getInterfaces()
    {
        return $this->interfaces;
    }
    public function getInterface($interfaceName)
    {
        if (array_key_exists($interfaceName, $this->interfaces)) {
            return $this->interfaces[$interfaceName];
        }
    }
    public function addInterface(InterfaceData $interface)
    {
        $this->interfaces[$interface->getName()] = $interface;
    }
}
