Feature: Menu

    Scenario: I can view labels
        Given I am connected as "alice"
          And I am on "/projects/foobar/history"
         Then I should not see "My projects"
         When I move mouse to "css=#global-menu a.active"
         Then I should see "My projects"
