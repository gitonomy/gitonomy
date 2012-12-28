Feature: Administrate roles globally

    Scenario: An administrator needs to authenticate to administrate roles
        Given I logout
          And I am on "/admin/roles"
         Then I should see "Login"
         When I fill:
            | Username | admin |
            | Password | admin |
          And I click on "Login"
        Then I should see "Master of the application"

    Scenario: I need to have sufficient credentials to administrate roles
        Given I logout
          And I am on "/admin/roles"
         Then I should see "Login"
         When I fill:
            | Username | alice |
            | Password | alice |
          And I click on "Login"
        Then I should not see "Master of the application"
