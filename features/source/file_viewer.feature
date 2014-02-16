Feature: View files with HTML

    Scenario: Images are visible
        Given I am connected as "alice"
          And I am on "/projects/foobar/tree/master/image.jpg"
         Then I should see 1 "css=img.blob-image"

    Scenario: Text is visible
        Given I am connected as "alice"
          And I am on "/projects/foobar/tree/new-feature/README.md"
         Then I should see "Foo Bar project"
