Feature: Browse source

    Scenario: Can see source
     Given I am connected as "alice"
      When I am on "/projects/foobar/tree/master"
      Then I should see "modify image"

    Scenario: Can see an image
     Given I am connected as "alice"
      When I am on "/projects/foobar/tree-history/master/image.jpg"
      Then I should see "add an image"

