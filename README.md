# craft-plugin-optinmail
A plugin that simplifies the implementation of opt-in process for any input forms.

This is a Plugin for developers, as you will need to provide your own template-files.

## Getting Started

TODO: These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

## Prerequisites

This plugin requires Craft CMS 3.0.0-beta.20 or later

## Installing

##### go to the project directory

```
cd /path/to/my-project.test
```

##### tell Composer to load the plugin
```
composer require mister-bk/opt-in-mail
```

##### tell Craft to install the plugin
```
./craft install/plugin opt-in-mail
```

## Setup

To get this plugin to work, you need to create or link your own template files.

##### 1. Settings
To do this, please open the Controll-Center setup page in the admin section of Craft:\
"Settings > Plug-ins > Opt In Mail"\
Set the path to all three template files. An example is provided in the example-folder of this plugin.
You can see how to access the variables provided in these views and see example implementations of the emails/sites   
##### 2. Qualified Field Names
To prevent database-injection you have to provide "qualified field names", which means, that you whitelist the fields you
want to accept in the form you want to connect with an opt-in-procedure.
Just add the names of your input fields under the corresponding formHandle in the "opt-in-mail.php file". You find this file under: "config/opt-in-mail.php"\
If that file does not exist, you can copy our example config file from "craft-plugin-optInMail/src/templates/opt-in-mail" to Craft's config folder and add your field names there.

##### 3. Hook up your form
When everything is set up you can connect your form to the plugin by adding a hidden input field to your form with the following format:\
```
<input name="action" type="hidden" value="opt-in-mail/form/save-form-data">
<input type="hidden" name="optInFormHandle" value="{{handle}}">
```
whereas {{handle}} is the formHandle you provided in the "opt-in-mail.php" config file in step 2

##### 4. Test your form
If Craft's mailing settings are set you can submit your form and see if you receive the opt-in-mail.

## Additional Info
This is one of our first Plug-ins for Craft 3, so we rely on your feedback.
If you have any suggestions or found any bugs, please contact as over our Github-Account : https://github.com/mister-bk or via
email: [mister-bk!](mailto:s.karst@mister-bk.de)
