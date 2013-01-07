Feature: Get the version details

    Scenario: I need to authenticate to be able to administrate a project
        Given I logout
          And I am on "/admin/version"
         Then I should see "Login"

    Scenario: I need to authenticate to be able to administrate a project
        Given I am connected as "admin"
          And I am on "/admin/version"
         Then I should see "Current version"
