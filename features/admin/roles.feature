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

    Scenario: An administrator needs to authenticate to create a role
        Given I logout
          And I am on "/admin/roles/create"
         Then I should see "Login"
         When I fill:
            | Username | admin |
            | Password | admin |
          And I click on "Login"
        Then I should see "Permissions"

    Scenario: I need to have sufficient credentials to administrate roles
        Given I logout
          And I am on "/admin/roles"
         Then I should see "Login"
         When I fill:
            | Username | alice |
            | Password | alice |
          And I click on "Login"
        Then I should not see "Master of the application"

    Scenario: I need to have sufficient credentials to create a role
        Given I logout
          And I am on "/admin/roles/create"
         Then I should see "Login"
         When I fill:
            | Username | alice |
            | Password | alice |
          And I click on "Login"
        Then I should not see "Master of the application"

    Scenario: I can create a new role
        Given role "Test role" does not exist
          And I am connected as "admin"
          And I am on "/admin/roles/create"
          And I fill:
            | Name        | Test role |
            | Slug        | test-role |
            | Description | Test role |
          And I click on "Save"
         Then I should see "Role created"

    Scenario: I cannot create a role if a role with same name exists
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
