Feature: Administrate users globally

    Scenario: I need to authenticate to be able to administrate users
        Given I logout
          And I am on "/admin/users"
         Then I should see "Login"

    Scenario: I need to have sufficient credentials to administrate users
        Given I am connected as "alice"
          And I am on "/admin/users"
         Then I should not see "New user"

    Scenario: I need to authenticate to be able to administrate users
        Given I am connected as "admin"
          And I am on "/admin/users"
         Then I should see "New user"
          And I should see 0 "xpath=//a[contains(@data-confirm, ""Yes, I want to delete Admin"")]"

    Scenario: As administrator, I can remove an user
        Given user "inexisting" exists
          And I am connected as "admin"
          And I am on "/admin/users"
          And I click on "xpath=//a[contains(@data-confirm, ""Yes, I want to delete Inexisting"")]"
         Then I should see "Yes, I want to delete Inexisting"
          And I click on "Yes, I want to delete Inexisting"
          And I should see "User deleted"
