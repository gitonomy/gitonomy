Feature: Forgot your password

    Scenario: I can reinitialize my password
        Given I am on "/"
         When I click on "Forgot your password?"
          And I fill:
            | Email | alice@example.org |
          And I click on "Send a mail with a link"
         Then I should see "We send a mail with instructions. Read it, and click, now!"
          And 1 mail should be sent
         When I open mail to "alice@example.org"
         Then I should see "requested to change the password" in mail
         When I click on "/forgot-password/" in mail
         Then I should see "Change your password"
         When I fill:
            | Password       | new |
            | Password again | new |
          And I click on "Change your password"
         Then I should see "successfully been changed"
         When I fill:
            | Username | alice |
            | Password | new |
          And I click on "Login"
         Then I should see "Projects"
         When I am on "/profile/password"
          And I fill:
            | Current password | new |
            | Password         | alice |
            | Confirm password | alice |
          And I click on "Reset password"
         Then I should see "conscientiously saved"

  Scenario: I can reinitialize my password twice or more
        Given I am on "/"
         When I click on "Forgot your password?"
          And I fill:
            | Email | alice@example.org |
          And I click on "Send a mail with a link"
         Then I should see "We send a mail with instructions. Read it, and click, now!"
          And 1 mail should be sent
         When I open mail to "alice@example.org"
         Then I should see "requested to change the password" in mail
         When I click on "/forgot-password/" in mail
         Then I should see "Change your password"
         When I fill:
            | Password       | new |
            | Password again | new |
          And I click on "Change your password"
         Then I should see "successfully been changed"
         When I purge mails
          And I click on "Forgot your password?"
          And I fill:
            | Email | alice@example.org |
          And I click on "Send a mail with a link"
         Then I should see "We send a mail with instructions. Read it, and click, now!"
          And 1 mail should be sent
         When I open mail to "alice@example.org"
         Then I should see "requested to change the password" in mail
         When I click on "/forgot-password/" in mail
         Then I should see "Change your password"
         When I fill:
            | Password       | new2 |
            | Password again | new2 |
          And I click on "Change your password"
         Then I should see "successfully been changed"
         When I fill:
            | Username | alice |
            | Password | new2 |
          And I click on "Login"
         Then I should see "Projects"
         When I am on "/profile/password"
          And I fill:
            | Current password | new2 |
            | Password         | alice |
            | Confirm password | alice |
          And I click on "Reset password"
         Then I should see "conscientiously saved"
