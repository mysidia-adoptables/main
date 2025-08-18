/* global CKEDITOR */

$(document).ready(function(){
    CKEDITOR.replaceAll(function(textarea, config){ 
        if(textarea.classList.contains("editor-basic")){
            config.customConfig = "config-basic.js";
            return true;
        }
        if(textarea.classList.contains("editor-standard")){
            config.customConfig = "config-standard.js";
            return true;   
        }
        if(textarea.classList.contains("editor-full")){
            config.customConfig = "config.js";
            return true;
        }
        return false;
    });
});