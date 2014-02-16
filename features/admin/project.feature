Feature: Administrate a project

    Scenario: I need to authenticate to be able to administrate a project
        Given I logout
          And I am on "/projects/foobar/admin"
         Then I should see "Login"

    Scenario: I need to have sufficient credentials to administrate a project
        Given I am connected as "alice"
          And I am on "/projects/foobar/admin"
         Then I should not see "Delete Foobar"

    Scenario: I have sufficient credentials to administrate roles
        Given I am connected as "admin"
          And I am on "/projects/foobar/admin"
         Then I should see "Delete Foobar"

    Scenario: As administrator, I can remove a project
        Given project "removed" exists
          And I am connected as "admin"
          And I am on "/projects/removed/admin"
         When I click on "xpath=//a[contains(@data-confirm, ""Yes, I want to delete Removed"")]"
         Then I should see "Yes, I want to delete Removed"
         When I click on "Yes, I want to delete Removed"
         Then I should see "Project removed"
