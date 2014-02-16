Feature: Login

    Scenario: Inactive user can't login
        Given I am on "/"
         When I fill:
            | Username | inactive |
            | Password | inactive |
         And I click on "Login"
        Then I should see "Bad credentials"

    Scenario: Remember me cookie works

        # First, verify behavior without "remember me"
        Given I am on "/"
         When I fill:
            | Username | alice |
            | Password | alice |
         And I click on "Login"
        When I delete cookie "PHPSESSID"
        Then I should see "Projects"
         And I refresh
        Then I should not see "Projects"

        # Second, with "remember me"
        Given I am on "/"
         When I fill:
            | Username | alice |
            | Password | alice |
         And I click on "id=_remember_me"
         And I click on "Login"
        When I delete cookie "PHPSESSID"
        Then I should see "Projects"
         And I refresh
        Then I should see "Projects"


