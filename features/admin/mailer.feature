Feature: Manage mailer in application

    Scenario: As an administrator, I don't see meaningless options
        Given I am connected as "admin"
          And I am on "/admin/config"

         When I fill:
            | Transport | Disabled |
         Then I should not see "Host"
         Then I should not see "Encryption"
         Then I should not see "Username"
         Then I should not see "Name on sent mails"
         When I fill:
            | Transport | SMTP |
         Then I should see "Host"
          And I should see "Encryption"
         Then I should see "Username"
         Then I should see "Name on sent mails"
         When I fill:
            | Transport | Gmail |
         Then I should not see "Host"
          And I should not see "Encryption"
         Then I should see "Username"
         Then I should see "Name on sent mails"
