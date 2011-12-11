<?php

namespace Gitonomy\Git\Parser;

class CommitParser
{
    public $tree;
    public $parents;
    public $authorName;
    public $authorEmail;
    public $authorDate;
    public $committerName;
    public $committerEmail;
    public $committerDate;
    public $message;

    protected $cursor;
    protected $content;

    public function parse($content)
    {
        $this->cursor  = 0;
        $this->content = $content;

        $this->consume('tree ');
        $this->tree = $this->consumeHash();
        $this->consumeNewLine();

        $this->parents = array();
        while ($this->expects('parent ')) {
            $this->parents[] = $this->consumeHash();
            $this->consumeNewLine();
        }

        $this->consume('author ');
        list($this->authorName, $this->authorEmail, $this->authorDate) = $this->consumeNameEmailDate();
        $this->authorDate = $this->parseDate($this->authorDate);
        $this->consumeNewLine();

        $this->consume('committer ');
        list($this->committerName, $this->committerEmail, $this->committerDate) = $this->consumeNameEmailDate();
        $this->committerDate = $this->parseDate($this->committerDate);
        $this->consumeNewLine();

        $this->consumeNewLine();
        $this->message = $this->consumeAll();

        $pattern = '/'.
            'tree (?<tree>[A-Za-z0-9]{40})'.
            "\n".
            '(parent (?<parent>[A-Za-z0-9]{40})'.
            "\n)*".
            'author (?<author_name>.*) <(?<author_email>.*)> (?<author_date>\d+ [+-]\d{4})'.
            "\n".
            'committer (?<committer_name>.*) <(?<committer_email>.*)> (?<committer_date>\d+ [+-]\d{4})'.
            "\n\n".
            '(?<message>[^$]*)'.
            '/'
        ;
    }

    private function consumeAll()
    {
        $rest = mb_substr($this->content, $this->cursor);
        $this->cursor += mb_strlen($rest);

        return $rest;
    }

    private function consumeNameEmailDate()
    {
        if (!preg_match('/(([^\n]*) <([^\n]*)> (\d+ [+-]\d{4}))/A', $this->content, $vars, 0, $this->cursor)) {
            throw new \RuntimeException('Unable to parse name, email and date');
        }

        $this->cursor += mb_strlen($vars[1]);

        return array($vars[2], $vars[3], $vars[4]);
    }

    private function expects($expected)
    {
        $length = mb_strlen($expected);
        $actual = mb_substr($this->content, $this->cursor, $length);
        if ($actual !== $expected) {
            return false;
        }

        $this->cursor += $length;

        return true;
    }

    private function consumeHash()
    {
        if (!preg_match('/([A-Za-z0-9]{40})/A', $this->content, $vars, null, $this->cursor)) {
            throw new \RuntimeException('No hash found');
        }

        $this->cursor += 40;

        return $vars[1];
    }

    private function consume($expected)
    {
        $length = mb_strlen($expected);
        $actual = mb_substr($this->content, $this->cursor, $length);
        if ($actual !== $expected) {
            throw new \RuntimeException(sprintf('Expected "%s", but got "%s"', $expected, $actual));
        }
        $this->cursor += $length;

        return $expected;
    }

    private function consumeNewLine()
    {
        return $this->consume("\n");
    }

    protected function parseDate($text)
    {
        $date = \DateTime::createFromFormat('U e O', $text.' UTC');

        if (!$date instanceof \DateTime) {
            throw new \RuntimeException(sprintf('Unable to convert "%s" to datetime', $text));
        }

        return $date;
    }
}