Feature: View project

    Scenario: Can't view if not connected and redirected to target
      When I am on "/projects/foobar"
      Then I should not see "foobar"
       And I should see "Login"
      When I fill:
        | Username | alice |
        | Password | alice |
      Then I click on "Login"
      Then I should see "Alice pushed"

    Scenario: Can't view if not allowed
     Given I am connected as "bob"
      When I am on "/projects/barbaz"
      Then I should see "You are not contributor of the project"

    Scenario: Can view project correctly
     Given I am connected as "alice"
      When I am on "/projects/foobar"
      Then I should see "Alice pushed"

    Scenario: Can view empty project
     Given I am connected as "alice"
      When I am on "/projects/empty"
      Then I should see "How to make your first commit"
