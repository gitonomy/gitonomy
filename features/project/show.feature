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

    Scenario: Can view history page
     Given I am connected as "alice"
      When I am on "/projects/foobar/history"
      Then I should see "rename file"

    Scenario: Can browse other branches
     Given I am connected as "alice"
      When I am on "/projects/foobar/history"
       And I click on "All branches"
       And I click on "pagination"
      Then I should see "Add element_100"

    Scenario: Can see newsfeed
     Given I am connected as "alice"
      When I am on "/projects/foobar"
      Then I should see "Lead deleted branch to-delete"

    Scenario: Can see source
     Given I am connected as "alice"
      When I am on "/projects/foobar/tree/master"
      Then I should see "modify image"

    Scenario: Can see an image
     Given I am connected as "alice"
      When I am on "/projects/foobar/tree-history/master/image.jpg"
      Then I should see "add an image"

    Scenario: Can see branches
     Given I am connected as "alice"
      When I am on "/projects/foobar/branches"
      Then I should see "Add element_100"

    Scenario: Can see tags
     Given I am connected as "alice"
      When I am on "/projects/barbaz/tags"
      Then I should see "No tag found"
