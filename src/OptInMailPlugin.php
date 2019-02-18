<?php

namespace misterbk\optInMail;

use Craft;
use craft\base\Plugin;
use craft\web\View;
use yii\base\Event;
use misterbk\optInMail\models\Settings;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\UrlManager;

class OptInMailPlugin extends Plugin
{
    public $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Event::on(
            View::class,
            //View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function(RegisterTemplateRootsEvent $event) {
                $event->roots['opt-in-mail'] = __DIR__ . '/templates';
            }
        );
//        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function(RegisterUrlRulesEvent $event) {
//            $event->rules['cocktails/new'] = ['template' => 'cocktails/_edit'];
//            $event->rules['cocktails/<widgetId:\d+>'] = 'cocktails/edit-cocktail';
//        });

        $this->setComponents([
            'optInFormHandle' => \misterbk\optInMail\services\HandleFormService::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @param array|BaseModel $values
     */
    public function setSettings(array $values)
    {
        // Merge in any values that are stored in craft/config/optinmail.php
        foreach ($this->getSettings() as $key => $value)
        {
            $configValue = $this->getSettings()->$key;

            if ($configValue !== null)
            {
                $values[$key] = $configValue;
            }
        }

        parent::setSettings($values);
    }

//	/**
//	 * @return array
//	 */
//	protected function defineSettings()
//	{
//		return array(
//			'send_opt_in' => array(AttributeType::Bool, 'default' => true, 'required' => true),
//			'opt_in_mail_template_path' => array(AttributeType::String, 'default' => Craft::t('app', 'path/to/template.twig'), 'required' => true),
//			'opt_in_success_recepient' => array(AttributeType::String, 'default' => Craft::t('app', 'example@mail.com'), 'required' => true),
//			'subject_opt_in_mail' => array(AttributeType::String, 'default' => Craft::t('app', 'subject'), 'required' => true),
//			'subject_success_mail' => array(AttributeType::String, 'default' => Craft::t('app', 'subject'), 'required' => true),
//			'success_page_template_path' => array(AttributeType::String, 'default' => Craft::t('app', 'path/to/template.twig'), 'required' => true),
//			'opt_in_confirmation_mail_template_path' => array(AttributeType::String, 'default' => Craft::t('app', 'path/to/template.twig'), 'required' => true),
//		);
//	}

    public function registerCpRoutes()
    {
        return array(
            'opt-in-mail/form' => array('action' => 'opt-in-mail/form/'),
        );
    }


    protected function settingsHtml(): string
    {
        //Craft::$app->getView()->setTemplateMode(View::TEMPLATE_MODE_CP);
        return Craft::$app->view->renderTemplate('opt-in-mail/settings', [
            'settings' => $this->getSettings()
        ]);
    }
}
