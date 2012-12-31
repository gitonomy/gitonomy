Feature: Blame feature

    Scenario: I can visualize authors of a file
        Given I am connected as "alice"
          And I am on "/projects/foobar/blame/new-feature/run.php"
         Then I should see 2 xpath elements "//table[@class='blame']//a[contains(., 'Add the run script')]"
         Then I should see 1 xpath element "//table[@class='blame']//a[contains(., 'A new feature')]"
