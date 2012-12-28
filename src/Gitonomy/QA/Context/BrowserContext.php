<?php

namespace Gitonomy\QA\Context;

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

use WebDriver\Browser;
use WebDriver\By;

class BrowserContext extends BaseBrowserContext
{
    /**
     * @Given /^I am on "(.+)"$/
     */
    public function iAmOn($url)
    {
        if (!preg_match('#^https?://#', $url)) {
            $url = $this->baseUrl . (substr($url, 0, 1) == "/" ? substr($url, 1) : $url);
        }

        $this->getBrowser()->open($url);
    }

    /**
     * @Then /^I should see a title "(.*)"$/
     */
    public function iShouldSeeATitle($text)
    {
        $title = $this->getBrowser()->getTitle();
        if ($text !== $title) {
            throw new \RuntimeException(sprintf('Expected title to be "%s", got "%s"', $text, $tile));
        }
    }

    /**
     * @Then /^I should see (\d+) (xpath|tag|css|class|id|name) elements? "(.*)"$/
     */
    public function iShouldSeeElements($count, $type, $value)
    {
        if ($type == 'tag') {
            $type = 'tag name';
        } elseif ($type == 'css') {
            $type = 'css selector';
        } elseif ($type == 'class') {
            $type = 'class name';
        }

        $elements = $this->getBrowser()->elements(new By($type, $value));

        if (count($elements) != $count) {
            throw new \InvalidArgumentException(sprintf("Expected %s elements, got %s", $count, count($elements)));
        }
    }

    /**
     * @Given /^I click on "(.*)"$/
     */
    public function iClickOn($text)
    {
        $this->getBrowser()->element(By::xpath('//a[contains(text(),"'.$text.'")]|//input[@type="submit" and contains(@value, "'.$text.'")]|//button[contains(text(),"'.$text.'")]|//button[contains(@value, "'.$text.'")]'))->click();
    }

    /**
     * @Then /^I should (not )?see "(.*)"$/
     */
    public function iShouldSee($not, $text)
    {
        $all = $this->getBrowser()->element(By::tag('body'))->text();
        $pos = strpos($all, $text);
        if ($not === "" && false === $pos) {
            throw new \RuntimeException('Unable to find "'.$text.'" in visible text :'."\n".$all);
        } elseif ($not === "not " && false !== $pos) {
            throw new \RuntimeException('Found text "'.$text.'" in visible text :'."\n".$all);
        }
    }

    /**
     * @When /^I fill:$/
     */
    public function iFill(TableNode $table)
    {
        foreach ($table->getRowsHash() as $key => $value) {
            $this->iFillWith($key, $value);
        }
    }

    /**
     * @Then /^I fill "(.*)" with "(.*)"$/
     */
    public function iFillWith($field, $value)
    {
        $label = $this->getBrowser()->element(By::xpath('//label[contains(text(), "'.$field.'")]'));
        $for = $label->attribute('for');
        $input = $this->getBrowser()->element(By::id($for));

        if ($input->name() == 'select') {
            $input->element(By::xpath('//option[contains(text(), "'.$value.'")]'))->click();
        } else {
            $input->clear();
            $input->value($value);
        }
    }
}
