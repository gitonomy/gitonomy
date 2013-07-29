Feature: Branches

    Scenario: Can see branches
     Given I am connected as "alice"
      When I am on "/projects/foobar/branches"
      Then I should see "Add element_100"
