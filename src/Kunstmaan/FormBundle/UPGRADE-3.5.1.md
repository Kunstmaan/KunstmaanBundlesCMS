# UPGRADE FROM 3.4 to 3.5

## FileFormSubmissionField changes

When using the FormBundle with the FileUploadPagePart, files with the same name were overridden. Therefore we have added two new fields to the FileFormSubmissionField, UUID and URL. By doing this, every file that is being uploaded will be placed into a unique folder. 

When updating from a previous version, be sure to update your database scheme.
