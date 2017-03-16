<?php

namespace Padawan\Framework\Domain\Project;


use Padawan\Domain\Project;
use Padawan\Domain\Project\Node\ClassData;
use Padawan\Domain\Project\FQCN;
use Padawan\Domain\Project\ClassRepository as ClassRepositoryInterface;

/**
 * Class ClassRepository
 * @author
 */
class ClassRepository implements ClassRepositoryInterface
{
    public function findByName(Project $project, FQCN $name)
    {
        $index = $project->getIndex();
        return $this->_findClass($index, $name);
    }

    public function findAllByNamePart(Project $project, $name = "")
    {
        if (empty($name)) {
            return $project->getIndex()->getClasses();
        }
        $classes = [];
        foreach ($project->getIndex()->getClasses() as $class) {
            if (strpos($class->fqcn->toString(), $name) !== false) {
                $classes[] = $class;
            }
        }
        return $classes;
    }

    private function _findClass(&$index, FQCN $name)
    {
        $class = $index->findClassByFQCN($name);
        if (empty($class)) {
            $class = $index->findInterfaceByFQCN($name);
        } else {
            $parent = $class->getParent();
            while (!empty($parent)) {
                if ($parent instanceof FQCN) {
                    $parent = $this->_findClass($index, $parent);
                    if (empty($parent)) {
                        break;
                    }
                    $class->setParent($parent);
                }
                $parent = $parent->getParent();
            }
        }
        return $class;
    }
}
