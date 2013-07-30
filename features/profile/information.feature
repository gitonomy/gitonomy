Feature: Edit profile information
    As a user
    I can configure things in my profile
    In order to customize my experience on Gitonomy

    Scenario: I change my fullname
        Given I am connected as "alice"
          And I am on "/profile"
         Then I should see "User profile"

         When I fill:
            | Fullname | My new name |
          And I click on "Save information"
         Then I should see "Your information have been changed"

         When I fill:
            | Fullname | Alice |
          And I click on "Save information"
         Then I should see "Your information have been changed"

    Scenario: I can't change to a wrong username
        Given I am connected as "alice"
          And I am on "/profile"
         When I fill:
          | Username | foo bar |
          And I click on "Save information"
         Then I should see "Only letters, numbers, -, _"

    Scenario: I activate a new mail
        Given user "alice" has an inactive email "alice-new@example.org"
          And I am connected as "alice"

         When I am on "/profile"
         Then I should see "Emails"
         When I click on button with tooltip "Send an activation email to alice-new@example.org"
         Then I should see "Activation mail sent"

          And 1 mail should be sent
         When I open mail with subject "You need to activate an email"
         Then I should see "You registered a new email" in mail
         When I click on "Activate email alice-new@example.org" in mail
         Then I should see "Email active"

         # On refresh, token should not be valid anymore
         When I refresh
         Then I should not see "Email active"
         When I am on "/profile"
         Then I should not see a button with tooltip "Send an activation email to alice-new@example.org"

    Scenario: I cannot add a mail if this mail is already present
        Given user "bob" has an email "bob@example.org"
          And I am connected as "alice"
         When I am on "/profile"
          And I fill "id=profile_email_email" with "bob@example.org"
          And I click on "Create"
         Then I should see "This value is already used."

    Scenario: I can add a mail
        Given user "bob" has no email "bob-add@example.org"
          And I am connected as "bob"
         When I am on "/profile"
          And I fill "id=profile_email_email" with "bob-add@example.org"
          And I click on "Create"
         Then I should see "New email was created"

    Scenario: I can delete a mail
        Given user "bob" has an email "bob-delete@example.org"
          And I am connected as "bob"
         When I am on "/profile"
          And I click on button with tooltip "Delete email bob-delete@example.org"
          And I click on button with tooltip "Delete email bob-delete@example.org"
         Then I should see "Email deleted"
