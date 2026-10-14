# Actuality cross-repository metadata regression

Run `php tests/doctrine-attributes.php /path/to/IMM` with the application entities and dependencies from the integration checkout. This is explicitly an IMM integration test, not a claim about every consuming application.

The test validates the five concrete entities, inherited fields/associations, multilingual KNP translations and locale uniqueness, picture ordering/cascades, Gedmo attributes, and publication callback without opening a database connection. It works with ORM 2.20 and ORM 3.3+. An optional second argument compares the ORM 2 complete metadata snapshot. Cross-major comparison of all application mappings is provided by IMM `tests/Doctrine/runtime-metadata.php`; it normalises only documented internal representations.

No DDL, production data, external delivery, or vendor changes are performed by this test. Full schema sync and HTTP persistence still require an already prepared compatible isolated database.

## A2lix TranslationForm 4 regression

Run `php tests/a2lix-4-translation-forms.php /path/to/IMM` against an IMM checkout installed with `a2lix/translation-form-bundle` ^4 (PHP 8.4+). It checks that the admin forms use `enabled_locales` and a locale-keyed getter, that both locales submit through `TranslationsFormsType` for the title, category and content translation types, that the locale is stored through `__set`, and that the concrete translation entities implement `OneLocaleInterface`. The content case uses plain textareas instead of CKEditor, so it validates the data mapping, not the editor widgets. No database connection or DDL is used.
