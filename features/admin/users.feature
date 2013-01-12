Feature: Administrate users globally

    Scenario: I need to authenticate to be able to administrate users
        Given I logout
          And I am on "/admin/users"
         Then I should see "Login"

    Scenario: I need to authenticate to be able to administrate users
        Given I am connected as "admin"
          And I am on "/admin/users"
         Then I should see "bob@example.org"

    Scenario: I can remove an user
        Given user "waldo" exists
          And I am connected as "admin"
          And I am on "/admin/users"
          And I click on xpath "//a[contains(@data-confirm, "Yes, I want to delete Waldo")]"
         Then I should see "Yes, I want to delete Waldo"
          And I click on "Yes, I want to delete Waldo"
          And I should see "User deleted"
