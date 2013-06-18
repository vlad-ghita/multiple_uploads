# Multiple Uploads

This extension adds multiple file upload facilities to Symphony backend.

It depends on this fork of [SelectBox Link Plus](https://github.com/vlad-ghita/selectbox_link_field_plus).


## Features

- UI for multiple file upload on section index (drag & drop).
- UI for multiple file upload on entry single (drag & drop).

## Usage

Take a section called `Images` with `Title (Input)` and `Image (File upload)`.

To enable multiple file upload for this section, create a `Mapper` for this section.

A `Mapper` is a PHP Class located in `/extensions/multiple_uploads/lib/upload/`.

Mapper filename: class.`SECTION_HANDLE`uploadhandler.php
Mapper class name: `SECTION_NAME`UploadHandler

Example for `Images` section:

    filename: class.imagesuploadhandler.php
    class name: ImagesUploadHandler

Example for `Personal attachments` section:

    filename: class.personal_attachmentsuploadhandler.php
    class name: Personal_AttachmentsUploadHandler

There already is a mapper for the `Images` section. Modify it to suit your needs.

### Section index

For each section that has a mapper, on their index page will be added a button to upload multiple files. Use it.

### Entry single

This extension provides a field for multiple uploads.

Create a section `Events` with `Title (Input)` and `Images (SBL+)`. For `Images` field, uncheck the `Enable Create button` checkbox.

**After** creating the section, add a field called `Upload new images (Multiple uploads)` and under `Related View` select `Images`. Save section.

NB: SBL+ in conjunction with Multiple Uploads used this way are designed to sit under their own [Publish tab](http://symphonyextensions.com/extensions/publish_tabs/).

Visit a new entry in `Events` section and use the interface.
