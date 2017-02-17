Feature: Get completion list for constants (class, namespace, global, defined)

    Scenario: Class constants
        Given there is a file with:
        """
        <?php
        class Test {
            const VAL_ONE = 0;
            const VAL_TWO = "asdf";
        }

        """
        When I type "Test::" on the 6 line
        And I ask for completion
        Then I should get:
            | Name    |
            | VAL_ONE |
            | VAL_TWO |
            | class   |

    Scenario: Namespace constants
        Given there is a file with:
        """
        <?php
        namespace Test;

        const VAL_ONE = 0;
        const VAL_TWO = "asdf";



        """
        When I type "\Test\V" on the 6 line
        And I ask for completion
        Then I should get:
            | Menu    |
            | VAL_ONE |
            | VAL_TWO |
        When I type "V" on the 7 line
        And I ask for completion
        Then I should get:
            | Menu    |
            | VAL_ONE |
            | VAL_TWO |

    Scenario: Defined constants
        Given there is a file with:
        """
        <?php
        define('VAL_ONE', 0);
        define('VAL_TWO', 'asdf');


        """
        When I type "V" on the 5 line
        And I ask for completion
        Then I should get:
            | Menu    |
            | VAL_ONE |
            | VAL_TWO |

    Scenario: Root namespace constants
        Given there is a file with:
        """
        <?php
        const VAL_ONE = 0;
        const VAL_TWO = "asdf";

        """
        When I type "V" on the 4 line
        And I ask for completion
        Then I should get:
            | Menu    |
            | VAL_ONE |
            | VAL_TWO |
