<?php

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

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
    /**
     * AllEventsClassMD::MakeMDObject()
     *
     * @param string $extension
     * @return string
     */
    public static function MakeMDObject($extension = "com_allevents", $object = "event")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB', true);
        $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, 'en-GB', true);

        $get_xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/models/forms/' . $object . '.xml');
        $filename = JPATH_ROOT . '/documentation/docs/AllEvents/items/' . $object . '.md';
        $handle = fopen($filename, 'w');

        $sLine = '# AllEvents Object ' . $object;
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = ' ![](//documentation.allevents3.com/docs/.images/allevents-hauteur.png)';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = '![](https://img.shields.io/badge/AllEvents-v3.4-blue.svg) &nbsp; ![](https://img.shields.io/badge/licence-GNU--GPL-green.svg)<br/><br/>';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

        foreach ($get_xml->fieldset as $fieldset) {
            $sLine = '### ' . JText::_($fieldset['name']);
            fwrite($handle, $sLine . PHP_EOL);

            $sLine = ' | Option | Description | Type | Value |';
            fwrite($handle, $sLine . PHP_EOL);
            $sLine = ' | ------ | ----------- | ----- | ----- |';
            fwrite($handle, $sLine . PHP_EOL);

            foreach ($fieldset->field as $field) {
                // if (!isset($field['aeimage'])) {
                if ($field['type'] == 'AETitleImg') {
                    if ($field['aeicon'] != 'info-circle') {
                        $sLine = '## <i class="fa fa-' . JText::_($field['aeicon']) . '" aria-hidden="true"></i> ' . JText::_($field['label']) . ' ...';
                        fwrite($handle, $sLine . PHP_EOL);
                        $sLine = ' | Option | Description | Type | Value |';
                        fwrite($handle, $sLine . PHP_EOL);
                        $sLine = ' | ------ | ----------- | ----- | ----- |';
                        fwrite($handle, $sLine . PHP_EOL);
                    }
                    // } elseif ($field['type'] == 'rules') {
                    // $get_rules = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/access.xml');
                    // $sLine = '## ' . JText::_($field['label']) . ' ...';
                    // fwrite($handle, $sLine . PHP_EOL);
                    // $sLine = ' | Action |  Description |';
                    // fwrite($handle, $sLine . PHP_EOL);
                    // $sLine = ' | ------ | ----------- |';
                    // fwrite($handle, $sLine . PHP_EOL);
                    // foreach ($get_rules->section->action as $action) {
                    // $sLine = ' | ' . JText::_($action['title']) . ' | ' . JText::_($action['description']) . ' | ';
                    // fwrite($handle, $sLine . PHP_EOL);
                    // }
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
                    if (isset($field['default'])) {
                        $sLine = '| <i class="fa fa-check" style="color: #669900;"></i>&nbsp;' . (empty(JText::_($field['label'])) ? JText::_($field['name']) : JText::_($field['label'])) . ' | ' . JText::_($field['description']) . ' | ' . JText::_($field['type']) . ' | ' . $str . ' (default: `' . JText::_($field['default']) . '`)' . '|';
                    } else {
                        $sLine = '| <i class="fa fa-check" style="color: #669900;"></i>&nbsp;' . (empty(JText::_($field['label'])) ? JText::_($field['name']) : JText::_($field['label'])) . ' | ' . JText::_($field['description']) . ' | ' . JText::_($field['type']) . ' | ' . (empty($str) ? '&nbsp;' : $str) . '|';
                    }
                    fwrite($handle, $sLine . PHP_EOL);
                }
                // }
            }
        }
        fclose($handle);
        return (JPATH_ROOT . '/administrator/components/' . $extension . '/models/forms/' . $object . '.xml');
    }

    /**
     * AllEventsClassMD::MakeMDConfig()
     *
     * @param string $extension
     * @return string
     */
    public static function MakeMDConfig($extension = "com_allevents")
    {
        $lang = JFactory::getLanguage();
        $lang->load($extension, JPATH_ADMINISTRATOR, 'en-GB', true);
        $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, 'en-GB', true);

        $get_xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/config.xml');
        $filename = JPATH_ROOT . '/documentation/docs/AllEvents/config_' . $extension . '.md';
        $handle = fopen($filename, 'w');

        $sLine = '# AllEvents Component Configuration';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = '![](//documentation.allevents3.com/docs/.images/allevents-hauteur.png)';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = '![](https://img.shields.io/badge/AllEvents-v3.4-blue.svg) &nbsp; ![](https://img.shields.io/badge/licence-GNU--GPL-green.svg)<br/><br/>';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

        $sLine = 'Your personnal Joomla calendar!';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

        $sLine = 'The AllEvents component provides 18 customizable layouts for your Joomla site.';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

        $sLine = 'All parameters available via the "Configuration" button on the top right of the backend are listed in a single table.';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

        $sLine = 'The main difficulty of a component is the documentation, the main defect of a component is the documentation. Therefore, this documentation is generated automatically from the Joomla config.xml and action.xml configuration files. The big advantage: ** the documentation is always up to date **!';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

        foreach ($get_xml->fieldset as $fieldset) {
            foreach ($fieldset->field as $field) {
                if (!isset($field['aeimage'])) {
                    if ($field['type'] == 'AETitleImg') {
                        if ($field['aeicon'] != 'info-circle') {
                            $sLine = '## <i class="fa fa-' . JText::_($field['aeicon']) . '" aria-hidden="true"></i> ' . JText::_($field['label']) . ' ...';
                            fwrite($handle, $sLine . PHP_EOL);
                            $sLine = ' | Option | Description | Value |';
                            fwrite($handle, $sLine . PHP_EOL);
                            $sLine = ' | ------ | ----------- | ----- |';
                            fwrite($handle, $sLine . PHP_EOL);
                        }
                    } elseif ($field['type'] == 'rules') {
                        $get_rules = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/access.xml');
                        $sLine = '## ' . JText::_($field['label']) . ' ...';
                        fwrite($handle, $sLine . PHP_EOL);
                        $sLine = ' | Action |  Description |';
                        fwrite($handle, $sLine . PHP_EOL);
                        $sLine = ' | ------ | ----------- |';
                        fwrite($handle, $sLine . PHP_EOL);
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
                        if (isset($field['default'])) {
                            $sLine = '| <i class="fa fa-check" style="color: #669900;"></i>&nbsp;' . JText::_($field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . ' (default: `' . JText::_($field['default']) . '`)' . '|';
                        } else {
                            $sLine = '| <i class="fa fa-check" style="color: #669900;"></i>&nbsp;' . JText::_($field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . '|';
                        }
                        fwrite($handle, $sLine . PHP_EOL);
                    }
                }
            }
        }
        fclose($handle);
        return (JPATH_ROOT . '/administrator/components/' . $extension . '/config.xml');
    }

    /**
     * AllEventsClassMD::MakeMDExtension()
     *
     * @param string $extension
     * @param string $name
     * @return int
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public static function MakeMDExtension($extension = "com_allevents", $name = "allevents")
    {
        $get_xml = simplexml_load_file(JPATH_ROOT . '/administrator/components/' . $extension . '/' . $name . '.xml');
        $filename = JPATH_ROOT . '/documentation/docs/AllEvents/' . $extension . '.md';
        $handle = fopen($filename, 'w');

        $sLine = '# AllEvents Component';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = '![](//documentation.allevents3.com/docs/.images/allevents-hauteur.png)';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = '![](https://img.shields.io/badge/AllEvents-v3.4-blue.svg) &nbsp; ![](https://img.shields.io/badge/licence-GNU--GPL-green.svg)<br/><br/>';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = 'Your personnal Joomla calendar !';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = 'The AllEvents component provides 18 customizable layouts for your Joomla site.';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = '![](https://www.allevents3.com//images/formulaire/hh.png)';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = '***';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

        $sLine = '## Modules';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = '<div class="begin-examples"></div>';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        foreach ($get_xml->modules->module as $module) {
            $sLine = '### ' . $module['name'];
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            if ($module['aetype'] == 'Starter') {
                $sLine = '![](https://img.shields.io/badge/Version-starter-green.svg)<br/><br/>';
            } else {
                $sLine = '![](https://img.shields.io/badge/Version-premium-yellow.svg)<br/><br/>';
            }
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            $sLine = '<a class="btn btn-success" href="https://documentation.allevents3.com/">Discover</a>';
            $sLine .= '&nbsp;or [Live Demo](https://www.allevents3.com/en/demo/' . $module['module'] . ')';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        }
        $sLine = '<div class="end-examples"></div>';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

        $sLine = '## Plugins';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        $sLine = '<div class="begin-examples"></div>';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        foreach ($get_xml->plugins->plugin as $plugin) {
            $sLine = '### ' . $plugin['name'];
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            if ($plugin['aetype'] == 'Starter') {
                $sLine = '![](https://img.shields.io/badge/Version-starter-green.svg)<br/><br/>';
            } else {
                $sLine = '![](https://img.shields.io/badge/Version-premium-yellow.svg)<br/><br/>';
            }
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            $sLine = '<a class="btn btn-success" href="https://documentation.allevents3.com/">Discover</a>';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        }
        $sLine = '<div class="end-examples"></div>';
        fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
        fclose($handle);
        return (JPATH_ROOT . '/administrator/components/' . $extension . '/' . $name . '.xml');
    }

    /**
     * AllEventsClassMD::MakeMD()
     *
     * @param $extension
     * @param $path
     * @param string $subpath
     * @return int
     * @internal param mixed $entity
     * @internal param mixed $entities
     */
    public static function MakeMD($extension, $path, $subpath = "")
    {
        $lang = JFactory::getLanguage();
        //$lang->load('com_allevents', JPATH_SITE);
        $lang->load('com_allevents', JPATH_ADMINISTRATOR);
        $lang->load($extension, JPATH_SITE);
        $lang->load($extension);
        $lang->load($extension, JPATH_ADMINISTRATOR);
        if ($path == 'plugins') {
            $lang->load('plg_' . $subpath . '_' . $extension, JPATH_SITE);
            $lang->load('plg_' . $subpath . '_' . $extension);
            $lang->load('plg_' . $subpath . '_' . $extension, JPATH_ADMINISTRATOR);
        }

        $db = JFactory::getDbo();
        // $extension_version = "";
        $extension_date = "";
        $extension_author = "";
        $extension_authorEmail = "";
        $extension_name = "";
        $filename = "";
        $get_xml = null;
        $home = null;
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['name', 'manifest_cache']))
            ->from($db->quoteName('#__extensions'))
            ->where('element = ' . $db->quote('com_allevents'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $db->setQuery($query);

        $results = $db->loadObjectList();

        foreach ($results as $result) {
            $decode = json_decode($result->manifest_cache);
            // $extension_version = $decode->version;
            $extension_date = $decode->creationDate;
            $extension_author = $decode->author;
            $extension_authorEmail = $decode->authorEmail;
        }

        if ($path == 'modules') {
            $get_xml = simplexml_load_file(JPATH_ROOT . '/' . $path . '/' . $subpath . '/' . $extension . '/' . $extension . '.xml');
            $extension_name = $get_xml->name;
            if (empty($extension_name)) {
                $extension_name = $extension;
            }
            $filename = JPATH_ROOT . '/documentation/docs/AllEvents/' . $path . '/' . JText::_($extension_name) . '.md';
        } elseif ($path == 'plugins') {
            $get_xml = simplexml_load_file(JPATH_ROOT . '/' . $path . '/' . $subpath . '/' . $extension . '/' . $extension . '.xml');
            $extension_name = $get_xml->name;
            if (empty($extension_name)) {
                $extension_name = $extension;
            }
            $filename = JPATH_ROOT . '/documentation/docs/AllEvents/' . $path . '/' . JText::_($extension_name) . '.md';
        } elseif ($path == 'views') {
            $get_xml = simplexml_load_file(JPATH_ROOT . '/components/com_allevents/' . $path . '/' . $subpath . '/tmpl/' . $extension . '.xml');
            $extension_name = $get_xml->layout['title'];
            if (empty($extension_name)) {
                $extension_name = $path . '_' . $subpath;
            }
            $filename = JPATH_ROOT . '/documentation/docs/AllEvents/' . $path . '/' . JText::_($extension_name) . '.md';
            $home = $get_xml;
            $get_xml = $get_xml->state;
        }

        //if (!empty($get_xml->version)) {
        //    $extension_version = $get_xml->version;
        //}
        if (!empty($get_xml->creationDate)) {
            $extension_date = $get_xml->creationDate;
        }
        if (!empty($get_xml->author)) {
            $extension_author = $get_xml->author;
        }
        if (!empty($get_xml->authorEmail)) {
            $extension_authorEmail = $get_xml->authorEmail;
        }

        if ($path == 'views') {
            $aepremiumonly = 'false';
        } else {
            if (isset($get_xml->aepremiumonly)) {
                $aepremiumonly = $get_xml->aepremiumonly;
            } else {
                $aepremiumonly = 'true';
            }
        }
        if (!empty($filename)) {
            $handle = fopen($filename, 'w');
            // $sLine = '# ' . JText::_($extension_name) .' ('.JPATH_ROOT . '/' . $path . '/' . $subpath . '/' . $extension . '/' . $extension . '.xml)';
            $sLine = '# ' . JText::_($extension_name);
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            $sLine = '[![](//documentation.allevents3.com/docs/.images/allevents-hauteur.png)](http://documentation.allevents3.com/docs/AllEvents/com_allevents.html)';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            $sLine = '![](https://img.shields.io/badge/AllEvents-v3.4-blue.svg) &nbsp;![](https://img.shields.io/badge/licence-GNU--GPL-green.svg)<br/><br/>';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            if ($aepremiumonly == 'true') {
                $sLine = '![](https://img.shields.io/badge/AllEvents-Premium_only-yellow.svg)<br/>';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                $sLine = 'Thank you for downloading our product. If you have any questions that are beyond the scope of this help file, please feel free to email via our [user page contact form](https://www.allevents3.com/en/support). Thanks so much!';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            } else {
                $sLine = 'If you have any questions that are beyond the scope of this help file, please feel free to email via our [user page contact form](https://www.allevents3.com/en/support). Thanks so much!';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            }

            $sLine = '## Description';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            $description = "";
            if ($path == 'modules') {
                $description = $get_xml->description;
            } elseif ($path == 'plugins') {
                $description = $get_xml->description;
            } elseif ($path == 'views') {
                $description = trim($home->layout->message);
            }

            $healthy = ["<![CDATA[", "]]>"];
            $yummy = ["", ""];
            $description = str_replace($healthy, $yummy, $description);
            fwrite($handle, JText::_($description) . PHP_EOL . PHP_EOL);

            if ($path == 'modules') {
                $sLine = '![' . JText::_($extension_name) . '](//documentation.allevents3.com/docs/.images/' . $extension . '_sample.png)';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            } elseif ($path == 'plugins') {
                //null
            } elseif ($path == 'views') {
                //null
            }

            if (!empty($get_xml->aeunsupported)) {
                $sLine = '<div class="alert alert-warning"><i class="fa fa-exclamation-circle" aria-hidden="true"></i>' . JText::_($get_xml->aeunsupported) . '</div>';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            }
            if ($aepremiumonly == 'true') {
                $sLine = '**Why choose AllEvents Premium ?**';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                $sLine = '* Display your events friendly.';
                fwrite($handle, $sLine . PHP_EOL);
                $sLine = '* View events via  multiple layout.';
                fwrite($handle, $sLine . PHP_EOL);
                $sLine = '* User friendly interface.';
                fwrite($handle, $sLine . PHP_EOL);
                $sLine = '* Full layout or Compact layout.';
                fwrite($handle, $sLine . PHP_EOL);
                $sLine = '* ...';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                $sLine = '<a class="btn btn-success" href="https://www.allevents3.com/en/buy">Subscribe Now</a>';
                if ($path == 'modules') {
                    $sLine .= '&nbsp;or [Live Demo](https://www.allevents3.com/en/demo/' . $extension . ')';
                } else {
                    $sLine .= '&nbsp;or [Live Demo](https://www.allevents3.com/en/demo)';
                }
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            } else {
                if ($path == 'modules') {
                    $sLine = '[Live Demo](https://www.allevents3.com/en/demo/' . $extension . ')';
                    fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                } else {
                    $sLine = '[Live Demo](https://www.allevents3.com/en/demo)';
                    fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                }
            }

            if ($path == 'modules') {
                $sLine = '## Install the module';
            } elseif ($path == 'plugins') {
                $sLine = '## Install the plugin';
            } elseif ($path == 'views') {
                $sLine = '## Install the component';
            }
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            $sLine = '**Step 1:** ![' . JText::_($extension_name) . '](//documentation.allevents3.com/docs/.images/download.png) the extension to your local machine as a zip file package.';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            $sLine = '**Step 2:** From the backend of your Joomla site (administration) select **Extensions >> Manager**, then Click the <b>Browse</b> button and select the extension package on your local machine. Then click the **Upload & Install** button to install module.';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            if ($path == 'modules') {
                $sLine = '**Step 3:** Go to **Extensions >> Module**, find and click on **' . JText::_($extension_name) . '**. In module detail page, select position for module and pages in which it display. Then enable it.';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            } elseif ($path == 'plugins') {
                $sLine = '**Step 3:** Go to **Extensions >> Plugin**, find and click on **' . JText::_($extension_name) . '**. Then enable it.';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            } elseif ($path == 'views') {
                //null
            }

            if ($path == 'modules') {
                $sLine = '## Configure the module';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            } elseif ($path == 'plugins') {
                $sLine = '## Configure the plugin';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            } elseif ($path == 'views') {
                $sLine = '## Configure the view';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            }

            $sLine = 'There are many options for you to customize your extension :';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            if ($aepremiumonly == 'false') {
                $sLine = '<div class="alert alert-warning">';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                $sLine = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Some elements presented in this page are only available in premium version.';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                $sLine = '</div>';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            }

            if ($path == 'modules') {
                $sLine = '![Configure module](//documentation.allevents3.com/docs/.images/' . $extension . '_configuration.png)<br/>';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            } elseif ($path == 'plugins') {
                $sLine = '![Configure plugin](//documentation.allevents3.com/docs/.images/' . $subpath . '_' . $extension . '_configuration.png)<br/>';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            } elseif ($path == 'views') {
                $sLine = '![Configure view](//documentation.allevents3.com/docs/.images/' . $extension . '_configuration.png)<br/>';
                fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            }

            if ($path == 'views') {
                foreach ($home->state->fields->fieldset as $fieldset) {
                    // if (!empty($fieldset->field)) {
                    $sLine = '### ' . JText::_($fieldset['name']);
                    fwrite($handle, $sLine . PHP_EOL);

                    $sLine = ' | Option | Description | Value |';
                    fwrite($handle, $sLine . PHP_EOL);
                    $sLine = ' | ------ | ----------- | ----- |';
                    fwrite($handle, $sLine . PHP_EOL);

                    foreach ($fieldset->field as $field) {
                        if (!isset($field['aeimage'])) {
                            if ($field['type'] == 'AETitleImg') {
                                if ($field['aeicon'] != 'info-circle') {
                                    $sLine = '### <i class="fa fa-' . JText::_($field['aeicon']) . '" aria-hidden="true"></i> ' . JText::_($field['label']) . ' ...';
                                    fwrite($handle, $sLine . PHP_EOL);
                                    $sLine = ' | Option | Description | Value |';
                                    fwrite($handle, $sLine . PHP_EOL);
                                    $sLine = ' | ------ | ----------- | ----- |';
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
                                if ($field['name'] == 'task') {
                                    $sLine = '| <i class="fa fa-check" style="color: #669900;"></i>&nbsp;' . JText::_($field['name']) . ' | ' . JText::_($field['type']) . ' | `' . JText::_($field['default']) . '` |';
                                } else {
                                    if (!empty($field['default'])) {
                                        $sLine = '| <i class="fa fa-check" style="color: #669900;"></i>&nbsp;' . JText::_($field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . ' (default: `' . JText::_($field['default']) . '`)' . '|';
                                    } else {
                                        $sLine = '| <i class="fa fa-check" style="color: #669900;"></i>&nbsp;' . JText::_($field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . '|';
                                    }
                                }
                                fwrite($handle, $sLine . PHP_EOL);
                            }
                        }
                    }
                    // }
                }
            } else {
                foreach ($get_xml->config->fields->fieldset as $fieldset) {
                    // $sLine = '### '. JText::_($fieldset->name) ;
                    // if (!empty($fieldset->field)) {
                    $sLine = '### ' . JText::_($fieldset['name']);
                    fwrite($handle, $sLine . PHP_EOL);

                    $sLine = ' | Option | Description | Value |';
                    fwrite($handle, $sLine . PHP_EOL);
                    $sLine = ' | ------ | ----------- | ----- |';
                    fwrite($handle, $sLine . PHP_EOL);

                    foreach ($fieldset->field as $field) {
                        if ($field['type'] == 'AETitleImg') {
                            if ($field['aeicon'] != 'info-circle') {
                                $sLine = '### <i class="fa fa-' . JText::_($field['aeicon']) . '" aria-hidden="true"></i> ' . JText::_($field['label']) . ' ...';
                                fwrite($handle, $sLine . PHP_EOL);
                                $sLine = ' | Option | Description | Value |';
                                fwrite($handle, $sLine . PHP_EOL);
                                $sLine = ' | ------ | ----------- | ----- |';
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
                            // $sLine = '| ' . JText::_($field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . '|';
                            if (!empty($field['default'])) {
                                $sLine = '| <i class="fa fa-check" style="color: #669900;"></i>&nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . (($field['type'] != "hidden") ? $str : '') . (empty($field['default']) ? '' : '(default:`' . JText::_($field['default']) . '`)') . '|';
                            } else {
                                $sLine = '| <i class="fa fa-check" style="color: #669900;"></i>&nbsp;' . JText::_(empty($field['label']) ? $field['name'] : $field['label']) . ' | ' . JText::_($field['description']) . ' | ' . $str . '|';
                            }

                            fwrite($handle, $sLine . PHP_EOL);
                        }
                        // }
                    }
                }
            }
            $sLine = ' <br/>';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            if ($path != 'views') {
                if ($get_xml->aedisplayevents == 'true') {
                    $sLine = '## My events are not displayed';

                    fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                    $sLine = ' If you want to display the events on the ' . JText::_($extension_name) . ', you should make sure that:';
                    fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                    $sLine = ' ';
                    fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                    $sLine = '1. The module or plugin is published (obviously)';
                    fwrite($handle, $sLine . PHP_EOL);
                    $sLine = '2. The module assigned to a visible module position. There can be many module positions listed for you to select, but make sure that the module position you select is visible in front-page.';
                    fwrite($handle, $sLine . PHP_EOL);
                    $sLine = '3. The module is assigned to a menu. When you want to display a module on specific menus, you need to assign it to the menus.';
                    fwrite($handle, $sLine . PHP_EOL);
                    $sLine = '4. the filters selected have events (obviously).';
                    fwrite($handle, $sLine . PHP_EOL);
                    $sLine = '5. your entities filtred are published.';
                    fwrite($handle, $sLine . PHP_EOL);
                    $sLine = ' ';
                    fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
                }
            }
            $sLine = '## Frequently Asked Questions';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            if (empty($get_xml->aeunsupported)) {
                $sLine = ' No questions for the moment';
            } else {
                $sLine = JText::_($get_xml->aeunsupported);
            }
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            if ($path == 'modules') {
                $sLine = '## Uninstall the module';
            } elseif ($path == 'plugins') {
                $sLine = '## Uninstall the plugin';
            } elseif ($path == 'views') {
                $sLine = '## Uninstall the component';
            }
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            $sLine = '1. Login to Joomla backend.';
            fwrite($handle, $sLine . PHP_EOL);
            $sLine = '2. Click **Extensions >> Manager** in the top menu.';
            fwrite($handle, $sLine . PHP_EOL);
            $sLine = '3. Click **Manage** on the left, navigate on the extension and click the Uninstall button on top.';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            $sLine = '***';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);
            $sLine = ' Once again, thank you so much for downloading our product. As I said at the beginning, I\'d be glad to help you if you have any questions relating to this product. No guarantees, but I\'ll do my best to assist.';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            $sLine = '> ###### Created on *' . JText::_($extension_date) . '* by *' . JText::_($extension_author) . '* ([' . JText::_($extension_authorEmail) . '](mailto:' . JText::_($extension_authorEmail) . '))';
            fwrite($handle, $sLine . PHP_EOL . PHP_EOL);

            fclose($handle);

            // if ($path == 'views') {
            // return '<pre>'.print_r($home->state->fields->fieldset, true).'</pre>' ;
            // }
        }
        return $filename;
    }
}
