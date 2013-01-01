Feature: Get the version details

    Scenario: I need to authenticate to be able to administrate a project
        Given I logout
          And I am on "/admin/version"
         Then I should see "Login"
         When I fill:
            | Username | admin |
            | Password | admin |
          And I click on "Login"
        Then I should see "Current version"
