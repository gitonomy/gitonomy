Feature: Register
    As an anonymous
    In order to start using Gitonomy
    I should be able to register to the application

    Background:
        Given administrator has enabled registration
        Given user "tomoto" does not exist
        Given user "existing" exists

    Scenario: I should be able to register

        Given I am on "/"
          And I click on "Register"
         Then I should see a title "Register"
          And I should see a register form

        Given I fill:
            | Username | tomoto |
            | Fullname | Tomoto Pomo |
            | Email | tomoto@pomo.com |
            | Timezone | Europe/Paris |
            | Password | haricoo |
            | Password confirmation | haricoo |
          And I click on "Register"
         Then I should see "You should have received a confirmation mail. Please read it to continue"
          And I should receive a mail with subject "Confirmation of your registration"
          And I should see "Welcome to you, Tomoto Pomo"

        Given I click on "Activate my account"
         Then I should see "Your account is now active, welcome to Gitonomy"
          And I should see navigation menu with my fullname "Tomoto Pomo"

    Scenario: I shouldn't be able to register with an e-mail already used

        Given I am on "/"
          And I click on "Register"
         Then I should see a title "Register"
          And I should see a register form

        Given I fill:
            | Username | tomoto |
            | Fullname | Tomoto Pomo |
            | Email | existing@example.org |
            | Timezone | Europe/Paris |
            | Password | haricoo |
            | Password confirmation | haricoo |
          And I click on "Register"
         Then I should see "Roger, we have a problem with form"
          And I should see "This e-mail cannot be used"
