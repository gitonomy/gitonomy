Feature: Browse history

    Scenario: Can browse other branches
     Given I am connected as "alice"
      When I am on "/projects/foobar/history?branch=pagination"
      Then I should see "Add element_100"

    Scenario: Can view history page
     Given I am connected as "alice"
      When I am on "/projects/foobar/history"
      Then I should see "rename file"
