<?php

namespace misterbk\optInMail;
use craft\base\Model;
use craft\base\Plugin;
use craft\web\View;
use yii\base\Event;
use misterbk\optInMail\models\Settings;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\UrlManager;

class OptInMailPlugin extends Plugin
{
    public bool $hasCpSettings = true;

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

        $this->setComponents([
            'optInFormHandle' => \misterbk\optInMail\services\HandleFormService::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function createSettingsModel(): Model
    {
        return new Settings();
    }
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

    public function registerCpRoutes(): array
    {
        return array(
            'opt-in-mail/form' => array('action' => 'opt-in-mail/form/'),
        );
    }


    protected function settingsHtml(): string
    {
        return \Craft::$app->view->renderTemplate('opt-in-mail/settings', [
            'settings' => $this->getSettings()
        ]);
    }
}

