<?php

namespace Padawan\Domain\Completer;

use Padawan\Domain\Project;
use Padawan\Domain\Completion\Context;
use Padawan\Domain\Completion\Entry;
use Padawan\Domain\Project\FQN;
use Psr\Log\LoggerInterface;

class ConstantCompleter extends AbstractInCodeBodyCompleter
{
    public function getEntries(Project $project, Context $context) {
        $entries = [];
        $postfix = $this->getPostfix($context);
        $this->logger->debug('Completing word: ' . $postfix);
        $fqn = '';
        $candidates = [];

        if ($postfix[0] === '\\') {
            // using fqn to refer to constants
            $postfix = ltrim($postfix, '\\');
            $parts = explode('\\', $postfix);
            // last part as keyword, the rest is FQN
            $postfix = array_pop($parts);
            $fqn = new FQN(implode('\\', $parts));
        } else {
            // using current namespace
            $fqn = $context->getScope()->getNamespace();
            $candidates = array_merge($candidates, $project->getIndex()->findConstantsInNamespace(new FQN()));
        }
        $candidates = array_merge($candidates, $project->getIndex()->findConstantsInNamespace($fqn));
        if (!empty($postfix)) {
            $candidates = array_filter($candidates, function($val) use($postfix) {
                return strpos($val, $postfix) === 0;
            });
        }
        $candidates = array_unique($candidates);
        return $this->formatEntries($candidates, $postfix);
    }

    private function formatEntries($candidates, $postfix)
    {
        $entries = [];
        foreach ($candidates as $name) {
            $pattern = preg_quote($postfix, '#');
            $complete = preg_replace("#$pattern#", '', $name, 1);
            $entries[] = new Entry(
                $complete, '', '', $name
            );
        }

        return $entries;
    }

    public function canHandle(Project $project, Context $context)
    {
        if ($context->isUse()) return false;

        $postfix = $this->getPostfix($context);
        return parent::canHandle($project, $context)
            && ($context->isString() || $context->isEmpty())
            && strlen($postfix) > 0
            ;
    }

    private function getPostfix(Context $context)
    {
        if (is_string($context->getData())) {
            $symbols = trim($context->getData());
            $symbols = explode(' ', $symbols);
            return trim($symbols[count($symbols) - 1]);
        }
        return "";
    }

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
}
