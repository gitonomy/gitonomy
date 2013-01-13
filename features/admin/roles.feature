Feature: Administrate roles globally

    Scenario: Administrator authenticates to administrate roles
        Given I am connected as "admin"
          And I am on "/admin/roles"
         Then I should see "Master of the application"
          And I should see "New role"

    Scenario: Administrator creates a new role
        Given role "Test role" does not exist
          And I am connected as "admin"
          And I am on "/admin/roles/create"
          And I fill:
            | Name        | Test role |
            | Slug        | test-role |
            | Description | Test role |
          And I click on "Save"
         Then I should see "Role created"

    Scenario: Administrator cannot create a role if name is already used
        Given role "Lead developer" exists
          And I am connected as "admin"
          And I am on "/admin/roles/create"
          And I fill:
            | Name        | Lead developer |
            | Slug        | lead-developer |
            | Description | Lead developer |
          And I click on "Save"
         Then I should not see "Role created"
         Then I should see "This value is already used"

    Scenario: Administrator edits a role
        Given I am connected as "admin"
          And I am on "/admin/roles/1/edit"
          And I fill:
            | Slug        | foobar |
          And I click on "Save"
         Then I should see "Role updated"

        # Revert
        Given I am on "/admin/roles/1/edit"
          And I fill:
            | Slug        | admin |
          And I click on "Save"
         Then I should see "Role updated"

    Scenario: User cannot administrate roles
        Given I am connected as "alice"
          And I am on "/admin/roles"
        Then I should not see "Master of the application"

    Scenario: User cannot create a role
        Given I am connected as "alice"
          And I am on "/admin/roles/create"
        Then I should not see "Master of the application"

    Scenario: User cannot edit a role
        Given I am connected as "alice"
          And I am on "/admin/roles/1/edit"
        Then I should not see "Master of the application"

    Scenario: Anonymous cannot create a role
        Given I am connected as "alice"
          And I am on "/admin/roles/create"
        Then I should not see "Master of the application"

    Scenario: Anonymous cannot edit a role
        Given I logout
          And I am on "/admin/roles/1/edit"
        Then I should not see "Master of the application"

    Scenario: I need to authenticate to be able to administrate roles
        Given I logout
         When I am on "/admin/roles"
         Then I should see "Login"

    Scenario: I need to have sufficient credentials to administrate roles
        Given I am connected as "alice"
         When I am on "/admin/roles"
         Then I should not see "Master of the application"

    Scenario: As administrator, I can remove a role
        Given role "todelete" exists
          And I am connected as "admin"
          And I am on "/admin/roles"
         When I click on xpath "//a[contains(@data-confirm, "Yes, I want to delete Todelete")]"
         Then I should see "Yes, I want to delete Todelete"
         When I click on "Yes, I want to delete Todelete"
         Then I should see "Role deleted"
