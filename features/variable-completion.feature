Feature: Variables Completion
    As a user
    I want to have all variables in my completion list

    Scenario: Getting normal variable assignments
        Given there is a file with:
        """
        <?php
        $a = 1;
        $b = array();

        """
        When I type "$" on the 4 line
        And I ask for completion
        Then I should get:
            | Menu |
            | a    |
            | b    |

    Scenario: Getting list assign variables
        Given there is a file with:
        """
        <?php
        list($a, $b,) = split($xx);

        """
        When I type "$" on the 3 line
        And I ask for completion
        Then I should get:
            | Menu |
            | a    |
            | b    |
