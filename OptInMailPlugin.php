<?php
namespace Craft;

class OptInMailPlugin extends BasePlugin
{
	/**
	 * @return mixed
	 */
	public function getName()
	{
		return Craft::t('Opt In Mail');
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return '0.1.0';
	}

	public function getSchemaVersion()
	{
		return '0.1.0';
	}

	/**
	 * @return string
	 */
	public function getDeveloper()
	{
		return 'mister bk! GmbH';
	}

	/**
	 * @return string
	 */
	public function getDeveloperUrl()
	{
		return 'https://www.mister-bk.de';
	}

	/**
	 * @return string
	 */
	public function getPluginUrl()
	{
		return '';
	}

	/**
	 * @return string
	 */
	public function getDocumentationUrl()
	{
		return '';
	}

	/**
	 * @return string
	 */
	public function getReleaseFeedUrl()
	{
		return '';
	}

	/**
	 * @return mixed
	 */
	public function getSettingsHtml()
	{
		return craft()->templates->render('optinmail/_settings', array(
			'settings' => $this->getSettings()
		));
	}

	/**
	 * @param array|BaseModel $values
	 */
	public function setSettings($values)
	{
		if (!$values)
		{
			$values = array();
		}

		if (is_array($values))
		{
			// Merge in any values that are stored in craft/config/optinmail.php
			foreach ($this->getSettings() as $key => $value)
			{
				$configValue = craft()->config->get($key, 'optinmail');

				if ($configValue !== null)
				{
					$values[$key] = $configValue;
				}
			}
		}

		parent::setSettings($values);
	}

	/**
	 * @return array
	 */
	protected function defineSettings()
	{
		return array(
			'send_opt_in' => array(AttributeType::Bool, 'default' => true, 'required' => true),
			'opt_in_mail_template_path' => array(AttributeType::String, 'default' => Craft::t('path/to/template.twig'), 'required' => true),
			'opt_in_success_recepient' => array(AttributeType::String, 'default' => Craft::t('example@mail.com'), 'required' => true),
			'subject_opt_in_mail' => array(AttributeType::String, 'default' => Craft::t('subject'), 'required' => true),
			'subject_success_mail' => array(AttributeType::String, 'default' => Craft::t('subject'), 'required' => true),
			'success_page_template_path' => array(AttributeType::String, 'default' => Craft::t('path/to/template.twig'), 'required' => true),
			'opt_in_confirmation_mail_template_path' => array(AttributeType::String, 'default' => Craft::t('path/to/template.twig'), 'required' => true),
		);
	}
}
