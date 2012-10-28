Feature: User profile
    As a user
    In order to customize my experience on Gitonomy
    I should be able to configure things in my profile

    Background:
        Given user "user" exists
          And user "user" has SSH key named "key A", content "foobar"
          And user "user" has SSH key named "key B", content "barbaz"

    Scenario: I can change my preferences

        Given I am on a page with a menu
         When I open menu "Default fullname"
          And I click on "Preferences"
         Then I should see "Edit your informations"

         When I fill:
            | Fullname | My new name |
            | Timezone | Europe/Paris |
          And I click on "Save changes"
         Then I should see "Your changes has successfully been changed"
          And I should see menu "My new name"
          And I should see "Europe/Paris"

    Scenario: I can see my SSH keys
        Given I am on a page with a menu
         When I open menu "Default fullname"
          And I click on "SSH keys"
         Then I should see "Manage your keyring"
          And I should see "key A"
          And I should see "key B"
          And I should see 2 buttons to delete SSH keys

    Scenario: I can delete a SSH-key
