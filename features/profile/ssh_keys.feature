Feature: Manage SSH keys
    As a user
    In order to customize my experience on Gitonomy
    I should be able to configure things in my profile

    Background:
        Given user "foobar" exists
          And user "foobar" has SSH key named "key A", content "foobar"
          And user "foobar" has SSH key named "key B", content "barbaz"

    Scenario: I can see my SSH keys
        Given I am connected as "foobar"
        Given I am on "/profile/ssh-keys"
         Then I should see "Create a key"
          And I should see "key A"
          And I should see "key B"
