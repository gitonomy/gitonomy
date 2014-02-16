Feature: View diff on project

    Scenario: Image compare when an image is modified
        Given I am connected as "alice"
          And I am on "/projects/foobar/commits/e0ec50e2af75fa35485513f60b2e658e245227e9"
         Then I should see 2 "css=img.blob-image"
