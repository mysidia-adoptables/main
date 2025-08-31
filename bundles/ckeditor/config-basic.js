/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function (config) {
  // Define changes to default configuration here.
  // For complete reference see:
  // https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html

  // The toolbar groups arrangement, optimized for a single toolbar row.
  config.toolbarGroups = [
    { name: "clipboard", groups: ["clipboard", "undo"] },
    { name: "basicstyles", groups: ["basicstyles"] },
    { name: "paragraph", groups: ["list", "indent"] },
    { name: "links" },
    { name: "about" },
  ];

  config.removeButtons = "PasteText,PasteFromWord,Maximize,ShowBlocks";
  // Dialog windows are also simplified.
  config.removeDialogTabs = "link:advanced";
};
