# Actuality bundle

## Install

```json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/webetdesign/wd-actuality.git"
        }
    ],
}
```
> Register the repository into your `composer.json`.
```json
{
  "require": {
    "webetdesign/actuality-bundle": "^1.0",
  }
}
```
> Require the bundle.


```php
<?php

return [
    ...
    WebEtDesign\ActualityBundle\ActualityBundle::class => ['all' => true],
    ...
];
```
> Should be done by composer, register the bundle in `config/bundles.php` if not.

```yaml
# config/packages/wd_actuality.yaml
wd_actuality:
  class:
    user: App\Entity\User
    media: App\Entity\Media
  seo:
    actuality_route_name: 'actuality'  
    category_route_name: 'category_actuality'
    host: exemple.com
    scheme: http
    priority: 0.3
    changefreq: weekly
```

```yaml
# config/packages/sonata_media.yaml
sonata_media:
    contexts:
        actuality:
            providers:
                - sonata.media.provider.image

            formats:
                small: { width: 100 , quality: 70}
                big:   { width: 500 , quality: 70}
                # Add your own format according to your needs
```

```yaml
# config/packages/sonata_admin.yaml
sonata_admin:
    dashboard:
        groups:
            actuality:
                keep_open:        false
                label:            Actuality
                icon:             '<i class="fa fa-newspaper-o"></i>'
                items:
                    - wd_actuality.admin.actuality
                    - wd_actuality.admin.category
```

```yaml
# config/packages/fos_ck_editor.yaml
fos_ck_editor:
    configs:
        actuality:
            toolbar:
                - [Bold, Italic, Underline, -, Cut, Copy, Paste, PasteText, PasteFromWord, -, Undo, Redo, -, BackgroundColor, TextColor, -, NumberedList, BulletedList, -, Outdent, Indent, -, JustifyLeft, JustifyCenter, JustifyRight, JustifyBlock, -, Blockquote, -, Image, Link, Unlink, Table]
                - [Format, Maximize, Source]
            allowedContent: true
            filebrowserUploadMethod: form
            filebrowserBrowseRoute: admin_app_media_ckeditor_browser
            filebrowserImageBrowseRoute: admin_app_media_ckeditor_browser
            # Display images by default when clicking the image dialog browse button
            filebrowserImageBrowseRouteParameters:
              provider: sonata.media.provider.image
              context: actuality
            # Upload file as image when sending a file from the image dialog
            filebrowserImageUploadRoute: admin_app_media_ckeditor_upload
            filebrowserImageUploadRouteParameters:
              provider: sonata.media.provider.image
              context: actuality # Optional, to upload in a custom context
              format: big # Optional, media format or original size returned to editor
```


```yaml
# config/packages/twig.yaml
twig:
    form_themes:
        - '@SonataCore/Form/datepicker.html.twig'  
```

## Configuration

```yaml
web_et_design_cms:
  pages:
    actuality:
      label: Actualitée
      controller: WebEtDesign\ActualityBundle\Controller\ActualityController
      action: __invoke
      # If global vars is enable in project
      entityVars: WebEtDesign\ActualityBundle\Cms\ActualityVars
      params:
        category:
          requirement: ^[a-z0-9]+(?:-[a-z0-9]+)*$
          default: null
          entity: WebEtDesign\ActualityBundle\Entity\Category
          property: slug
        actuality:
          requirement: ^[a-z0-9]+(?:-[a-z0-9]+)*$
          default: null
          entity: WebEtDesign\ActualityBundle\Entity\Actuality
          property: slug
      template: pages/actuality.html.twig # TODO create your template
      contents:
        # TODO add contents according to your needs
        - { label: 'title', code: 'title', type: 'TEXT' }
    actualities:
      label: Actualitée listing
      controller: WebEtDesign\ActualityBundle\Controller\ActualityController
      action: list
      params:
        category:
          requirement: ^[a-z0-9]+(?:-[a-z0-9]+)*$
          default: null
          entity: WebEtDesign\ActualityBundle\Entity\Category
          property: slug
      template: pages/actualities.html.twig # TODO create your template
      contents:
        # TODO add contents according to your needs
        - { label: 'title', code: 'title', type: 'TEXT' }
```

> You can now create the pages in admin.


