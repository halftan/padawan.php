<?php

namespace Padawan\Parser;

use Padawan\Domain\Project\Node\Variable;
use Padawan\Domain\Project\Node\Comment;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\Node\Expr\FuncCall;
use Psr\Log\LoggerInterface;

class InlineDocBlockParser {

    /**
     * Constructs
     *
     */
    public function __construct(
        LoggerInterface $logger,
        CommentParser $commentParser
    )
    {
        $this->logger        = $logger;
        $this->commentParser = $commentParser;
    }

    /**
     * Parses inline doc blocks
     *
     * @return Variable[]
     */
    public function parse(Node $node)
    {
        $result = [];
        $nodes  = [];
        $subNodes = $node->getSubNodeNames();

        foreach ($subNodes as $nodeName) {
            if ($node->$nodeName instanceof Node) {
                $nodes[] = $node->$nodeName;
            } else if (is_array($node->$nodeName)) {
                $nodes = array_merge($nodes,
                    array_filter($node->$nodeName, function($item) {
                        return $item instanceof Node;
                    })
                );
            }
        }
        if (empty($nodes)) {
            return $result;
        }
        foreach ($nodes AS $stmt) {
            $result = array_merge($result, $this->parse($stmt));
            $comments = $stmt->getAttribute('comments');
            if (empty($comments)) {
                continue;
            }
            foreach ($comments as $rawComment) {
                $text = trim($rawComment->getText());
                if (!empty($text)) {
                    if (strpos($text, '/**') !== 0) {
                        // only parse phpdoc
                        continue;
                    }
                    $comment = $this->commentParser->parse($text);
                    foreach ($comment->getVars() as $variable) {
                        /**
                         * @var $variable Variable
                         * @var $stmt \PhpParser\NodeAbstract
                         */
                        $line = $rawComment->getLine();
                        if ($line > 1) {
                            // make up line difference between parsers
                            $line -= 2;
                        }
                        $variable->setStartLine($line);
                        $result[] = $variable;
                    }
                }
            }
        }

        return $result;
    }

    /** @property CommentParser $commentParser */
    private $commentParser;
    /** @property LoggerInterface */
    private $logger;
}
