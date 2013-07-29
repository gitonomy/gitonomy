Feature: View tags

    Scenario: Can see tags
     Given I am connected as "alice"
      When I am on "/projects/foobar/tags"
      Then I should see "No tag found"
