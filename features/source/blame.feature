Feature: Blame feature

    Scenario: I can see authors of a file
        Given I am connected as "alice"
          And user "alice" is "Lead developer" on "foobar"
         When I am on "/projects/foobar/tree/new-feature/run.php"
         Then I should see an action "Blame" in contextual navigation
         When I click on "Blame" in contextual navigation
         Then I should see 2 "xpath=//table[@class='blame']//a[contains(., 'Add the run script')]"
         Then I should see 1 "xpath=//table[@class='blame']//a[contains(., 'A new feature')]"

    Scenario: I can't see blame of a folder
        Given I am connected as "alice"
          And user "alice" is "Lead developer" on "foobar"
         When I am on "/projects/foobar/tree/master"
         Then I should not see an action "Blame" in contextual navigation

    Scenario: I can't see blame of a binary file
        Given I am connected as "alice"
          And user "alice" is "Lead developer" on "foobar"
         When I am on "/projects/foobar/tree/master/image.jpg"
         Then I should not see an action "Blame" in contextual navigation
