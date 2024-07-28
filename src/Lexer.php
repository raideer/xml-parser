<?php

declare(strict_types=1);

namespace Raideer\XmlParser;

use Raideer\XmlParser\Lexer\Grammar;
use Raideer\XmlParser\Lexer\Rule;

class Lexer
{
    private int $currentMode = Rule::MODE_DEFAULT;

    /**
     * @var int[]
     */
    private array $modeStack = [];

    /**
     * @var array<Rule[]>
     */
    private array $modeRules = [];

    /**
     * @var string[]
     */
    private array $modePatterns = [];

    private int $offset = 0;

    public function __construct()
    {
        $grammarRules = Grammar::rules();

        foreach ($grammarRules as  $rule) {
            foreach ($rule->modes as $mode) {
                // Using a string key so we can use it as name of the capture group
                $this->modeRules[$mode]['T' . $rule->token] = $rule;
            }
        }

        $this->buildModePatterns();
    }

    /**
     * Splits raw XML string into tokens
     * 
     * @param string $input 
     * @return Token[] 
     */
    public function tokenize(string $input)
    {
        $inputLen = strlen($input);
        $this->offset = 0;
        $tokens = [];

        do {
            $matched = $this->matchNextToken($input);

            if ($matched === true) {
                continue;
            } elseif ($matched === false) {
                break;
            }

            $tokens[] = $matched;
        } while (true);

        // Check if we managed to tokenize the whole XML file or if it stopped due to an error
        if ($this->offset === $inputLen) {
            $tokens[] = new Token(TokenKind::EOF, '', $this->offset);
        } else {
            $tokens[] = new Token(TokenKind::ERROR, substr($input, $this->offset), $this->offset);
        }

        return $tokens;
    }

    /**
     * @param string $input
     * @return bool|Token
     */
    private function matchNextToken(string $input)
    {
        $modePattern = $this->modePatterns[$this->currentMode];

        if (!preg_match($modePattern, $input, $result, PREG_OFFSET_CAPTURE, $this->offset)) {
            return false;
        }

        $rule = $this->getMatchedRule($result);

        $firstMatch = $result[0];

        // Last result "should" be the unnamed value capture group if it's set
        $ruleMatch = end($result);
        [$matchValue, $matchOffset] = $ruleMatch;

        if ($rule->hasFlag(Rule::FLAG_PUSH_INSIDE)) {
            $this->modeStack[] = $this->currentMode;
            $this->currentMode = Rule::MODE_INSIDE;
        } elseif ($rule->hasFlag(Rule::FLAG_POP_MODE)) {
            $this->currentMode = array_pop($this->modeStack) ?? Rule::MODE_DEFAULT;
        }

        $this->offset = $firstMatch[1] + strlen($firstMatch[0]);

        if ($rule->hasFlag(Rule::FLAG_SKIP)) {
            return true;
        }

        return new Token($rule->token, $matchValue, (int) $matchOffset);
    }

    /**
     * TODO: figure out how to find the rule faster since this adds a significant processing time
     * 
     * @param array $result
     * @return Rule|null
     */
    private function getMatchedRule(array $result)
    {
        foreach ($this->modeRules[$this->currentMode] as $rule) {
            $tokenKey = 'T' . $rule->token;

            if (isset($result[$tokenKey]) && $result[$tokenKey][1] !== -1) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * @param Rule[] $rules
     * @return string
     */
    private function makePattern(array $rules)
    {
        $patterns = [];

        foreach ($rules as $rule) {
            $patterns[] =  '(?<T' . $rule->token . '>' . $rule->pattern . ')';
        }

        return '/' . implode('|', $patterns) . '/Au';
    }

    /**
     * @return void
     */
    private function buildModePatterns()
    {
        $this->modePatterns = [];

        foreach ($this->modeRules as $mode => $rules) {
            $this->modePatterns[$mode] = $this->makePattern($rules);
        }
    }
}
