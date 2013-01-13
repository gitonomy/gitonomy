Feature: Project permissions
    As a user
    I can administration project permissions
    So I easily manage permissions on my repository

    Scenario: I need to have sufficient credentials to administrate project
        Given I am connected as "alice"
          And I am on "/projects/secret/permissions"
         Then I should not see "Delete Secret"

    Scenario: User can delete a role
        Given project "todelete" exists
          And user "alice" exists
          And user "alice" is "Lead developer" on "Todelete"
          And I am connected as "admin"
          And I am on "/projects/todelete/permissions"
         When I click on xpath "//a[contains(@data-confirm, "Yes, I want to delete Alice as Lead developer")]"
         Then I should see "Yes, I want to delete Alice as Lead developer"
         When I click on "Yes, I want to delete Alice as Lead developer"
         Then I should see "Role deleted"

    Scenario: User can delete a permission
        Given project "todelete" exists
          And I am connected as "admin"
          And I am on "/projects/todelete/permissions"
         When I click on xpath "//a[contains(@data-confirm, "Yes, I want to revoke git access to Lead developer")]"
         Then I should see "Yes, I want to revoke git access to Lead developer"
         When I click on "Yes, I want to revoke git access to Lead developer"
         Then I should see "Git access deleted"
