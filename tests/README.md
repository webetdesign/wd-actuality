# Actuality cross-repository metadata regression

Run `php tests/doctrine-attributes.php /path/to/IMM` with the application entities and dependencies from the integration checkout. This is explicitly an IMM integration test, not a claim about every consuming application.

The test validates the five concrete entities, inherited fields/associations, multilingual KNP translations and locale uniqueness, picture ordering/cascades, Gedmo attributes, and publication callback without opening a database connection. It works with ORM 2.20 and ORM 3.3+. An optional second argument compares the ORM 2 complete metadata snapshot. Cross-major comparison of all application mappings is provided by IMM `tests/Doctrine/runtime-metadata.php`; it normalises only documented internal representations.

No DDL, production data, external delivery, or vendor changes are performed by this test. Full schema sync and HTTP persistence still require an already prepared compatible isolated database.
