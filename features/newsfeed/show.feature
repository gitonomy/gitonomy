Feature: Browser newsfeed

    Scenario: Can see newsfeed
     Given I am connected as "alice"
      When I am on "/projects/foobar"
      Then I should see "Lead deleted branch to-delete"
