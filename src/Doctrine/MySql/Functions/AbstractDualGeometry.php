<?php

declare(strict_types = 1);

namespace DevTools\Doctrine\MySql\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

abstract class AbstractDualGeometry extends FunctionNode
{
    /**
     * @var Node
     */
    protected $firstGeomExpression;

    /**
     * @var Node
     */
    protected $secondGeomExpression;

    protected string $functionName;

    public function getSql(SqlWalker $sqlWalker)
    {
        return sprintf(
            '%s(%s, %s)',
            $this->functionName,
            $this->firstGeomExpression->dispatch($sqlWalker),
            $this->secondGeomExpression->dispatch($sqlWalker)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->firstGeomExpression = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->secondGeomExpression = $parser->ArithmeticPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
