Feature: Administrate a project

    Scenario: I need to authenticate to be able to administrate a project
        Given I logout
          And I am on "/projects/foobar/admin"
         Then I should see "Login"
         When I fill:
            | Username | admin |
            | Password | admin |
          And I click on "Login"
        Then I should see "I want to delete foobar"

    Scenario: I need to have sufficient credentials to administrate a project
        Given I logout
          And I am on "/projects/foobar/admin"
         Then I should see "Login"
         When I fill:
            | Username | alice |
            | Password | alice |
          And I click on "Login"
        Then I should not see "I want to delete foobar"
