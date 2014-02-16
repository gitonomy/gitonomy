Feature: Administrate users
    As an administrator
    I can execute user-related operations
    So I can manage efficiently my users

    Scenario: Administrator looks user list
        Given I am connected as "admin"
         When I am on "/admin/users"
         Then I should see "New user"
          And I should see "Alice"
          And I should see "Bob"
          And I should see "Visitor"
          And I should see "Admin"

    Scenario: Administrator creates a user
        Given user "foobar" does not exist
          And I am connected as "admin"
         When I am on "/admin/users/create"
         Then I should see "Create new user"
         When I fill:
            | Username | foobar |
            | Fullname | Foobar |
          And I click on "Save"
         Then I should see "User created"
         When I am on "/admin/users"
         Then I should see "Foobar"

    Scenario: Administrator edits a user
        Given I am connected as "admin"
          And user "testedit" exists
         When I am on "/admin/users/testedit/edit"
         Then I should see "Edit user testedit"
         When I fill:
            | Fullname | New fullname |
          And I click on "Save"
         Then I should see "User updated"
         When I fill:
            | Fullname | Testedit |
          And I click on "Save"
         Then I should see "User updated"

    Scenario: Administrator can add email of a user
        Given I am connected as "admin"
          And user "testedit" exists
         When I am on "/admin/users/testedit/edit"
         When I fill:
            | id=profile_email_email | foobarbaz@example.org |
          And I click on "Create email"
         Then I should see "Email foobarbaz@example.org created"
         When I am on "/admin/users/testedit/edit"
         Then I should see "foobarbaz@example.org"

    Scenario: Administrator can't add an existing email to a user
        Given I am connected as "admin"
         When I am on "/admin/users/alice/edit"
         When I fill:
            | id=profile_email_email | admin@example.org |
          And I click on "Create email"
         Then I should see "This value is already used"

    Scenario: Administrator can delete a user
        Given I am connected as "admin"
          And user "todelete" exists
         When I am on "/admin/users"
          And I click on button with tooltip "Delete user Todelete"
          And I click on "Yes, I want to delete Todelete"
         Then I should see "User deleted"
         When I am on "/admin/users"
         Then I should not see "Todelete"

    Scenario: Administrator can activate and disactivate a mail
        Given I am connected as "admin"
          And I am on "/admin/users/alice/edit"
         Then I should see "Edit user alice"
         When I click on button with tooltip "Activate email derpina@example.org"
         Then I should see "Email derpina@example.org activated"
         When I click on button with tooltip "Set derpina@example.org as default email"
         Then I should see "Email derpina@example.org set as default"
         When I click on button with tooltip "Disactivate email derpina@example.org"
         Then I should see "Email derpina@example.org disactivated"
         When I click on button with tooltip "Set alice@example.org as default email"
         Then I should see "Email alice@example.org set as default"


    Scenario: Administrator can delete a mail
        Given I am connected as "admin"
          And user "alice" has an email "alice.todelete@example.org"
          And I am on "/admin/users/alice/edit"
         Then I should see "alice.todelete@example.org"
         When I click on button with tooltip "Delete email alice.todelete@example.org"
         Then I should see "Email alice.todelete@example.org deleted"
         When I refresh
         Then I should not see "alice.todelete@example.org"

    Scenario: User cannot look user list
        Given I am connected as "alice"
         When I am on "/admin/users"
         Then I should not see "New user"

    Scenario: User cannot create user
        Given I am connected as "alice"
         When I am on "/admin/users"
         Then I should not see "New user"

    Scenario: User cannot edit user
        Given I am connected as "alice"
         When I am on "/admin/users/alice/edit"
         Then I should not see "Edit user alice"
