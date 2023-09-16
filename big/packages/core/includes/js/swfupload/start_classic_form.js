var swfu;
window.onload = function () {
    var settings= {
		// Backend settings
		upload_url: "form_uploadswf.php",
//
		file_post_name: "resume_file",
		// Flash file settings
		file_size_limit : "100 MB",
		file_types : "*.*",			// or you could use something like: "*.doc;*.wpd;*.pdf",
		file_types_description : "All Files",
		file_upload_limit : 100,
		file_queue_limit : 1,
		// Event handler settings
		swfupload_loaded_handler : swfUploadLoaded,
		file_dialog_start_handler: fileDialogStart,
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		//upload_start_handler : uploadStart,	// I could do some client/JavaScript validation here, but I don't need to.
		swfupload_preload_handler : preLoad,
		swfupload_load_failed_handler : loadFailed,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		// Button Settings
		button_image_url : "packages/core/includes/js/swfupload/images/XPButtonUploadText_61x22.png",
		button_placeholder_id : "spanButtonPlaceholder",
		button_width: 61,
		button_height: 22,
		// Flash Settings
		flash_url : "packages/core/includes/js/swfupload/swfupload.swf",
		flash9_url : "packages/core/includes/js/swfupload/swfupload_fp9.swf",
		custom_settings : {
			progress_target : "fsUploadProgress",
			upload_successful : false
		},
		// Debug settings
		debug: true
	}
    	swfu = new SWFUpload(settings);
};