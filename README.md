# Project notes
This file explains the tricky parts of the code base.

## Profile AWS config based on country.
The users have different aws bucket config depending on the country they're from, to conform to the country's regulation.

The AWS configuration is returned to the app via api in the \App\Http\Resources\ProfileResource.php. The configuration is available in every profile object returned. 

The URLs saved in the database will be the full configured url based on the country.

*Note:* The bucket and regions in the config file should be the same as what needs to be configured in S3 console.

## Creating a profile.
By default, the newly created profile is set as the default profile. is_default is set to 1 by default in the database

## Filesystems and S3
The old compressed and non compressed videos are on separate buckets. 
The new videos will be uploaded in side the same bucket of the users

### Adding a new bucket for compliance

When a new bucket is created for compliance purposes, a disk should be created for it in filesystems.php, for the admin downloads and delete profile features not to break.

It should also be added in the aws-buckets.php config file to be sent to the app's frontend, for the user photos/videos to be uplaoded to the correct bucket.



