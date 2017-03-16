<?php

namespace Padawan\Domain\Completer;

use Padawan\Domain\Project;
use Padawan\Domain\Completion\Entry;
use Padawan\Domain\Completion\Context;
use Padawan\Domain\Project\ClassRepository;
use Padawan\Domain\Project\Node\FunctionData;

class GlobalFunctionsCompleter extends AbstractInCodeBodyCompleter
{

    /** @property ClassRepository */
    private $classRepository;

    public function __construct(
        ClassRepository $classRepository
    ) {
        $this->classRepository = $classRepository;
    }

    public function getEntries(Project $project, Context $context)
    {
        $entries = [];
        $postfix = $this->getPostfix($context);
        if (empty($postfix)) {
            return [];
        }
        $pattern = preg_quote($postfix, '#');
        foreach ($project->getIndex()->getFunctions() as $function) {
            /** @var FunctionData $function */
            $name = $function->name;
            if (strpos($name, $postfix) === 0) {
                $nameToComplete = preg_replace("#$pattern#", "", $name, 1);
                $entries[$name] = new Entry(
                    $nameToComplete,
                    $function->getSignature(),
                    $function->doc,
                    $name
                );
            }
        }
        $entries = array_values($entries);
        return $entries;
    }

    public function canHandle(Project $project, Context $context)
    {
        $postfix = $this->getPostfix($context);
        return parent::canHandle($project, $context)
            && (
                $context->isString()
                || $context->isEmpty()
                || $context->isMethodCall()
            )
            && strlen($postfix) > 0
            && !$context->isObject()
            ;
    }

    private function getPostfix(Context $context)
    {
        if (is_string($context->getData())) {
            return trim($context->getData());
        }
        if (empty($postfix)) {
            $contextData = $context->getData();
            if (is_array($contextData) && @$contextData[3] instanceof \PhpParser\Node) {
                $node = $contextData[3];
                if (!empty($node->name)) {
                    $postfix = $node->name;
                    $context->addType(Context::T_ANY_NAME);
                    return trim($postfix);
                }
                if (!empty($node->value) && $node->value instanceof \PhpParser\Node\Expr\ConstFetch) {
                    $postfix = $node->value->name;
                    $context->addType(Context::T_ANY_NAME);
                    return trim($postfix);
                }
            }
        }
        return '';
    }
}
