Feature: Registration administration

    Scenario: I can disable registration
        Given I am on "/logout"
         Then I should see "Register"
        Given I am connected as "admin"
          And I am on "/admin/config"
         When I fill "Registration enabled?" with "0"
          And I click on "Save configuration"
          And I am on "/logout"
         Then I should see "Login"
          And I should not see "Register"
         Then I am connected as "admin"
          And I am on "/admin/config"
          And I fill "Registration enabled?" with "1"
          And I click on "Save configuration"
