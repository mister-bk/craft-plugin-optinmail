<?php
/**
 * Craft Mix
 *
 * @author    mister bk! GmbH
 * @copyright Copyright (c) 2017-2018 mister bk! GmbH
 * @link      https://www.mister-bk.de/
 */

namespace misterbk\optInMail\models;

use craft\base\Model;

class Settings extends Model
{
    /**
     * Path to the public directory.
     *
     * @var string
     */
    public $send_opt_in = true;

//    /**
//     * Path to the asset directory.
//     *
//     * @var string
//     */
//    public $assetPath = 'assets';


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['send_opt_in'], 'required'],
            [['send_opt_in'], 'bool'],
        ];
    }
}
