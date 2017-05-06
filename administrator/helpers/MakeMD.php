<?php

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * AllEventsClassMD
 *
 * @version %%ae3.version%%
 * @package %%ae3.package%%
 * @copyright %%ae3.copyright%%
 * @license %%ae3.license%%
 * @author %%ae3.author%%
 * @access public
 */
class AllEventsClassMD
{
    protected $root = null;

    /**
     * AllEventsClassMD::MakeMDViews(
     *
     * @param string $extension
     * @param string $category
     * @param string $identifier
     * @return string
     */
    public function MakeMDViews($category = "AllEvents", $extension = "com_allevents", $identifier = "site")
    {
        $msg = "";
        //$list = array();

        //get the list of all .xml files in the folder
        if ($identifier == "site") {
            $base_dir = JPATH_ROOT . '/components/' . $extension . '/views/';
        } else {
            $base_dir = JPATH_ROOT . '/administrator/components/' . $extension . '/views/';
        }

        foreach (scandir($base_dir) as $file) {
            if ($file == '.' || $file == '..') continue;
            $dir = $base_dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($dir)) {
                $msg .= '<br/>, ' . self::MakeMDView($extension, $file, $category);
            }
        }

        return $msg;
    }

    /**
     * AllEventsClassMD::MakeMD()
     *
     * @param $extension
     * @param string $subpath
     * @param string $category
     * @return int
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public function MakeMDView($category = "AllEvents", $extension, $subpath = "")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB', true);
        $lang->load($extension, JPATH_SITE, 'en-GB', true);

        $db = JFactory::getDbo();
        $extension_date = "";
        $extension_author = "";
        $extension_authorEmail = "";
        //$extension_name = "";
        //$filename = "";
        $get_xml = null;
        $home = null;
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['name', 'manifest_cache']))
            ->from($db->quoteName('#__extensions'))
            ->where('element = ' . $db->quote($extension))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $db->setQuery($query);

        $results = $db->loadObjectList();

        foreach ($results as $result) {
            $decode = json_decode($result->manifest_cache);
            $extension_date = $decode->creationDate;
            $extension_author = $decode->author;
            $extension_authorEmail = $decode->authorEmail;
        }

        $get_xml = simplexml_load_file(JPATH_ROOT . '\\components\\' . $extension . '\\views\\' . $subpath . '\\tmpl\\default.xml');
        $extension_name = $get_xml->layout['title'];
        if (empty($extension_name)) {
            $extension_name = 'views_' . $subpath;
        }
        $filename = $this->root . $category . '/views/' . JText::_($extension_name) . '.md';

        if (!empty($get_xml->creationDate)) {
            $extension_date = $get_xml->creationDate;
        }
        if (!empty($get_xml->author)) {
            $extension_author = $get_xml->author;
        }
        if (!empty($get_xml->authorEmail)) {
            $extension_authorEmail = $get_xml->authorEmail;
        }

        if (!empty($filename)) {
            $handle = fopen($filename, 'w');
            fwrite($handle, '# ' . JText::_($extension_name) . PHP_EOL);
            fwrite($handle, '## Description' . PHP_EOL);

            $description = trim($get_xml->layout->message);
            $healthy = ["<![CDATA[", "]]>"];
            $yummy = ["", ""];
            $description = str_replace($healthy, $yummy, $description);
            fwrite($handle, JText::_($description) . PHP_EOL);

            fwrite($handle, '## Install the component' . PHP_EOL);
            fwrite($handle, '**Step 1:** Download the extension to your local machine as a zip file package.' . PHP_EOL);
            fwrite($handle, '**Step 2:** From the backend of your Joomla site (administration) select **Extensions >> Manager**, then Click the <b>Browse</b> button and select the extension package on your local machine. Then click the **Upload & Install** button to install module.' . PHP_EOL);

            fwrite($handle, '## Configure the view' . PHP_EOL);
            fwrite($handle, 'There are many options for you to customize your extension :' . PHP_EOL);

            foreach ($get_xml->state->fields as $fieldset) {
                if (($fieldset['name'] == 'request') || ($fieldset['name'] == 'params')) {
                    fwrite($handle, '### ' . JText::_($fieldset['name']) . PHP_EOL);
                    fwrite($handle, '| Option | Description | Value |' . PHP_EOL);
                    fwrite($handle, '| ------ | ----------- | ----- |' . PHP_EOL);

                    foreach ($fieldset->fieldset->field as $field) {
                        $first = true;
                        $str = "";
                        foreach ($field->option as $option) {
                            if ($first) {
                                $str .= '`' . JText::_($option) . '`';
                                $first = false;
                            } else {
                                $str .= ', `' . JText::_($option) . '`';
                            }
                        }
                        // $sLine = '| ' . JText::_($field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . '|';
                        if (!empty($field['default'])) {
                            $sLine = '| &nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . (($field['type'] != "hidden") ? $str : '') . (empty($field['default']) ? '' : '(default:`' . JText::_($field['default']) . '`)') . '|';
                        } else {
                            $sLine = '| &nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . '|';
                        }

                        fwrite($handle, $sLine . PHP_EOL);
                    }
                }
            }

            fwrite($handle, '## Frequently Asked Questions' . PHP_EOL);
            fwrite($handle, 'No questions for the moment' . PHP_EOL);

            fwrite($handle, 'Once again, thank you so much for downloading our product. As I said at the beginning, I\'d be glad to help you if you have any questions relating to this product. No guarantees, but I\'ll do my best to assist.' . PHP_EOL);

            $sLine = '> ###### Created on *' . JText::_($extension_date) . '* by *' . JText::_($extension_author) . '* ([' . JText::_($extension_authorEmail) . '](mailto:' . JText::_($extension_authorEmail) . '))';
            fwrite($handle, $sLine . PHP_EOL);

            fclose($handle);
        }
        return $filename;
    }

    /**
     * AllEventsClassMD::MakeMDObjects(
     *
     * @param string $extension
     * @param string $category
     * @param string $identifier
     * @return string
     */
    public function MakeMDObjects($category = "AllEvents", $extension = "com_allevents", $identifier = "site")
    {
        $msg = "";
        $list = array();

        //get the list of all .xml files in the folder
        if ($identifier == "site") {
            $original = JFolder::files(JPATH_ROOT . '/components/' . $extension . '/models/forms/', '.xml');
        } elseif ($identifier == "administrator") {
            $original = JFolder::files(JPATH_ROOT . '/administrator/components/' . $extension . '/models/forms/', '.xml');
        }

        //create the final list that contains name of files
        $total = count($original);
        $index = 0;
        for ($i = 0; $i < $total; $i++) {
            //separate name&extension si besoin ...
            //remove the file extension and the dot from the filename
            $list[$index]['name'] = substr($original[$i], 0, -1 * (1 + strlen(JFile::getExt($original[$i]))));
            //add the extension
            // $list[$index]['ext'] = JFile::getExt($original[$i]);
            $msg .= '<br/>, ' . self::MakeMDObject($extension, $list[$index]['name'], $category, $identifier);
            $index++;
        }
        return $msg;
    }

    /**
     * AllEventsClassMD::MakeMDObject()
     *
     * @param string $extension
     * @param string $object
     * @param string $category
     * @param string $identifier
     * @return string
     */
    public function MakeMDObject($category = "AllEvents", $extension = "com_allevents", $object = "event", $identifier = "site")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB', true);
        $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, 'en-GB', true);

        if (JFile::exists(JPATH_ROOT . '/components/' . $extension . '/models/forms/' . $object . '.xml')) {
            if ($identifier == "site") {
                $get_xml = simplexml_load_file(JPATH_ROOT . '/components/' . $extension . '/models/forms/' . $object . '.xml');
            } elseif ($identifier == "administrator") {
                $get_xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/models/forms/' . $object . '.xml');
            }
            $filename = $this->root . $category . '/items/' . $identifier . '_' . $object . '.md';
            $handle = fopen($filename, 'w');

            fwrite($handle, '# ' . $category . ' Object ' . $object . PHP_EOL);

            foreach ($get_xml->fieldset as $fieldset) {
                fwrite($handle, '### ' . JText::_($fieldset['name']) . PHP_EOL);
                fwrite($handle, '| Option | Description | Type | Value |' . PHP_EOL);
                fwrite($handle, '| ------ | ----------- | ---- | ----- |' . PHP_EOL);

                foreach ($fieldset->field as $field) {
                    $first = true;
                    $str = "";
                    foreach ($field->option as $option) {
                        if ($first) {
                            $str .= '`' . JText::_($option) . '`';
                            $first = false;
                        } else {
                            $str .= ', `' . JText::_($option) . '`';
                        }
                    }
                    $default = (isset($field['default'])) ? ' (default: `' . JText::_($field['default']) . '`)' : '';
                    $sLine = '| &nbsp;' . (empty(JText::_($field['label'])) ? JText::_($field['name']) : JText::_($field['label'])) . ' | ' . JText::_($field['description']) . ' | ' . JText::_($field['type']) . ' | ' . $str . $default . '|';
                    fwrite($handle, $sLine . PHP_EOL);
                }
            }
            fclose($handle);
            return (JPATH_ROOT . '/administrator/components/' . $extension . '/models/forms/' . $object . '.xml');
        } else {
            return ('* NOT EXIST : ' . JPATH_ROOT . '/administrator/components/' . $extension . '/models/forms/' . $object . '.xml');
        }

    }

    /**
     * AllEventsClassMD::MakeMDComponent(
     *
     * @param string $extension
     * @param string $category
     * @param string $identifier
     * @return string
     */
    public function MakeMDComponent($category = "AllEvents", $extension = "com_allevents", $identifier = "site")
    {
        $msg = "";
        $list = array();

        //get the list of all .xml files in the folder
        $original = JFolder::files(JPATH_ROOT . '/administrator/components/' . $extension . '/', '.xml');

        //create the final list that contains name of files
        $total = count($original);
        $index = 0;
        for ($i = 0; $i < $total; $i++) {
            //separate name&extension si besoin ...
            //remove the file extension and the dot from the filename
            $list[$index]['name'] = substr($original[$i], 0, -1 * (1 + strlen(JFile::getExt($original[$i]))));
            //add the extension
            // $list[$index]['ext'] = JFile::getExt($original[$i]);
            $msg .= '<br/>, ' . self::MakeMDExtension($extension, $category, $list[$index]['name']);
            $index++;
        }
        return $msg;
    }

    /**
     * AllEventsClassMD::MakeMDExtension()
     *
     * @param string $extension
     * @param string $category
     * @param string $name
     * @return int
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public function MakeMDExtension($category = "AllEvents", $extension = "com_allevents", $name = 'allevents')
    {
        $get_xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/' . $name . '.xml');
        $filename = $this->root . $category . '/' . $extension . '.md';
        $handle = fopen($filename, 'w');

        fwrite($handle, '# ' . $extension . ' Component' . PHP_EOL);

        fwrite($handle, '## Modules' . PHP_EOL);
        foreach ($get_xml->modules->module as $module) {
            fwrite($handle, '### ' . $module['name'] . PHP_EOL);
        }

        fwrite($handle, '## Plugins' . PHP_EOL);
        foreach ($get_xml->plugins->plugin as $plugin) {
            fwrite($handle, '### ' . $plugin['name'] . PHP_EOL);
        }
        fclose($handle);
        return (JPATH_ROOT . '/administrator/components/' . $extension . '/' . $name . '.xml');
    }

    /**
     * AllEventsClassMD::MakeMD()
     *
     * @param string $extension
     * @param string $category
     * @return int
     * @internal param string $subpath
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public function MakeMDModule($category = "AllEvents", $extension = "mod_aesearch")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB', true);
        $lang->load($extension, JPATH_SITE, 'en-GB', true);

        $db = JFactory::getDbo();
        $extension_date = "";
        $extension_author = "";
        $extension_authorEmail = "";
        //$extension_name = "";
        // $filename = "";
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['name', 'manifest_cache']))
            ->from($db->quoteName('#__extensions'))
            ->where('element = ' . $db->quote($extension))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $db->setQuery($query);

        $results = $db->loadObjectList();

        foreach ($results as $result) {
            $decode = json_decode($result->manifest_cache);
            $extension_date = $decode->creationDate;
            $extension_author = $decode->author;
            $extension_authorEmail = $decode->authorEmail;
        }

        $get_xml = simplexml_load_file(JPATH_ROOT . '/modules/' . $extension . '/' . $extension . '.xml');
        $extension_name = empty($get_xml->name) ? $extension : $get_xml->name;
        $filename = $this->root . $category . '/modules/' . JText::_($extension_name) . '.md';

        if (!empty($get_xml->creationDate)) {
            $extension_date = $get_xml->creationDate;
        }
        if (!empty($get_xml->author)) {
            $extension_author = $get_xml->author;
        }
        if (!empty($get_xml->authorEmail)) {
            $extension_authorEmail = $get_xml->authorEmail;
        }
        $handle = fopen($filename, 'w');

        fwrite($handle, '# ' . JText::_($extension_name) . PHP_EOL);
        fwrite($handle, '## Description' . PHP_EOL);

        $healthy = ["<![CDATA[", "]]>"];
        $yummy = ["", ""];
        $description = str_replace($healthy, $yummy, $get_xml->description);
        fwrite($handle, JText::_($description) . PHP_EOL);

        fwrite($handle, '## Install the module' . PHP_EOL);
        fwrite($handle, '1.  Download the extension to your local machine as a zip file package.' . PHP_EOL);
        fwrite($handle, '2.  From the backend of your Joomla site (administration) select **Extensions >> Manager**, then Click the <b>Browse</b> button and select the extension package on your local machine. Then click the **Upload & Install** button to install module.' . PHP_EOL);
        fwrite($handle, '3.  Go to **Extensions >> Module**, find and click on **' . JText::_($extension_name) . '**. In module detail page, select position for module and pages in which it display. Then enable it.' . PHP_EOL);
        fwrite($handle, PHP_EOL);

        fwrite($handle, '## Configure the module' . PHP_EOL);
        fwrite($handle, 'There are many options for you to customize your extension :' . PHP_EOL);

        foreach ($get_xml->config->fields->fieldset as $fieldset) {
            fwrite($handle, '### ' . JText::_($fieldset['name']) . PHP_EOL);
            fwrite($handle, '| Option | Description | Value |' . PHP_EOL);
            fwrite($handle, '| ------ | ----------- | ----- |' . PHP_EOL);

            foreach ($fieldset->field as $field) {
                $first = true;
                $str = "";
                foreach ($field->option as $option) {
                    if ($first) {
                        $str .= '`' . JText::_($option) . '`';
                        $first = false;
                    } else {
                        $str .= ', `' . JText::_($option) . '`';
                    }
                }
                $str = ($field['type'] != 'hidden') ? $str : '';
                $default = (!empty($field['default'])) ? '(default:`' . JText::_($field['default']) . '`)' : '';
                $sLine = '| &nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . $default . '|';

                fwrite($handle, $sLine . PHP_EOL);
            }
        }

        fwrite($handle, '## Frequently Asked Questions' . PHP_EOL);
        fwrite($handle, 'No questions for the moment' . PHP_EOL);

        fwrite($handle, '## Uninstall the module' . PHP_EOL);
        fwrite($handle, '1. Login to Joomla backend.' . PHP_EOL);
        fwrite($handle, '2. Click **Extensions >> Manager** in the top menu.' . PHP_EOL);
        fwrite($handle, '3. Click **Manage** on the left, navigate on the extension and click the Uninstall button on top.' . PHP_EOL);
        fwrite($handle, PHP_EOL);

        fwrite($handle, ' Once again, thank you so much for downloading our product. As I said at the beginning, I\'d be glad to help you if you have any questions relating to this product. No guarantees, but I\'ll do my best to assist.' . PHP_EOL);

        $sLine = '> ###### Created on *' . JText::_($extension_date) . '* by *' . JText::_($extension_author) . '* ([' . JText::_($extension_authorEmail) . '](mailto:' . JText::_($extension_authorEmail) . '))';
        fwrite($handle, $sLine . PHP_EOL);

        fclose($handle);

        return $filename;
    }

    /**
     * AllEventsClassMD::MakeMDPlugin()
     *
     * @param string $extension
     * @param string $subpath
     * @param string $category
     * @return int
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public function MakeMDPlugin($category = "AllEvents", $extension = "", $subpath = "")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB', true);
        $lang->load($extension, JPATH_SITE, 'en-GB', true);
        $lang->load('plg_' . $subpath . '_' . $extension, JPATH_SITE, 'en-GB', true);
        $lang->load('plg_' . $subpath . '_' . $extension, JPATH_ADMINISTRATOR, 'en-GB', true);

        $db = JFactory::getDbo();
        $extension_date = "";
        $extension_author = "";
        $extension_authorEmail = "";
        //$extension_name = "";
        //$filename = "";
        $get_xml = null;
        $home = null;
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['name', 'manifest_cache']))
            ->from($db->quoteName('#__extensions'))
            ->where('element = ' . $db->quote($extension))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $db->setQuery($query);

        $results = $db->loadObjectList();

        foreach ($results as $result) {
            $decode = json_decode($result->manifest_cache);
            $extension_date = $decode->creationDate;
            $extension_author = $decode->author;
            $extension_authorEmail = $decode->authorEmail;
        }

        $get_xml = simplexml_load_file(JPATH_ROOT . '/plugins/' . $subpath . '/' . $extension . '/' . $extension . '.xml');

        $extension_name = $get_xml->name;
        if (empty($extension_name)) {
            $extension_name = $extension;
        }
        $filename = $this->root . $category . '/plugins/' . $subpath . '_' . JText::_($extension_name) . '.md';

        if (!empty($get_xml->creationDate)) {
            $extension_date = $get_xml->creationDate;
        }
        if (!empty($get_xml->author)) {
            $extension_author = $get_xml->author;
        }
        if (!empty($get_xml->authorEmail)) {
            $extension_authorEmail = $get_xml->authorEmail;
        }

        $handle = fopen($filename, 'w');
        fwrite($handle, '# ' . JText::_($extension_name) . PHP_EOL);

        fwrite($handle, '## Description' . PHP_EOL);
        $healthy = ["<![CDATA[", "]]>"];
        $yummy = ["", ""];
        $description = str_replace($healthy, $yummy, $get_xml->description);
        fwrite($handle, JText::_($description) . PHP_EOL);

        fwrite($handle, '## Install the plugin' . PHP_EOL);
        fwrite($handle, '1. Download the extension to your local machine as a zip file package.' . PHP_EOL);
        fwrite($handle, '2. From the backend of your Joomla site (administration) select **Extensions >> Manager**, then Click the <b>Browse</b> button and select the extension package on your local machine. Then click the **Upload & Install** button to install module.' . PHP_EOL);
        fwrite($handle, '3. Go to **Extensions >> Plugin**, find and click on **' . JText::_($extension_name) . '**. Then enable it.' . PHP_EOL);
        fwrite($handle, PHP_EOL);

        fwrite($handle, '## Configure the plugin' . PHP_EOL);
        fwrite($handle, 'There are many options for you to customize your extension :' . PHP_EOL);

        foreach ($get_xml->config->fields->fieldset as $fieldset) {
            fwrite($handle, '### ' . JText::_($fieldset['name']) . PHP_EOL);
            fwrite($handle, '| Option | Description | Value |' . PHP_EOL);
            fwrite($handle, '| ------ | ----------- | ----- |' . PHP_EOL);

            foreach ($fieldset->field as $field) {
                $first = true;
                $str = "";
                foreach ($field->option as $option) {
                    if ($first) {
                        $str .= '`' . JText::_($option) . '`';
                        $first = false;
                    } else {
                        $str .= ', `' . JText::_($option) . '`';
                    }
                }
                $str = ($field['type'] != 'hidden') ? $str : '';
                $default = (!empty($field['default'])) ? '(default:`' . JText::_($field['default']) . '`)' : '';
                $sLine = '| &nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . $default . '|';

                fwrite($handle, $sLine . PHP_EOL);
            }
        }

        fwrite($handle, '## Frequently Asked Questions' . PHP_EOL);
        fwrite($handle, 'No questions for the moment' . PHP_EOL);

        fwrite($handle, '## Uninstall the plugin' . PHP_EOL);
        fwrite($handle, '1. Login to Joomla backend.' . PHP_EOL);
        fwrite($handle, '2. Click **Extensions >> Manager** in the top menu.' . PHP_EOL);
        fwrite($handle, '3. Click **Manage** on the left, navigate on the extension and click the Uninstall button on top.' . PHP_EOL);
        fwrite($handle, PHP_EOL);

        fwrite($handle, ' Once again, thank you so much for downloading our product. As I said at the beginning, I\'d be glad to help you if you have any questions relating to this product. No guarantees, but I\'ll do my best to assist.' . PHP_EOL);

        $sLine = '> ###### Created on *' . JText::_($extension_date) . '* by *' . JText::_($extension_author) . '* ([' . JText::_($extension_authorEmail) . '](mailto:' . JText::_($extension_authorEmail) . '))';
        fwrite($handle, $sLine . PHP_EOL);

        fclose($handle);

        return $filename;
    }

    /**
     * AllEventsClassMD::MakeMDConfig()
     *
     * @param string $extension
     * @param string $category
     * @return string
     */
    public function MakeMDConfig($category = "AllEvents", $extension = "com_allevents")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB', true);
        $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, 'en-GB', true);

        $get_xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/config.xml');
        $filename = $this->root . $category . '/config_' . $extension . '.md';
        $handle = fopen($filename, 'w');

        fwrite($handle, '# Component Configuration' . PHP_EOL);
        fwrite($handle, '| Option | Description | Value |' . PHP_EOL);
        fwrite($handle, '| ------ | ----------- | ----- |' . PHP_EOL);

        foreach ($get_xml->fieldset->field as $field) {
            if ($field['type'] == 'rules') {
                $get_rules = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/access.xml');
                fwrite($handle, '## ' . JText::_($field['label']) . ' ...' . PHP_EOL);
                fwrite($handle, '| Action | Description |' . PHP_EOL);
                fwrite($handle, '| ------ | ----------- |' . PHP_EOL);
                foreach ($get_rules->section->action as $action) {
                    $sLine = ' | ' . JText::_($action['title']) . ' | ' . JText::_($action['description']) . ' | ';
                    fwrite($handle, $sLine . PHP_EOL);
                }
            } else {
                $first = true;
                $str = "";
                foreach ($field->option as $option) {
                    if ($first) {
                        $str .= '`' . JText::_($option) . '`';
                        $first = false;
                    } else {
                        $str .= ', `' . JText::_($option) . '`';
                    }
                }
                $default = (isset($field['default'])) ? ' (default: `' . JText::_($field['default']) . '`)' : '';
                $sLine = '| &nbsp;' . JText::_($field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . $default . '|';
                fwrite($handle, $sLine . PHP_EOL);
            }
        }
        fclose($handle);
        return (JPATH_ROOT . '/administrator/components/' . $extension . '/config.xml');
    }

    /**
     * @param string $category
     */
    public function CheckFolder($category = "AllEvents")
    {
        $folder[0][0] = 'root';
        $folder[0][1] = $this->root . $category;
        $folder[1][0] = 'items';
        $folder[1][1] = $this->root . $category . '/items/';
        $folder[2][0] = 'views';
        $folder[2][1] = $this->root . $category . '/views/';
        $folder[3][0] = 'plugins';
        $folder[3][1] = $this->root . $category . '/plugins/';
        $folder[4][0] = 'modules';
        $folder[4][1] = $this->root . $category . '/modules/';
        foreach ($folder as $key => $value) {
            if (!JFolder::exists($value[1])) {
                if (JFolder::create($value[1], 0755)) {
                    //
                } else {
                    //
                }
            } else // Folder exist
            {
                //
            }
        }
    }

    /**
     * @param string $url
     */
    function setRoot($url = JPATH_ROOT . '/documentation/docs/')
    {
        $this->root = $url;
        $this->root = rtrim($this->root, '/') . '/';
    }
}

