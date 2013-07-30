Feature: Manage SSH keys

    Scenario: I can see my SSH keys
        Given I am connected as "alice"
          And I am on "/profile/ssh-keys"
         Then I should see "Create a key"
          And I should see "Laptop key"
          And I should see "alice-key"

    Scenario: I can create a new SSH key
        Given I am connected as "alice"
          And user "alice" has no SSH key named "key creation"
         When I am on "/profile/ssh-keys"
          And I fill:
          | Title | key creation |
          | Content | alice-key-creation |
          And I click on "Save"
         Then I should see "SSH key created!"
          And I should see "key creation"

    Scenario: I can't create invalid SSH key
        Given I am connected as "alice"
         When I am on "/profile/ssh-keys"
          And I fill "Title" with "key creation"
          And I fill "Content" with:
          """
          Foobar
          Barbaz
          """
          And I click on "Save"
         Then I should see "No newline permitted"
