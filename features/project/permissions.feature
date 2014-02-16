Feature: Project permissions
    As a user
    I can administration project permissions
    So I easily manage permissions on my repository

    Scenario: I need to have sufficient credentials to administrate project
        Given I am connected as "alice"
          And I am on "/projects/secret/permissions"
         Then I should not see "Delete Secret"

    Scenario: An anonymous cannot access permissions
        Given I am on "/projects/secret/permissions"
         Then I should not see "Delete Secret"

    Scenario: User deletes a role
        Given project "todelete" exists
          And user "alice" is "Lead developer" on "Todelete"
          And I am connected as "admin"
          And I am on "/projects/todelete/permissions"
         When I click on "xpath=//a[contains(@data-confirm, ""Yes, I want to delete Alice as Lead developer"")]"
         Then I should see "Yes, I want to delete Alice as Lead developer"
         When I click on "Yes, I want to delete Alice as Lead developer"
         Then I should see "Role deleted"

    Scenario: User creates a role
        Given project "todelete" exists
          And I am connected as "admin"
          And I am on "/projects/todelete/permissions"
         When I fill:
          | id=project_role_user | Alice |
          | id=project_role_role | Lead developer |
          And I click on "Create role"
         Then I should see "Role created"

    Scenario: User deletes a permission
        Given project "todelete" exists
          And I am connected as "admin"
          And I am on "/projects/todelete/permissions"
         When I click on "xpath=//a[contains(@data-confirm, ""Yes, I want to revoke git access to Lead developer"")]"
         Then I should see "Yes, I want to revoke git access to Lead developer"
         When I click on "Yes, I want to revoke git access to Lead developer"
         Then I should see "Git access deleted"

    Scenario: User creates a git access
        Given project "todelete" exists
          And I am connected as "admin"
          And I am on "/projects/todelete/permissions"
         When I fill:
          | id=project_git_access_role      | Lead developer |
          | id=project_git_access_reference | * |
          And I click on "Create access"
         Then I should see "Git access created"
         When I click on "xpath=//a[contains(@data-confirm, ""Yes, I want to revoke git access to Lead developer on"")]"
         Then I should see "Yes, I want to revoke git access to Lead developer"
         When I click on "Yes, I want to revoke git access to Lead developer"
         Then I should see "Git access deleted"
