<?php

defined('_JEXEC') or die;
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');


class com_Manifest2MDInstallerScript
{
    /**
     * The title of the component (printed on installation and uninstallation messages)
     *
     * @var string
     */
    protected $componentTitle = 'Manifest2MD';

    /**
     * The component's name
     *
     * @var   string
     */
    protected $componentName = 'com_manifest2md';

    /**
     * The minimum PHP version required to install this extension
     *
     * @var   string
     */
    protected $minimumPHPVersion = '5.3.10';

    /**
     * The minimum Joomla! version required to install this extension
     *
     * @var   string
     */
    protected $minimumJoomlaVersion = '3.4';
    protected $release;

    /**
     * Post-installation message definitions for Joomla! 3.2 or later.
     *
     * This array contains the message definitions for the Post-installation Messages component added in Joomla! 3.2 and
     * later versions. Each element is also a hashed array. For the keys used in these message definitions please
     * @see F0FUtilsInstallscript::addPostInstallationMessage
     *
     * @var array
     */
    protected $postInstallationMessages = [];

    /**
     * @var array Obsolete files and folders to remove from the AllEvents oldest releases
     */
    private $AllEventsRemoveFiles = [];

    /**
     * com_Manifest2MDInstallerScript::install()
     *
     * @param mixed $parent
     */
    function install($parent)
    {
        echo '<p>' . JText::_('COM_MANIFEST2MD_UNINSTALL') . '</p>';
    }

    /**
     * com_Manifest2MDInstallerScript::uninstall()
     *
     * @param mixed $parent
     */
    function uninstall($parent)
    {
        echo '<p>' . JText::_('COM_MANIFEST2MD_UNINSTALL') . '</p>';
    }

    /**
     * com_Manifest2MDInstallerScript::update()
     *
     * @param mixed $parent
     */
    function update($parent)
    {
        echo '<p>' . JText::_('COM_MANIFEST2MD_UPDATE') . '</p>';
    }

    /**
     * com_AllEventsInstallerScript::preflight()
     * method to run before an install/update/uninstall method
     *
     * @param mixed $type
     * @param mixed $parent
     * @return void
     */
    function preflight($type, $parent)
    {

    }

    /**
     * com_AllEventsInstallerScript::getParam()
     *
     * @param mixed $name
     * @return
     */
    function getParam($name)
    {
        $db = JFactory::getDbo();
        $db->setQuery('SELECT manifest_cache FROM `#__extensions` WHERE name = "allevents"');
        $manifest = json_decode($db->loadResult(), true);
        return $manifest[$name];
    }

    /**
     * com_AllEventsInstallerScript::postflight()
     *
     * @param mixed $type
     * @param mixed $parent
     * @throws Exception
     */
    function postflight($type, $parent)
    {
        $db = JFactory::getDbo();

        //$sqlorder = new array();
        $sqlorder[] = ["ALTER TABLE `#__manifest2md_extensions` CHANGE `category` `catid` INT(11) NOT NULL;", "extensions.catid", false];

        //version 3.4
        $elements = [];
        foreach ($sqlorder as $key => $value) {
            try {
                $db->setQuery($value[0]);
                $db->execute();
                if (strpos($value[0], 'CHANGE') == false) {
                    if (!isset($elements[$value[1]])) {
                        // echo '<div>' . $value[1] . ' : ' . JText::_('COM_ALLEVENTS_CREATED') . '</div>';
                        $elements[$value[1]] = true;
                    }
                }
            } catch (Exception $e) {
                if ($value[2]) {
                    JFactory::getApplication()->enqueueMessage('Error ' . $e->getMessage() . ' on ' . $value[1], 'error');
                }
                // echo '<div>' . $e . ' : ' . JText::_('COM_ALLEVENTS_EXISTS') . '</div>';
            }
        }

        // Add post-installation messages on Joomla! 3.2 and later
        $this->_applyPostInstallationMessages();
    }

    /**
     * Applies the post-installation messages for Joomla! 3.2 or later
     *
     * @return void
     */
    protected
    function _applyPostInstallationMessages()
    {
        // Make sure it's Joomla! 3.2.0 or later
        if (!version_compare(JVERSION, '3.2.0', 'ge')) {
            return;
        }

        // Make sure there are post-installation messages
        if (empty($this->postInstallationMessages)) {
            return;
        }

        // Get the extension ID for our component
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('extension_id')->from('#__extensions')->where($db->qn('element') . ' = ' . $db->q($this->componentName));
        $db->setQuery($query);

        try {
            $ids = $db->loadColumn();
        } catch (Exception $exc) {
            return;
        }

        if (empty($ids)) {
            return;
        }

        $extension_id = array_shift($ids);

        foreach ($this->postInstallationMessages as $message) {
            $message['extension_id'] = $extension_id;
            $this->addPostInstallationMessage($message);
        }
    }

    /**
     * sets parameter values in the component's row of the extension table
     */

    /**
     * Adds or updates a post-installation message (PIM) definition for Joomla! 3.2 or later. You can use this in your
     * post-installation script using this code:
     *
     * The $options array contains the following mandatory keys:
     *
     * extension_id        The numeric ID of the extension this message is for (see the #__extensions table)
     *
     * type                One of message, link or action. Their meaning is:
     *                    message        Informative message. The user can dismiss it.
     *                    link        The action button links to a URL. The URL is defined in the action parameter.
     *                  action      A PHP action takes place when the action button is clicked. You need to specify the
     *                              action_file (RAD path to the PHP file) and action (PHP function name) keys. See
     *                              below for more information.
     *
     * title_key        The JText language key for the title of this PIM
     *                    Example: COM_FOOBAR_POSTINSTALL_MESSAGEONE_TITLE
     *
     * description_key    The JText language key for the main body (description) of this PIM
     *                    Example: COM_FOOBAR_POSTINSTALL_MESSAGEONE_DESC
     *
     * action_key        The JText language key for the action button. Ignored and not required when type=message
     *                    Example: COM_FOOBAR_POSTINSTALL_MESSAGEONE_ACTION
     *
     * language_extension    The extension name which holds the language keys used above. For example, com_foobar,
     *                    mod_something, plg_system_whatever, tpl_mytemplate
     *
     * language_client_id   Should we load the front-end (0) or back-end (1) language keys?
     *
     * version_introduced   Which was the version of your extension where this message appeared for the first time?
     *                        Example: 3.2.1
     *
     * enabled              Must be 1 for this message to be enabled. If you omit it, it defaults to 1.
     *
     * condition_file        The RAD path to a PHP file containing a PHP function which determines whether this message
     *                        should be shown to the user. @see F0FTemplateUtils::parsePath() for RAD path format. Joomla!
     *                        will include this file before calling the condition_method.
     *                      Example:   admin://components/com_foobar/helpers/postinstall.php
     *
     * condition_method     The name of a PHP function which will be used to determine whether to show this message to
     *                      the user. This must be a simple PHP user function (not a class method, static method etc)
     *                        which returns true to show the message and false to hide it. This function is defined in the
     *                        condition_file.
     *                        Example: com_foobar_postinstall_messageone_condition
     *
     * When type=message no additional keys are required.
     *
     * When type=link the following additional keys are required:
     *
     * action                The URL which will open when the user clicks on the PIM's action button
     *                        Example:    index.php?option=com_foobar&view=tools&task=installSampleData
     *
     * Then type=action the following additional keys are required:
     *
     * action_file            The RAD path to a PHP file containing a PHP function which performs the action of this PIM.
     *
     * @see                   F0FTemplateUtils::parsePath() for RAD path format. Joomla! will include this file
     *                        before calling the function defined in the action key below.
     *                        Example:   admin://components/com_foobar/helpers/postinstall.php
     *
     * action                The name of a PHP function which will be used to run the action of this PIM. This must be a
     *                      simple PHP user function (not a class method, static method etc) which returns no result.
     *                        Example: com_foobar_postinstall_messageone_action
     *
     * @param array $options See description
     *
     * @return  void
     *
     * @throws Exception
     */
    protected
    function addPostInstallationMessage(array $options)
    {
        // Make sure there are options set
        if (!is_array($options)) {
            throw new Exception('Post-installation message definitions must be of type array', 500);
        }

        // Initialise array keys
        // Initialise array keys
        $defaultOptions = [
            'extension_id' => '',
            'type' => '',
            'title_key' => '',
            'description_key' => '',
            'action_key' => '',
            'language_extension' => '',
            'language_client_id' => '',
            'action_file' => '',
            'action' => '',
            'condition_file' => '',
            'condition_method' => '',
            'version_introduced' => '',
            'enabled' => '1',
        ];

        $options = array_merge($defaultOptions, $options);

        // Array normalisation. Removes array keys not belonging to a definition.
        $defaultKeys = array_keys($defaultOptions);
        $allKeys = array_keys($options);
        $extraKeys = array_diff($allKeys, $defaultKeys);

        if (!empty($extraKeys)) {
            foreach ($extraKeys as $key) {
                unset($options[$key]);
            }
        }

        // Normalisation of integer values
        $options['extension_id'] = (int)$options['extension_id'];
        $options['language_client_id'] = (int)$options['language_client_id'];
        $options['enabled'] = (int)$options['enabled'];

        // Normalisation of 0/1 values
        foreach (['language_client_id', 'enabled'] as $key) {
            $options[$key] = $options[$key] ? 1 : 0;
        }

        // Make sure there's an extension_id
        if (!(int)$options['extension_id']) {
            throw new Exception('Post-installation message definitions need an extension_id', 500);
        }

        // Make sure there's a valid type
        if (!in_array($options['type'], [
            'message',
            'link',
            'action'])
        ) {
            throw new Exception('Post-installation message definitions need to declare a type of message, link or action', 500);
        }

        // Make sure there's a title key
        if (empty($options['title_key'])) {
            throw new Exception('Post-installation message definitions need a title key', 500);
        }

        // Make sure there's a description key
        if (empty($options['description_key'])) {
            throw new Exception('Post-installation message definitions need a description key', 500);
        }

        // If the type is anything other than message you need an action key
        if (($options['type'] != 'message') && empty($options['action_key'])) {
            throw new Exception('Post-installation message definitions need an action key when they are of type "' . $options['type'] . '"', 500);
        }

        // You must specify the language extension
        if (empty($options['language_extension'])) {
            throw new Exception('Post-installation message definitions need to specify which extension contains their language keys', 500);
        }

        // The action file and method are only required for the "action" type
        if ($options['type'] == 'action') {
            if (empty($options['action_file'])) {
                throw new Exception('Post-installation message definitions need an action file when they are of type "action"', 500);
            }

            $file_path = F0FTemplateUtils::parsePath($options['action_file'], true);

            if (!@is_file($file_path)) {
                throw new Exception('The action file ' . $options['action_file'] . ' of your post-installation message definition does not exist', 500);
            }

            if (empty($options['action'])) {
                throw new Exception('Post-installation message definitions need an action (function name) when they are of type "action"', 500);
            }
        }

        if ($options['type'] == 'link') {
            if (empty($options['link'])) {
                throw new Exception('Post-installation message definitions need an action (URL) when they are of type "link"', 500);
            }
        }

        // The condition file and method are only required when the type is not "message"
        if ($options['type'] != 'message') {
            if (empty($options['condition_file'])) {
                throw new Exception('Post-installation message definitions need a condition file when they are of type "' . $options['type'] . '"', 500);
            }

            $file_path = F0FTemplateUtils::parsePath($options['condition_file'], true);

            if (!@is_file($file_path)) {
                throw new Exception('The condition file ' . $options['condition_file'] . ' of your post-installation message definition does not exist', 500);
            }

            if (empty($options['condition_method'])) {
                throw new Exception('Post-installation message definitions need a condition method (function name) when they are of type "' . $options['type'] . '"', 500);
            }
        }

        // Check if the definition exists
        $tableName = '#__postinstall_messages';

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)->select('*')->from($db->qn($tableName))->where($db->qn('extension_id') . ' = ' . $db->q($options['extension_id']))->where($db->qn('type') . ' = ' . $db->q($options['type']))->where($db->qn('title_key') . ' = ' . $db->q($options['title_key']));
        $existingRow = $db->setQuery($query)->loadAssoc();

        // Is the existing definition the same as the one we're trying to save (ignore the enabled flag)?
        if (!empty($existingRow)) {
            $same = true;

            foreach ($options as $k => $v) {
                if ($k == 'enabled') {
                    continue;
                }

                if ($existingRow[$k] != $v) {
                    $same = false;
                    break;
                }
            }

            // Trying to add the same row as the existing one; quit
            if ($same) {
                return;
            }

            // Otherwise it's not the same row. Remove the old row before insert a new one.
            $query = $db->getQuery(true)->delete($db->qn($tableName))->where($db->q('extension_id') . ' = ' . $db->q($options['extension_id']))->where($db->q('type') . ' = ' . $db->q($options['type']))->where($db->q('title_key') . ' = ' . $db->q($options['title_key']));
            $db->setQuery($query)->execute();
        }

        // Insert the new row
        $options = (object)$options;
        $db->insertObject($tableName, $options);
    }

    /**
     * com_AllEventsInstallerScript::setParams()
     *
     * @param mixed $param_array
     */
    function setParams($param_array)
    {
        if (count($param_array) > 0) {
            // read the existing component value(s)
            $db = JFactory::getDbo();
            $db->setQuery('SELECT params FROM `#__extensions` WHERE name = ' . $db->quote('allevents'));
            $params = json_decode($db->loadResult(), true);
            // Add the new variable(s) to the existing one(s)
            foreach ($param_array as $name => $value) {
                if (!isset($params[(string )$name])) {
                    $params[(string )$name] = (string )$value;
                } elseif (($params[(string )$name] == '') && ($params[(string )$name] <> 0)) {
                    $params[(string )$name] = (string )$value;
                }
            }
            // Store the combined new and existing values back as a JSON string
            $paramsString = json_encode($params);
            $db->setQuery('UPDATE `#__extensions` SET params = ' . $db->quote($paramsString) . ' WHERE name = ' . $db->quote('allevents'));
            $db->execute();
        }
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    private function _removeObsoleteFilesAndFolders()
    {
        // Remove files
        jimport('joomla.filesystem.file');
        if (!empty($this->AllEventsRemoveFiles['files'])) {
            foreach ($this->AllEventsRemoveFiles['files'] as $file) {
                $f = JPATH_ROOT . '/' . $file;
                if (!JFile::exists($f))
                    continue;
                JFile::delete($f);
            }
        }

        // Remove folders
        jimport('joomla.filesystem.file');
        if (!empty($this->AllEventsRemoveFiles['folders'])) {
            foreach ($this->AllEventsRemoveFiles['folders'] as $folder) {
                $f = JPATH_ROOT . '/' . $folder;
                if (!JFolder::exists($f))
                    continue;
                JFolder::delete($f);
            }
        }
    }
}
