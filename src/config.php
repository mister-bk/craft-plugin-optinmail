<?php

namespace Craft;

$send_opt_in = Craft::$app->getRequest->post('send_opt_in');
$send_opt_in = craft()->security->validateData($send_opt_in);
$success_page_template_path = craft()->request->getPost('success_page_template_path');
$success_page_template_path = craft()->security->validateData($success_page_template_path);
$opt_in_confirmation_mail_template_path = craft()->request->getPost('opt_in_confirmation_mail_template_path');
$opt_in_confirmation_mail_template_path = craft()->security->validateData($opt_in_confirmation_mail_template_path);
$opt_in_mail_template_path = craft()->request->getPost('opt_in_mail_template_path');
$opt_in_mail_template_path = craft()->security->validateData($opt_in_mail_template_path);
return array(
    '*' => array(
        'send_opt_in' => ($send_opt_in ? $send_opt_in === 'true' : null),  //change null value to the desired value, this will override the admin-site settings
        'success_page_template_path' => ($success_page_template_path ?: null),  //change null value to the desired value, this will override the admin-site settings
        'opt_in_confirmation_mail_template_path' => ($opt_in_confirmation_mail_template_path ?: null),  //change null value to the desired value, this will override the admin-site settings
        'opt_in_mail_template_path' => ($opt_in_mail_template_path ?: null),  //change null value to the desired value, this will override the admin-site settings
        'opt_in_success_recepient' => ($opt_in_success_recepient ?: null),  //change null value to the desired value, this will override the admin-site settings
        'subject_opt_in_mail' => ($subject_opt_in_mail ?: null),  //change null value to the desired value, this will override the admin-site settings
        'subject_success_mail' => ($subject_success_mail ?: null),  //change null value to the desired value, this will override the admin-site settings
        'qualified_fieldnames' => array(
            'CRAFT_CSRF_TOKEN',
            'action',
            'subject',
            'courseTitle',
            'courseDate',
            'courseDuration',
            'coursePrice',
            'firstname',
            'lastname',
            'address',
            'plz',
            'ort',
            'phone',
            'email',
            'birthday',
            'iban',
            'bic',
            'bank',
            'bankAccountOwner',
            'nameKid',
            'birthdayKid',
            'notice',
            'optInFormHandle',
            'postcode',
            'location',
            'privacy',
            'agb'
        ),
    ),
);