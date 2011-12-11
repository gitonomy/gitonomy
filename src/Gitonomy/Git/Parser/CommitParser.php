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

    public function parse($content)
    {
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

        if (!preg_match($pattern, $content, $vars)) {
            throw new \RuntimeException('Unable to parse a commit');
        }

        $this->tree           = $vars['tree'];
        $this->parents        = array($vars['parent']);
        $this->authorName     = $vars['author_name'];
        $this->authorEmail    = $vars['author_email'];
        $this->authorDate     = $this->parseDate($vars['author_date']);
        $this->committerName  = $vars['committer_name'];
        $this->committerEmail = $vars['committer_email'];
        $this->committerDate  = $this->parseDate($vars['committer_date']);
        $this->message        = $vars['message'];
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