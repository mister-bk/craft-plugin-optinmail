<?php

namespace misterbk\optInMail\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * Path to the public directory.
     *
     * @var string
     */

    public $send_opt_in = null;
    public $opt_in_success_recepient = null;
    public $subject_opt_in_mail = null;
    public $subject_success_mail = null;
    public $opt_in_mail_template_path = null;
    public $success_page_template_path = null;
    public $opt_in_confirmation_mail_template_path = null;
    public $qualified_fieldnames = null;

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['send_opt_in'], 'boolean'],
        ];
    }
}

